<?php

namespace App\Filament\Widgets;

use App\Models\Saldo;
use App\Models\SaldoItem;
use App\Models\transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\TransactionItem;
use App\Models\RekeningSaldoItem;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsWidget extends BaseWidget
{
    // use InteractsWithPageFilters;

    protected function getStats(): array
    {
        // Validasi filter tanggal
        $start = isset($this->filters['startDate']) ? Carbon::parse($this->filters['startDate']) : now()->startOfYear();
        $end = isset($this->filters['endDate']) ? Carbon::parse($this->filters['endDate']) : now();

        // Penghitungan Kas
        $kasData = $this->calculateBalanceData('1-00001 - Kas', 'Terima Dana', $start, $end);

        // Penghitungan Rekening
        $rekeningData = $this->calculateBalanceData('1-00002 - Rekening', 'Terima Dana', $start, $end);

        $totalIncome = RekeningSaldoItem::query()
        ->join('rekeningsaldos', 'rekening_saldo_items.rekeningsaldo_id', '=', 'rekeningsaldos.id')  // Melakukan join dengan tabel 'saldos'
        ->where('rekeningsaldos.kas_bank', 'Terima Dana')  // Menambahkan kondisi pada kolom 'kas_bank'
        ->sum('rekening_saldo_items.biaya');  // Mengambil total 'biaya' dari tabel 'saldoitems'    

        // Total pengeluaran dari semua transaksi yang sudah lunas
        $totalExpense = DB::table('transaction_items')
        ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
        // ->where('transactions.lunas', false)  // Hanya transaksi yang lunas
        ->where('transactions.bayar_dari', '1-00002 - Rekening')  // Hanya transaksi yang dibayar dari "Kas"
        ->where('transactions.status', 'paid')  // Transaksi yang belum lunas
        ->sum('transaction_items.biaya'); 

        // Hitung saldo bersih petty cash
        $netBalance = $totalIncome - $totalExpense;

        // Grafik Saldo Kas
        $kasChartData = $this->generateChartData('1-00001 - Kas', $start, $end);

        // Grafik Saldo Rekening
        $rekeningChartData = $this->generateChartData('1-00002 - Rekening', $start, $end);

        return [
            // Stats Saldo Kas
            Stat::make('Kas Masuk', Number::currency($kasData['income'], 'IDR'))
                ->description('Total terima dana (voucher)')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Kas Keluar', Number::currency($kasData['expense'], 'IDR'))
                ->description('Total pengeluaran (voucher)')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Petty Cash', Number::currency($kasData['net'], 'IDR'))
                ->description('Total saldo sekarang')
                ->color($kasData['net'] >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-currency-dollar')
                ->chart($kasChartData), 

            // Stats Saldo Rekening
            Stat::make('Rekening Masuk', Number::currency($totalIncome, 'IDR'))
            ->description('Total terima dana (voucer)')
            ->icon('heroicon-o-currency-dollar'),

            Stat::make('Transaksi Keluar - Lunas', Number::currency($totalExpense, 'IDR'))
                ->description('Total biaya (invoice) yang telah dibayar')
                ->icon('heroicon-o-currency-dollar'),

            // Stat::make('Saldo Rekening', Number::currency($rekeningData['net'], 'IDR'))
            // ->description('Total saldo sekarang')
            // ->color($rekeningData['net'] >= 0 ? 'success' : 'danger')
            // ->icon('heroicon-o-currency-dollar'),

            Stat::make('Saldo Rekening', Number::currency($netBalance, 'IDR'))
            ->description('Total Saldo Sekarang')
            ->color($netBalance >= 0 ? 'success' : 'danger')
            ->icon('heroicon-o-currency-dollar')
            ->chart($rekeningChartData),
        ];
    }

    private function calculateBalanceData($source, $kasBankCondition, $start, $end): array
    {
        // Total pemasukan
        $income = DB::table('saldo_items')
            ->join('saldos', 'saldo_items.saldo_id', '=', 'saldos.id')
            ->where('saldos.kas_bank', $kasBankCondition)
            ->whereBetween('saldos.created_at', [$start, $end])
            ->sum('saldo_items.biaya');

        // Total pengeluaran
        $expense = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.bayar_dari', $source)
            ->where('transactions.status', 'paid')
            ->whereBetween('transactions.created_at', [$start, $end])
            ->sum('transaction_items.biaya');

        // Hitung saldo bersih
        $net = $income - $expense;

        return compact('income', 'expense', 'net');
    }

    private function generateChartData($source, $start, $end)
    {
        return DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.bayar_dari', $source)
            ->whereBetween('transactions.created_at', [$start, $end])
            ->selectRaw('MONTH(transactions.created_at) as month, SUM(transaction_items.biaya) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->values()
            ->toArray();
    }
}
