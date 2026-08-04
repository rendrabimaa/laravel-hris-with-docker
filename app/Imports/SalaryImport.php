<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\SalaryItem;
use App\Models\SalaryBatch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;

class SalaryImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
  protected array $failures = [];
  protected

  public function __construct(protected int $batchId) {}

  public function headingRow(): int
  {
    return 2;
  }

  public function array(array $rows)
  {
    $periode = $rows[0][3] ?? null;     
    $hrdName = $rows[1][3] ?? null;        
    $jumlahKaryawan = $rows[2][3] ?? null;

    $this->batch->update([
        'periode'  => $periode,
        'hrd_name' => $hrdName,
    ]);

    for ($i = 7; $i < count($rows); $i++) {
        $row = $rows[$i];

        // Skip baris kosong (misal nama kosong = akhir data)
        if (empty($row[1])) {
            continue;
        }

        $gajiPokok          = $this->toNumber($row[5] ?? 0);
        $tunjanganLoyalitas = $this->toNumber($row[6] ?? 0);
        $tunjanganJabatan   = $this->toNumber($row[7] ?? 0);
        $lembur             = $this->toNumber($row[8] ?? 0);
        $insentif           = $this->toNumber($row[9] ?? 0);
        $uangMakan          = $this->toNumber($row[10] ?? 0);
        $jmlPendapatan      = $this->toNumber($row[11] ?? 0);

        $bpjsKesehatan = $this->toNumber($row[12] ?? 0);
        $bpjsTk        = $this->toNumber($row[13] ?? 0);
        $kedisiplinan  = $this->toNumber($row[14] ?? 0);
        $jmlPotongan   = $this->toNumber($row[15] ?? 0);
        $gajiBersih    = $this->toNumber($row[16] ?? ($jmlPendapatan - $jmlPotongan));

        $noId = (string) ($row[2] ?? '');
        $email = optional(Employee::where('nip', $noId)->first())->email;

        SalaryItem::create([
            'salary_batch_id'      => $this->batch->id,
            'nama'                 => $row[1] ?? '',
            'no_id'                => $noId,
            'jabatan'              => $row[3] ?? null,
            'npwp'                 => $row[4] ?? null,
            'email'                => $email,

            'gaji_pokok'           => $gajiPokok,
            'tunjangan_loyalitas'  => $tunjanganLoyalitas,
            'tunjangan_jabatan'    => $tunjanganJabatan,
            'lembur'               => $lembur,
            'insentif'             => $insentif,
            'uang_makan'           => $uangMakan,
            'jml_pendapatan'       => $jmlPendapatan,

            'bpjs_kesehatan'       => $bpjsKesehatan,
            'bpjs_tk'              => $bpjsTk,
            'kedisiplinan'         => $kedisiplinan,
            'jml_potongan'         => $jmlPotongan,

            'gaji_bersih'          => $gajiBersih,
        ]);
    }
  }

  public function import(Request $request)
  {
      $request->validate(['file' => 'required|mimes:xlsx,xls']);

      $batch = SalaryBatch::create([
          'batch_name' => 'Batch ' . now()->format('Y-m-d H:i'),
          'company_name' => 'PT MUDA JAYA KAYA RAYA',
          'company_address' => 'Jl. Mastrip No 17 Kota Blitar, Kepanjen Kidul/Kepanjen Kidul, Jawa Timur',
      ]);

      Excel::import(new SalaryImport($batch), $request->file('file'));

      return response()->json([
          'message' => 'Import berhasil',
          'batch_id' => $batch->id,
          'periode' => $batch->fresh()->periode,
          'total_items' => $batch->items()->count(),
      ]);
  }

  

}