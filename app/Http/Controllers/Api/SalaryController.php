<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\SalaryImport;
use App\Models\SalaryBatch;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Jobs\GenerateSalarySlipJob;
use Illuminate\Support\Facades\Storage;

use ZipArchive;

class SalaryController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        DB::beginTransaction();

        try {
            $batch = SalaryBatch::create([
                'batch_name'      => 'Batch ' . now()->format('Y-m-d H:i'),
                'company_name'    => 'PT MUDA JAYA KAYA RAYA',
                'periode' => '',
                'hrd_name' => '',
                'company_address' => 'Jl. Mastrip No 17 Kota Blitar, Kepanjen Kidul/Kepanjen Kidul, Jawa Timur',
                'status' => 'processing'
            ]);

            Excel::import(new SalaryImport($batch), $request->file('file'));

            $batch->update(['status' => 'completed']);

            DB::commit();

            return response()->json([
                'message' => 'Import data gaji berhasil diproses',
                'batch_id'    => $batch->id,
                'total_items' => $batch->salaryItems()->count(),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal import data gaji',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function process($batchId)
    {
        $batch = SalaryBatch::with('salaryItems')->findOrFail($batchId);

        if($batch->salaryItems->isEmpty()) {
            return response()->json(['message' => 'Batch ini tidak memiliki data karyawan'], 422);
        }

        $batch->update(['status' => 'processing']);

        foreach($batch->salaryItems as $item) {
            GenerateSalarySlipJob::dispatch($item);
        }

        return response()->json([
            'message' => 'Proses generate PDF dimulai',
            'total_item' => $batch->salaryItems->count()
        ]);
    }

    public function status($batchId)
    {
        $batch = SalaryBatch::with('salaryItems')->findOrFail($batchId);

        return response()->json([
            'batch_status' => $batch->status,
            'total' => $batch->salaryItems->count(),
            'generated' => $batch->salaryItems->where('status', 'generated')->count(),
            'failed' => $batch->salaryItems->where('status', 'failed')->count()
        ]);
    }

    public function downloadZip($batchId)
    {
        $batch = SalaryBatch::with('salaryItems')->findOrFail($batchId);

        $generatedItems = $batch->salaryItems->where('status', 'generated');

        if($generatedItems->isEmpty()) {
            return response()->json(['message' => 'Belum ada PDF yang siap didownload'], 422);
        }

        $zipDir = storage_path('app/temp');
        if(!is_dir($zipDir)) {
            mkdir($zipDir, 0755, true);
        }

        $zipFileName = "slip-gaji-{$batch->periode}-batch-{$batch->id}.zip";
        $zipPath = "${zipDir}/{$zipFileName}";

        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach($generatedItems as $item) {
            if($item->pdf_path && Storage::disk('local')->exists($item->pdf_path)) {
                $zip->addFile(
                    Storage::disk('local')->path($item->pdf_path),
                    basename($item->pdf_path)
                );
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }
}