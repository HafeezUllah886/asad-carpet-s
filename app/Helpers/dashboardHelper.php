<?php

use App\Models\accounts;
use App\Models\purchase_details;
use App\Models\sale_details;

function totalSales()
{
    $amount = sale_details::sum('amount');
    return convertToK($amount);
}

function totalPurchases()
{
    $amount = purchase_details::sum('amountpkr');
   return convertToK($amount);
}
function totalPurchasesToman()
{
   $amount = purchase_details::sum('amount');
   return convertToK($amount);
}

function myBalance()
{
    $accounts = accounts::where('type', 'Business')->get();
    $balance = 0;
    foreach($accounts as $account)
    {
        $balance += getAccountBalance($account->id);
    }
    return convertToK($balance);
}

function customerBalance()
{
    $accounts = accounts::where('type', 'Customer')->get();
    $balance = 0;
    foreach($accounts as $account)
    {
        $balance += getAccountBalance($account->id);
    }

    return convertToK($balance);
}

function exchangeBalance()
{
    $accounts = accounts::where('type', 'Exchnge')->get();
    $balance = 0;
    foreach($accounts as $account)
    {
        $balance += getAccountBalance($account->id);
    }

    return convertToK($balance);
}


function vendorBalance()
{
    $accounts = accounts::where('type', 'Vendor')->get();
    $balance = 0;
    foreach($accounts as $account)
    {
        $balance += getAccountBalance($account->id);
    }

    return convertToK($balance);
}
