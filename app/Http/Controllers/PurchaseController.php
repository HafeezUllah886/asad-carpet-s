<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\products;
use App\Models\purchase;
use App\Models\purchase_details;
use App\Models\purchase_payments;
use App\Models\stock;
use App\Models\transactions;
use App\Models\warehouses;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $purchases = purchase::orderby('id', 'desc')->get();
        return view('purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::orderby('code', 'asc')->get();
        $warehouses = warehouses::all();
        $vendors = accounts::vendor()->get();
        return view('purchase.create', compact('products', 'warehouses', 'vendors'));
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
            $purchase = purchase::create(
                [
                  'vendorID'        => $request->vendorID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,
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

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'warehouseID'   => $request->warehouse[$key],
                        'length'       => $request->length[$key],
                        'width'        => $request->width[$key],
                        'size'          => $request->size[$key],
                        'price'         => $request->price[$key],
                        'toman'         => $request->toman[$key],
                        'qty'           => $request->qty[$key],
                        'totalsize'     => $totalsize,
                        'amount'        => $request->amount[$key],
                        'amountpkr'        => $request->amountpkr[$key],
                        'date'          => $request->date,
                        'refID'         => $ref,
                    ]
                );

                $product = products::find($id);
                if($product->cat == 'Kaleen')
                {
                    $totalsize = $request->qty[$key];
                }
                createStock($id, $totalsize, 0, $request->date, "Purchased", $ref, $request->warehouse[$key]);
            }

            $purchase->update(
                [
                    'net' => $total,
                ]
            );
            createTransaction($request->vendorID, $request->date, 0, $total, "Pending Amount of Purchase No. $purchase->id", $ref);
            DB::commit();
            return back()->with('success', "Purchase Created");

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
    public function show(purchase $purchase)
    {
        return view('purchase.view', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(purchase $purchase)
    {
        $products = products::orderby('code', 'asc')->get();
        $warehouses = warehouses::all();
        $vendors = accounts::vendor()->get();
        return view('purchase.edit', compact('products', 'vendors', 'purchase', 'warehouses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, purchase $purchase)
    {
        try
        {
            if($request->isNotFilled('id'))
            {
                throw new Exception('Please Select Atleast One Product');
            }
            dashboard();
            DB::beginTransaction();
            foreach($purchase->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();

            $purchase->update(
                [
                    'vendorID'        => $request->vendorID,
                  'date'            => $request->date,
                  'notes'           => $request->notes,

                  ]
            );
            $ref = $purchase->refID;

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

                purchase_details::create(
                    [
                        'purchaseID'    => $purchase->id,
                        'productID'     => $id,
                        'warehouseID'   => $request->warehouse[$key],
                        'width'       => $request->width[$key],
                        'length'       => $request->length[$key],
                        'size'          => $request->size[$key],
                        'price'         => $request->price[$key],
                        'toman'         => $request->toman[$key],
                        'qty'           => $request->qty[$key],
                        'totalsize'     => $totalsize,
                        'amount'        => $request->amount[$key],
                        'amountpkr'        => $request->amountpkr[$key],
                        'date'          => $request->date,
                        'refID'         => $ref,
                    ]
                );

                createStock($id, $totalsize, 0, $request->date, "Purchased", $ref, $request->warehouse[$key]);
            }

            $purchase->update(
                [
                    'net' => $total,
                ]
            );

            createTransaction($request->vendorID, $request->date,0 , $total, "Pending Amount of Purchase No. $purchase->id", $purchase->refID);

            DB::commit();
            return back()->with('success', "Purchase Updated");

        }
        catch(\Exception $e)
        {
            DB::rollback();
            return back()->with('error', $e->getMessage());
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
            $purchase = purchase::find($id);
            foreach($purchase->details as $product)
            {
                stock::where('refID', $product->refID)->delete();
                $product->delete();
            }
            transactions::where('refID', $purchase->refID)->delete();
            $purchase->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('purchase.index')->with('success', "Purchase Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('purchase.index')->with('error', $e->getMessage());
        }
    }

    public function getSignleProduct($id)
    {
        $product = products::find($id);
        return $product;
    }
}
