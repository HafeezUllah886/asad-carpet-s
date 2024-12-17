<?php

namespace App\Http\Controllers;

use App\Models\expenses;
use App\Models\products;
use App\Models\sale_details;
use Illuminate\Http\Request;

class profitController extends Controller
{
    public function index()
    {
        return view('reports.profit.index');
    }

    public function data($from, $to)
    {
        $products = products::all();
        $data = [];
        foreach($products as $product)
        {
            $purchaseRate = avgPurchasePrice($from, $to, $product->id);
            $saleRate = avgSalePrice($from, $to, $product->id);
            if($product->unit == "Nos")
            {
                $sold = sale_details::where('productID', $product->id)->whereBetween('date', [$from, $to])->sum('qty');
            }
            else
            {
                $sold = sale_details::where('productID', $product->id)->whereBetween('date', [$from, $to])->sum('totalsize');
                $sold = $sold;
            }

            $ppu = $saleRate - $purchaseRate;
            $profit = $ppu * $sold;
            $stock = getStock($product->id);
            $stockValue = productStockValue($product->id);

            $data[] = ['code' => $product->code, 'color' => $product->color, 'desc' => $product->desc, 'purchaseRate' => $purchaseRate, 'saleRate' => $saleRate, 'sold' => $sold, 'ppu' => $ppu, 'profit' => $profit, 'stock' => $stock, 'stockValue' => $stockValue];
        }

        $expenses = expenses::whereBetween('date', [$from, $to])->sum('amount');

        return view('reports.profit.details', compact('from', 'to', 'data', 'expenses'));
    }
}
