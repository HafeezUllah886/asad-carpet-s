<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\products;
use App\Models\sale_details;
use App\Models\sale_payments;
use App\Models\sales;
use App\Models\stock;
use App\Models\transactions;
use App\Models\units;
use App\Models\warehouses;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sales = sales::orderby('id', 'desc')->get();
        return view('sales.index', compact('sales'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::orderby('code', 'asc')->get();
        $customers = accounts::customer()->get();
        $accounts = accounts::business()->get();
        $warehouses = warehouses::all();
        return view('sales.create', compact('products', 'customers', 'accounts', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
            DB::beginTransaction();
            $ref = getRef();
            $sale = sales::create(
                [
                  'customerID'      => $request->customerID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
                  'discount'        => $request->discount,
                  'dc'              => $request->dc,
                  'net'             => $request->total,
                  'refID'           => $ref,
                ]
            );

            $ids = $request->id;

            $total = 0;
            foreach($ids as $key => $id)
            {
                $totalsize = $request->size[$key] * $request->qty[$key];
                $amount = $request->amount[$key];
                $total += $amount;
                $product = products::find($id);
                if($product->cat == 'Kaleen')
                {
                    $totalsize = $request->qty[$key];
                }

                sale_details::create(
                    [
                        'salesID'        => $sale->id,
                        'productID'     => $id,
                        'warehouseID'   => $request->warehouse[$key],
                        'width'       => $request->width[$key],
                        'length'       => $request->length[$key],
                        'size'          => $request->size[$key],
                        'price'         => $request->price[$key],
                        'qty'           => $request->qty[$key],
                        'totalsize'     => $totalsize,
                        'amount'        => $request->amount[$key],
                        'date'          => $request->date,
                        'refID'         => $ref,
                    ]
                );
                dashboard();
                createStock($id, 0, $totalsize, $request->date, "Sold", $ref, $request->warehouse[$key]);
            }
            $total = $request->total;

            if($request->status == 'paid')
            {
                createTransaction($request->accountID, $request->date, $total, 0, "Payment of Invoice No. $sale->id", $ref);
            }
            else
            {
                createTransaction($request->customerID, $request->date, $total, 0, "Pending Amount of Invoice No. $sale->id", $ref);

            }
            DB::commit();
            return to_route('sale.show', $sale->id)->with('success', "Sale Created");

        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(sales $sale)
    {
        $cr = transactions::where('accountID', $sale->customerID)->where('refID', '<', $sale->refID)->sum('cr');
        $db = transactions::where('accountID', $sale->customerID)->where('refID', '<', $sale->refID)->sum('db');
        $balance = $cr - $db;

        return view('sales.view', compact('sale', 'balance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(sales $sale)
    {
        $products = products::orderby('code', 'asc')->get();
        $customers = accounts::customer()->get();
        $accounts = accounts::business()->get();
        $warehouses = warehouses::all();
        return view('sales.edit', compact('products', 'warehouses', 'customers', 'accounts', 'sale'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try
        {
            DB::beginTransaction();
            $sale = sales::find($id);
            foreach($sale->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $sale->refID)->delete();
            $sale->update(
                [
                  'customerID'      => $request->customerID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
                  'discount'        => $request->discount,
                  'dc'              => $request->dc,
                  'net'             => $request->total,
                ]
            );

            $ids = $request->id;
            $ref = $sale->refID;

            $total = 0;
            foreach($ids as $key => $id)
            {
                $totalsize = $request->size[$key] * $request->qty[$key];
                $amount = $request->amount[$key];
                $total += $amount;
                $product = products::find($id);
                if($product->cat == 'Kaleen')
                {
                    $totalsize = $request->qty[$key];
                }

                sale_details::create(
                    [
                        'salesID'        => $sale->id,
                        'productID'     => $id,
                        'warehouseID'   => $request->warehouse[$key],
                        'width'       => $request->width[$key],
                        'length'       => $request->length[$key],
                        'size'          => $request->size[$key],
                        'price'         => $request->price[$key],
                        'qty'           => $request->qty[$key],
                        'totalsize'     => $totalsize,
                        'amount'        => $request->amount[$key],
                        'date'          => $request->date,
                        'refID'         => $ref,
                    ]
                );
                createStock($id, 0, $totalsize, $request->date, "Sold", $ref, $request->warehouse[$key]);
            }
            $total = $request->total;

            if($request->status == 'paid')
            {
                createTransaction($request->accountID, $request->date, $total, 0, "Payment of Invoice No. $sale->id", $ref);
            }
            else
            {
                createTransaction($request->customerID, $request->date, $total, 0, "Pending Amount of Invoice No. $sale->id", $ref);

            }
            DB::commit();
            return to_route('sale.index')->with('success', "Sale Updated");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return to_route('sale.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try
        {
            DB::beginTransaction();
            $sale = sales::find($id);
            foreach($sale->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $sale->refID)->delete();
            $sale->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return to_route('sale.index')->with('success', "Sale Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return to_route('sale.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {

        $product = products::find($id);
        return $product;
    }


}
