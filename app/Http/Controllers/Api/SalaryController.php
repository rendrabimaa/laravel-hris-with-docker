<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\SalaryImport;
use App\Models\SalaryBatch;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

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
                'company_address' => 'Jl. Mastrip No 17 Kota Blitar, Kepanjen Kidul/Kepanjen Kidul, Jawa Timur',
                'status' => 'processing'
            ]);

            Excel::import(new SalaryImport($batch), $request->file('file'));

            $batch->update(['status' => 'completed']);

            DB::commit();

            return response()->json([
                'message' => 'Import data gaji berhasil diproses',
                'batch_id'    => $batch->id,
                'periode'     => $batch->fresh()->periode,
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
}