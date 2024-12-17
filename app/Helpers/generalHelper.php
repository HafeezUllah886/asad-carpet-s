<?php

use App\Models\material_stock;
use App\Models\products;
use App\Models\purchase_details;
use App\Models\sale_details;
use App\Models\stock;
use Carbon\Carbon;

function firstDayOfMonth()
{
    $startOfMonth = Carbon::now()->startOfMonth();

    return $startOfMonth->format('Y-m-d');
}
function lastDayOfMonth()
{

    $endOfMonth = Carbon::now()->endOfMonth();

    return $endOfMonth->format('Y-m-d');
}


function createStock($id, $cr, $db, $date, $notes, $ref, $warehouse)
{
    stock::create(
        [
            'productID'     => $id,
            'cr'            => $cr,
            'db'            => $db,
            'date'          => $date,
            'notes'         => $notes,
            'refID'         => $ref,
            'warehouseID'   => $warehouse,
        ]
    );
}

function getStock($id){
    $stocks  = stock::where('productID', $id)->get();
    $balance = 0;
    foreach($stocks as $stock)
    {
        $balance += $stock->cr;
        $balance -= $stock->db;
    }


    return $balance;
}

function projectName()
{
    return "Asad Carpets";
}

function projectShortName()
{
    return "AC";
}

function getSize($length, $width) {

    return $length * $width;
}

function sqFeets($inches)
{
    return  $inches / 144;
}

function avgSalePrice($from, $to, $id)
{
    $sales = sale_details::where('productID', $id);
    if($from != 'all' && $to != 'all')
    {
        $sales->whereBetween('date', [$from, $to]);
    }
    $product = products::find($id);
    if($product->unit == "Nos")
    {
        $sales_amount = $sales->sum('amount');
        $sales_qty = $sales->sum('qty');
    }
    else
    {
        $sales_amount = $sales->sum('amount');
        $sales_qty = $sales->sum('totalsize');
    }

    if($sales_qty > 0)
    {
        $sale_price = $sales_amount / $sales_qty;
    }
    else
    {
        $sale_price = 0;
    }

    return $sale_price;
}


function avgPurchasePrice($from, $to, $id)
{
    $purchases = purchase_details::where('productID', $id);
    if($from != 'all' && $to != 'all')
    {
        $purchases->whereBetween('date', [$from, $to]);
    }
    $product = products::find($id);
    if($product->cat == "Kaleen")
    {
        $purchase_amount = $purchases->sum('amountpkr');
        $purchase_qty = $purchases->sum('qty');
    }
    else
    {
        $purchase_amount = $purchases->sum('amountpkr');
        $purchase_qty = $purchases->sum('totalsize');
    }

    if($purchase_qty > 0)
    {
        $purchase_price = $purchase_amount / $purchase_qty;
    }
    else
    {
        $purchase_price = 0;
    }

    return $purchase_price;
}

function stockValue()
{
    $products = products::all();

    $value = 0;
    foreach($products as $product)
    {
        $value += productStockValue($product->id);
    }

    return $value;
}

function productStockValue($id)
{
    $stock = getStock($id);
    $price = avgPurchasePrice('all', 'all', $id);

    return $price * $stock;
}

function metersToFeet($meters) {
    return round($meters * 3.28084,1);
}

function squareMetersToSquareFeet($squareMeters) {
    return round($squareMeters * 10.7639,1);
}

