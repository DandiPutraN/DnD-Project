<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\transaction;
use Illuminate\Support\Number;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TransactionStats extends BaseWidget
{
    protected function getStats(): array
    {
        $totalBelumBayar = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.lunas', true)  // Transaksi yang belum lunas
            ->where('transactions.status', 'paid')  // Transaksi yang belum lunas
            ->sum('transaction_items.biaya');  // Ambil total biaya

        $totalLunas = DB::table('transaction_items')
        ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
        ->where('transactions.lunas', false) // Hanya transaksi yang lunas
        ->where('transactions.status', 'paid')  // Transaksi yang belum lunas
        ->sum('transaction_items.biaya');

        $totalExpense = TransactionItem::query()
        ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id') // Join dengan tabel transactions
        ->where('transactions.status', 'paid')
        ->sum('transaction_items.biaya');

        return [
            
            Stat::make('Tagihan - Belum Bayar', Number::currency($totalBelumBayar, 'IDR'))
            ->description('Total tagihan belum dibayar')
            ->icon('heroicon-o-exclamation-circle'), // Ikon untuk status "Belum Bayar"

            Stat::make('Transaksi Keluar - Lunas', Number::currency($totalLunas, 'IDR'))
            ->description('Total biaya (invoice) yang telah dibayar')
            ->icon('heroicon-o-currency-dollar'),
            
            Stat::make('Total Transaksi Keluar', Number::currency($totalExpense, 'IDR'))
            ->description('Total pengeluaran bank (voucher)')
            ->icon('heroicon-o-currency-dollar'),
        ];
    }
}