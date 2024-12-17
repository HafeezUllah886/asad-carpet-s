<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\vendorPayments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorPaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendorPayments = vendorPayments::orderBy('id', 'desc')->get();
        $vendors = accounts::Vendor()->get();
        $exchanges = accounts::Exchange()->get();
        $accounts = accounts::Business()->get();

        return view('Finance.vendorPayments.index', compact('vendorPayments', 'vendors', 'accounts', 'exchanges'));
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
            vendorPayments::create(
                [
                    'vendorID' => $request->vendorID,
                    'exchangeID' => $request->exchangeID,
                    'accountID' => $request->accountID,
                    'pkr' => $request->pkr,
                    'rate' => $request->rate,
                    'toman' => $request->toman,
                    'date' => $request->date,
                    'notes' => $request->notes,
                    'refID' => $ref,
                ]
            );


            createTransaction($request->vendorID, $request->date, $request->toman, 0, "Paid Through Vendor Payment | Rate: $request->rate | Pkr: $request->pkr | Notes: $request->notes", $ref);

            if($request->ispaid == "Paid")
            {
                createTransaction($request->accountID, $request->date, 0, $request->pkr, "Paid Through Vendor Payment | Rate: $request->rate | Toman: $request->toman | Notes: $request->notes", $ref);
                createTransaction($request->exchangeID, $request->date, $request->pkr, $request->pkr, "Paid Through Vendor Payment | Rate: $request->rate | Toman: $request->toman | Notes: $request->notes", $ref);
            }
            else
            {
                createTransaction($request->exchangeID, $request->date, $request->pkr, 0, "Pending of Vendor Payment | Rate: $request->rate | Toman: $request->toman | Notes: $request->notes", $ref);
            }
            DB::commit();
            return back()->with('success', 'Payment Saved');
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
    public function show(vendorPayments $vendorPayments)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(vendorPayments $vendorPayments)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, vendorPayments $vendorPayments)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(vendorPayments $vendorPayments)
    {
        //
    }
}
