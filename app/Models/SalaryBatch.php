<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryBatch extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'batch_name',
        'periode',
        'status',
        'hrd_name',
    ];

    public function salaryItems(): HasMany
    {
        return $this->hasMany(SalaryItem::class);
    }
}
