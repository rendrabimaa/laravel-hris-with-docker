{{-- resources/views/slips/salary.blade.php --}}
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; font-size: 12px; color: #000; }
    table { width: 100%; border-collapse: collapse; }

    .header-table td { border: none; vertical-align: top; padding: 0; }
    .company-name { font-size: 15px; font-weight: bold; }
    .slip-title { font-size: 16px; font-weight: bold; text-align: right; }
    .periode { font-style: italic; text-align: right; }

    .divider { border-top: 2px solid #000; margin: 6px 0; }

    .info-table td { border: none; padding: 2px 0; }
    .info-table .label { width: 90px; }
    .info-table .sep { width: 15px; }

    .content-table { margin-top: 8px; }
    .content-table th, .content-table td {
      border: 1px solid #000;
      padding: 5px 8px;
      font-size: 11px;
    }
    .content-table th { background: #f0f0f0; text-align: left; }
    .amount { text-align: right; white-space: nowrap; }

    .total-row { font-weight: bold; }

    .gaji-bersih-table td {
      border: 2px solid #000;
      padding: 6px 8px;
      font-weight: bold;
    }
    .terbilang { font-style: italic; font-size: 11px; margin-top: 4px; }

    .signature { margin-top: 40px; text-align: right; }
    .signature .space { height: 50px; }
  </style>
</head>
<body>

  @php
    use App\Helpers\Terbilang;
  @endphp

  {{-- Header --}}
  <table class="header-table">
    <tr>
      <td width="60%">
        <div class="company-name">PT MUDA JAYA KAYA RAYA</div>
        <div>Jl. Mastrip No 17 Kota Blitar</div>
        <div>Kepanjen Kidul/Kepanjen Kidul</div>
        <div class="company-name">Jawa Timur</div>
      </td>
      <td width="40%">
        <div class="slip-title">SLIP GAJI</div>
        <div class="periode">{{ $batch->periode }}</div>
      </td>
    </tr>
  </table>
  <div class="divider"></div>

  {{-- Info Karyawan --}}
  <table class="info-table">
    <tr>
      <td class="label">ID</td><td class="sep">:</td><td width="35%">{{ $item->nip }}</td>
      <td class="label">Jabatan</td><td class="sep">:</td><td>{{ $item->jabatan }}</td>
    </tr>
    <tr>
      <td class="label">Nama</td><td class="sep">:</td><td colspan="4"><strong>{{ $item->nama_lengkap }}</strong></td>
    </tr>
  </table>

  {{-- Pendapatan & Potongan sejajar --}}
  <table class="content-table">
    <tr>
      <th width="35%">PENDAPATAN</th><th width="15%"></th>
      <th width="35%">POTONGAN</th><th width="15%"></th>
    </tr>
    <tr>
      <td>Gaji Pokok</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->gaji_pokok) }}</td>
      <td>BPJS Kesehatan</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->bpjs_kesehatan) }}</td>
    </tr>
    <tr>
      <td>Tunjangan Loyalitas</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->tunjangan_loyalitas) }}</td>
      <td>BPJS TK</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->bpjs_tk) }}</td>
    </tr>
    <tr>
      <td>Tunjangan Jabatan</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->tunjangan_jabatan) }}</td>
      <td>Kedisiplinan</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->kedisiplinan) }}</td>
    </tr>
    <tr>
      <td>Lembur</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->lembur) }}</td>
      <td></td><td></td>
    </tr>
    <tr>
      <td>Insentif</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->insentif) }}</td>
      <td></td><td></td>
    </tr>
    <tr>
      <td>Uang Makan</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->uang_makan) }}</td>
      <td></td><td></td>
    </tr>
    <tr class="total-row">
      <td>JUMLAH PENDAPATAN</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->jml_pendapatan) }}</td>
      <td>JUMLAH POTONGAN</td><td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->jml_potongan) }}</td>
    </tr>
  </table>

  {{-- Gaji Bersih --}}
  <table class="gaji-bersih-table" style="margin-top: 8px;">
    <tr>
      <td width="20%">GAJI BERSIH</td>
      <td class="amount">{{ \App\Helpers\Terbilang::formatRp($item->gaji_bersih) }}</td>
    </tr>
  </table>
  <div class="terbilang">({{ $terbilang }})</div>

  {{-- Tanda tangan --}}
  <div class="signature">
    <div>HRD,</div>
    <div class="space"></div>
    <strong>{{ $batch->hrd_name ?? 'HRD' }}</strong>
  </div>

</body>
</html>