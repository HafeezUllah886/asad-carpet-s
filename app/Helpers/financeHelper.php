<?php

use App\Models\accounts;
use App\Models\balance;
use App\Models\ref;
use App\Models\transactions;


function getRef(){
    $ref = ref::first();
    if($ref){
        $ref->ref = $ref->ref + 1;
    }
    else{
        $ref = new ref();
        $ref->ref = 1;
    }
    $ref->save();
    return $ref->ref;
}

function createTransaction($accountID, $date, $cr, $db, $notes, $ref){
    transactions::create(
        [
            'accountID' => $accountID,
            'date' => $date,
            'cr' => $cr,
            'db' => $db,
            'notes' => $notes,
            'refID' => $ref,
        ]
    );

}

function getAccountBalance($id){
    $transactions  = transactions::where('accountID', $id)->get();
    $balance = 0;
    foreach($transactions as $trans)
    {
        $balance += $trans->cr;
        $balance -= $trans->db;
    }

    return $balance;
}

function getGroupAccountBalance($type)
{
    if($type == 'All')
    {
        $accounts = accounts::all();
    }
    else
    {
        $accounts = accounts::where('type', $type)->get();
    }
    $balance = 0;
    foreach($accounts as $account)
    {
        $balance += getAccountBalance($account->id);
    }

    return $balance;
}

function numberToWords($number)
{
    $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
    return ucfirst($f->format($number));
}


function spotBalanceBefore($id, $ref)
{
    $cr = transactions::where('accountID', $id)->where('refID', '<', $ref)->sum('cr');
    $db = transactions::where('accountID', $id)->where('refID', '<', $ref)->sum('db');
    return $balance = $cr - $db;
}

function spotBalance($id, $ref)
{
    $cr = transactions::where('accountID', $id)->where('refID', '<=', $ref)->sum('cr');
    $db = transactions::where('accountID', $id)->where('refID', '<=', $ref)->sum('db');
    return $balance = $cr - $db;
}


function getWhatsappBalance(){
    $trans  = balance::all();
    $balance = 0;
    foreach($trans as $tran)
    {
        $balance += $tran->cr;
        $balance -= $tran->db;
    }

    return $balance;
}

function createWhatsappBalance($notes, $date)
{
    balance::create(
        [
            'date' => $date,
            'db' => 0.006,
            'notes' => $notes
        ]
    );
}

function convertToK($amount, $precision = 1)
    {
        if ($amount >= 1000000000) {
            return number_format($amount / 1000000000, $precision) . 'B'; // Billion
        } elseif ($amount >= 1000000) {
            return number_format($amount / 1000000, $precision) . 'M'; // Million
        } elseif ($amount >= 1000) {
            return number_format($amount / 1000, $precision) . 'K'; // Thousand
        }

        return $amount; // Less than 1000
    }












