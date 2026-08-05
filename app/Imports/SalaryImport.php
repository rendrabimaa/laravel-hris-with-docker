<?php

namespace App\Imports;

use App\Models\SalaryItem;
use App\Models\SalaryBatch;
use Maatwebsite\Excel\Concerns\ToArray;

class SalaryImport implements ToArray
{
  protected SalaryBatch $batch;

  public function __construct(SalaryBatch $batch)
  {
    $this->batch = $batch;
  }

  public function array(array $rows)
  {

    $periode = $rows[0][3] ?? 'test';     
    $hrdName = $rows[1][3] ?? 'test';

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
        // $email = optional(Employee::where('nip', $noId)->first())->email;

        SalaryItem::create([
            'salary_batch_id'      => $this->batch->id,
            'nama_lengkap'                 => $row[1] ?? '',
            'nip'                => $noId,
            'jabatan'              => $row[3] ?? null,
            'npwp'                 => $row[4] ?? null,

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

    if(!empty($items)) {
        SalaryItem::insert($items);
    }
  }

  private function toNumber($value): float
  {
      if (is_numeric($value)) {
          return (float) $value;
      }

      // Membersihkan format Rp, titik/koma dari Excel string
      $clean = preg_replace('/[^\d.]/', '', str_replace(',', '.', $value));
      return (float) $clean;
  }

}