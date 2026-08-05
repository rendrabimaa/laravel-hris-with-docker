<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalaryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'salary_batch_id',
        'nip',
        'nama_lengkap',
        'jabatan',
        'npwp',
        'gaji_pokok',
        'tunjangan_jabatan',
        'tunjangan_loyalitas',
        'uang_makan',
        'insentif',
        'kedisiplinan',
        'lembur',
        'bpjs_kesehatan',
        'bpjs_tk',
        'jml_pendapatan',
        'jml_potongan',
        'gaji_bersih',
        'pdf_path',
        'status',
    ];

    public function salaryBatch(): BelongsTo
    {
        return $this->belongsTo(SalaryBatch::class);
    }
}
