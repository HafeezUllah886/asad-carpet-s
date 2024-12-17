<?php

namespace App\Http\Controllers;

use App\Models\products;
use App\Models\stock;
use App\Models\stockAdjustment;
use App\Models\units;
use App\Models\warehouses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $adjustments = stockAdjustment::orderBy('id', 'desc')->get();
        $products = products::all();
        $warehouses = warehouses::all();

        return view('stock.adjustment.index', compact('adjustments', 'products', 'warehouses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try
        {
            DB::beginTransaction();
            $ref = getRef();
            $qty = $request->qty;
            stockAdjustment::create(
                [
                    'productID' => $request->productID,
                    'warehouseID' => $request->warehouseID,
                    'date'      => $request->date,
                    'type'      => $request->type,
                    'qty'       => $request->qty,
                    'notes'     => $request->notes,
                    'refID'     => $ref
                ]
            );

            if($request->type == 'Stock-In')
            {
               createStock($request->productID, $qty, 0, $request->date, "Stock-In: $request->notes", $ref, $request->warehouseID);
            }
            else
            {
                createStock($request->productID, 0, $qty, $request->date, "Stock-Out: $request->notes", $ref, $request->warehouseID);
            }

            DB::commit();
            return back()->with('success', "Stock Adjustment Created");
        }
        catch(\Exception $e)
        {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(stockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(stockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, stockAdjustment $stockAdjustment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ref)
    {
        try
        {
            DB::beginTransaction();
            stockAdjustment::where('refID', $ref)->delete();
            stock::where('refID', $ref)->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('stockAdjustments.index')->with('success', "Stock Adjustment Deleted");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            session()->forget('confirmed_password');
            return redirect()->route('stockAdjustments.index')->with('error', $e->getMessage());
        }
    }
}
