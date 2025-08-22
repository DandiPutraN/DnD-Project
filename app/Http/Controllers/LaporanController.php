<?php

namespace App\Http\Controllers;

use App\Models\Saldo;
use App\Models\RekeningSaldo;
use App\Models\SaldoItem;
use App\Models\transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function kas(Request $request)
    {
        // Ambil parameter selected dari URL
        $selected = $request->query('selected', false);
        
        // Query dasar dengan eager loading
        $query = Saldo::with([
            'saldo_items', 
            'saldo_items.category'
        ])->orderBy('tanggal_transaksi');
    
        // Jika ada data terpilih di session
        $selectedIds = session('selected_ids', []);
        
        if ($selected && !empty($selectedIds)) {
            // Filter berdasarkan ID yang dipilih
            $saldos = $query->whereIn('id', $selectedIds)->get();
        } else {
            // Ambil semua data jika tidak ada filter
            $saldos = $query->get();
        }
    
        // Hitung saldo awal
        $saldo_awal = $this->hitungSaldoAwal($saldos);
    
        // Hapus session setelah digunakan
        $request->session()->forget('selected_ids');
    
        return view('laporan.kas', compact('saldos', 'saldo_awal'));
    }
    
    protected function hitungSaldoAwal($saldos)
    {
        // Jika ada saldo, cari saldo sebelum tanggal transaksi pertama
        if ($saldos->isNotEmpty()) {
            $tanggalPertama = $saldos->min('tanggal_transaksi');
            
            $saldoSebelumnya = Saldo::where('tanggal_transaksi', '<', $tanggalPertama)
                ->orderBy('tanggal_transaksi', 'desc')
                ->first();
    
            return $saldoSebelumnya ? $this->calculateSaldoAkhir($saldoSebelumnya) : 0;
        }
    
        return 0;
    }
    
    protected function calculateSaldoAkhir($saldo)
    {
        // Hitung saldo akhir dari saldo sebelumnya
        $totalDebit = $saldo->saldo_items
            ->where('kas_bank', 'Terima Dana')
            ->sum('biaya');
        
        $totalKredit = $saldo->saldo_items
            ->where('kas_bank', '!=', 'Terima Dana')
            ->sum('biaya');
    
        return $saldo->saldo_awal + $totalDebit - $totalKredit;
    }

    // Rekening saldo

    public function rekening(Request $request)
    {
        // Ambil parameter selected dari URL
        $selected = $request->query('selected', false);
        
        // Query dasar dengan eager loading
        $query = RekeningSaldo::with([
            'rekeningsaldo_items', 
            'rekeningsaldo_items.category'
        ])->orderBy('tanggal_transaksi');
    
        // Jika ada data terpilih di session
        $selectedIds = session('selected_ids', []);
        
        if ($selected && !empty($selectedIds)) {
            // Filter berdasarkan ID yang dipilih
            $rekeningsaldos = $query->whereIn('id', $selectedIds)->get();
        } else {
            // Ambil semua data jika tidak ada filter
            $rekeningsaldos = $query->get();
        }
    
        // Hitung saldo awal
        $rekeningsaldo_awal = $this->rekeninghitungSaldoAwal($rekeningsaldos);
    
        // Hapus session setelah digunakan
        $request->session()->forget('selected_ids');
    
        return view('laporan.rekening', compact('rekeningsaldos', 'rekeningsaldo_awal'));
    }
    
    protected function rekeninghitungSaldoAwal($rekeningsaldo)
    {
        // Jika ada saldo, cari saldo sebelum tanggal transaksi pertama
        if ($rekeningsaldo->isNotEmpty()) {
            $tanggalPertama = $rekeningsaldo->min('tanggal_transaksi');
            
            $rekeningsaldoSebelumnya = RekeningSaldo::where('tanggal_transaksi', '<', $tanggalPertama)
                ->orderBy('tanggal_transaksi', 'desc')
                ->first();
    
            return $rekeningsaldoSebelumnya ? $this->rekeningcalculateSaldoAkhir($rekeningsaldoSebelumnya) : 0;
        }
    
        return 0;
    }
    
    protected function rekeningcalculateSaldoAkhir($rekeningsaldo)
    {
        // Hitung saldo akhir dari saldo sebelumnya
        $totalDebit = $rekeningsaldo->rekeningsaldo_items
            ->where('kas_bank', 'Terima Dana')
            ->sum('biaya');
        
        $totalKredit = $rekeningsaldo->rekeningsaldo_items
            ->where('kas_bank', '!=', 'Terima Dana')
            ->sum('biaya');
    
        return $rekeningsaldo->rekeningsaldo_awal + $totalDebit - $totalKredit;
    }
}