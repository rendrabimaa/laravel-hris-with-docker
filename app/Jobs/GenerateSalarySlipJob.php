<?php

namespace App\Jobs;

use App\Helpers\Terbilang;
use App\Models\SalaryItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;

class GenerateSalarySlipJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $tries = 3;

  public function __construct(protected SalaryItem $item) {}

  public function handle(): void
  {
    $this->item->update(['status' => 'processing']);
    $batch = $this->item->salaryBatch;

    $pdf = Pdf::loadView('slips.salary', [
      'item' => $this->item,
      'batch' => $batch,
      'terbilang' => Terbilang::rupiah($this->item->gaji_bersih)
    ]);

    $filename = "slip-gaji/{$batch->id}/{$this->item->nip}-{$this->sanitize($this->item->nama_lengkap)}.pdf";
    Storage::disk('local')->put($filename, $pdf->output());

    $this->item->update([
      'pdf_path' => $filename,
      'status' => 'generated'
    ]);

    if($batch->salaryItems()->where('status', '!=', 'generated')->doesntExist()) {
      $batch->update(['status' => 'completed']);
    }
  }

  private function sanitize(string $name): string
  {
    return preg_replace('/[^A-Za-z0-9]/', '-', $name);
  }

  public function failed(\Throwable $e): void
  {
    $this->item->update(['status' => 'failed']);
  }
}