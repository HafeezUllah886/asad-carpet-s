<?php

namespace App\Http\Controllers;

use App\Models\material_stock;
use App\Models\production;
use App\Models\production_details;
use App\Models\products;
use App\Models\raw_units;
use App\Models\recipe_management;
use App\Models\stock;
use App\Models\units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productions = production::orderby('id', 'desc')->paginate(10);

        return view('production.index', compact('productions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = products::all();
        $units = units::all();
        $material_units = raw_units::all();
        return view('production.create', compact('products', 'units', 'material_units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $check = production::where('batchNumber', $request->batchNumber)->count();
        if($check > 0)
        {
            return back()->with('error', "Batch Number Already Used");
        }
        try
        {
            $unit = units::find($request->unit);
            DB::beginTransaction();
                $ref = getRef();
                $pro_qty = $request->qty * $unit->value;
                $production = production::create(
                    [
                        'productID'     => $request->productID,
                        'qty'           => $pro_qty,
                        'unitID'        => $unit->id,
                        'date'          => now(),
                        'batchNumber'   => $request->batchNumber,
                        'refID'         => $ref,
                    ]
                );
                createStock($request->productID, $pro_qty, 0, now(), "Production", $request->batchNumber, $ref);
                $ids = $request->material_id;
                foreach($ids as $key => $materials)
                {
                    $raw_unit = raw_units::find($request->material_unit[$key]);
                    $qty = $request->material_qty[$key] * $raw_unit->value;
                    production_details::create(
                        [
                            'productionID'  => $production->id,
                            'materialID'    => $request->material_id[$key],
                            'raw_unitID'    => $request->material_unit[$key],
                            'qty'           => $qty,
                            'refID'         => $ref,
                        ]
                    );

                    createMaterialStock($request->material_id[$key], 0, $qty, now(),"Used in Production", $ref);
                }
            DB::commit();
            return back()->with('success', "Production Saved");
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
    public function show(production $production)
    {
        return view('production.view', compact('production'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(production $production)
    {
        $production = production::with('details', 'unit')->find($production->id);
        $units = units::all();
        $material_units = raw_units::all();
        $products = products::all();

        return view('production.edit', compact('production', 'units', 'material_units', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        
        try
        {
            $pro = production::find($id);
            $ref = $pro->refID;
            $unit = units::find($request->unit);
            DB::beginTransaction();
                $pro_qty = $request->qty * $unit->value;
                
                $pro->update(
                    [
                        'productID'     => $request->productID,
                        'qty'           => $pro_qty,
                        'unitID'        => $unit->id,
                    ]
                );
                
                $pro->details()->delete();
                stock::where('refID', $ref)->delete();
                material_stock::where('refID', $ref)->delete();

                createStock($request->productID, $pro_qty, 0, now(), "Production", $request->batchNumber, $ref);
                $ids = $request->material_id;
                foreach($ids as $key => $materials)
                {
                    $raw_unit = raw_units::find($request->material_unit[$key]);
                    $qty = $request->material_qty[$key] * $raw_unit->value;
                    production_details::create(
                        [
                            'productionID'  => $pro->id,
                            'materialID'    => $request->material_id[$key],
                            'raw_unitID'    => $request->material_unit[$key],
                            'qty'           => $qty,
                            'refID'         => $ref,
                        ]
                    );

                    createMaterialStock($request->material_id[$key], 0, $qty, now(),"Used in Production", $ref);
                }
            DB::commit();
            return back()->with('success', "Production Updated");
        }
        catch(\Exception $e)
        {
            DB::rollBack();
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
            $production = production::find($id);
            $production->details()->delete();
            stock::where('refID', $production->refID)->delete();
            material_stock::where('refID', $production->refID)->delete();
            $production->delete();
            DB::commit();
            session()->forget('confirmed_password');
            return redirect()->route('production.index')->with('success', 'Production Deleted');
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            return redirect()->route('production.index')->with('error', $e->getMessage());
        }
    }

    public function getIngredients($id)
    {
        $ingredients = recipe_management::with('material')->where('productID', $id)->get();

        return response()->json([
            'data' => $ingredients
        ]);
    }
}
