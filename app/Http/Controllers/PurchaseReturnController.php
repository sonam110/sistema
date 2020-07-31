<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseReturnController extends Controller
{
    public function purchaseReturnList()
    {
      	return view('purchases.purchase-return');
    }
}
