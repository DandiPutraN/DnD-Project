<?php

namespace App\Http\Controllers;

use App\Models\Rekeningsaldo;
use Illuminate\Http\Request;

class RekeningSaldoController extends Controller
{
    public function print($id)
    {
        $transaction = Rekeningsaldo::with('rekeningsaldoitems', 'kontak')->findOrFail($id); 
        return view('rekeningsaldo.print', compact('transaction')); 
    }

    public function printVoucher($id)
    {
        $transaction = Rekeningsaldo::with('rekeningsaldoitems', 'kontak')->findOrFail($id);
        return view('rekeningsaldo.voucher', compact('transaction'));
    }
    
}
