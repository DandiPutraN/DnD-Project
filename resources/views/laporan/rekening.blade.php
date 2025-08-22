<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekening Bank Nama Perusahaan</title>

    <!-- Favicons -->
    <link href="{{ asset('assets/img/favicon.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none; }
            body { font-size: 12px; }
        }
        .header {
            border-bottom: 2px solid black;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }
        .signature-section { margin-top: 30px; }
        .signature-box {
            border-top: 1px solid black;
            padding-top: 5px;
            text-align: center;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Kop Surat -->
        <div class="header text-center">
            <h2 class="mb-1">Nama Perusahaan</h2>
            <p class="mb-1">Laporan Kas Besar <br> (Bank)</p>
            
            @php
                // Pastikan hanya mengambil tanggal dari data yang ada
                if ($rekeningsaldos->isNotEmpty()) {
                    $tanggalAwal = $rekeningsaldos->min('tanggal_transaksi');
                    $tanggalAkhir = $rekeningsaldos->max('tanggal_transaksi');
                } else {
                    $tanggalAwal = now();
                    $tanggalAkhir = now();
                }
            @endphp
            
            <p>
                Per 
                {{ \Carbon\Carbon::parse($tanggalAwal)->format('d F Y') }} 
                s/d 
                {{ \Carbon\Carbon::parse($tanggalAkhir)->format('d F Y') }}
            </p>
        </div>

        <!-- Bagian Transaksi -->
        <div class="mb-4">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Keterangan</th>
                        <th>Debit</th>
                        <th>Kredit</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sisarekeningSaldo = $rekeningsaldo_awal ?? 0;
                        $totalDebit = 0;
                        $totalKredit = 0;
                        $nomorUrut = 1;
                    @endphp
        
                    @forelse($rekeningsaldos as $rekeningsaldo)
                        @foreach($rekeningsaldo->rekeningsaldo_items as $item)
                            @php
                                $status = $rekeningsaldo->transaction ? $rekeningsaldo->transaction->status : null;
                            @endphp
                            
                            @if($status !== 'unpaid') {{-- Cek status transaksi, jika bukan unpaid maka tampilkan --}}
                                <tr>
                                    <td>{{ $nomorUrut++ }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rekeningsaldo->tanggal_transaksi)->format('d/M/Y') }}</td>
                                    <td>
                                        {{ $item->account ? $item->account->nama : 'Tidak ada kategori' }} 
                                        - 
                                        {{ $item->keterangan ?? 'Tidak ada keterangan' }}
                                    </td>
                                    <td>
                                        @php
                                            $isDebit = $rekeningsaldo->kas_bank == 'Terima Dana';
                                            $biaya = $item->biaya;
                                        @endphp
        
                                        @if ($isDebit)
                                            Rp. {{ number_format($biaya, 2, ',', '.') }}
                                            @php $totalDebit += $biaya; @endphp
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @if (!$isDebit)
                                            Rp. {{ number_format($biaya, 2, ',', '.') }}
                                            @php $totalKredit += $biaya; @endphp
                                        @else
                                            0
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            if ($isDebit) {
                                                $sisarekeningSaldo += $biaya;
                                            } else {
                                                $sisarekeningSaldo -= $biaya;
                                            }
                                        @endphp
                                        Rp. {{ number_format($sisarekeningSaldo, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada data transaksi</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-center">Jumlah</th>
                        <th>Rp. {{ number_format($totalDebit, 2, ',', '.') }}</th>
                        <th>Rp. {{ number_format($totalKredit, 2, ',', '.') }}</th>
                        <th>Rp. {{ number_format($rekeningsaldo_awal + $totalDebit - $totalKredit, 2, ',', '.') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Bagian Tanda Tangan -->
        <div class="signature-section row mt-5">
            <div class="col-6 text-center">
                <div class="signature-box">
                    <p class="mb-4">Finance,</p>
                    <div style="height: 100px;"></div>
                    <strong>Staff</strong>
                </div>
            </div>
            <div class="col-6 text-center">
                <div class="signature-box">
                    <p class="mb-4">Menyetujui,</p>
                    <div style="height: 100px;"></div>
                    <strong>Manager</strong>
                </div>
            </div>
        </div>

        <!-- Tombol Cetak -->
        <div class="no-print text-center mt-4">
            <button class="btn btn-primary" onclick="window.print()">Cetak Laporan</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>