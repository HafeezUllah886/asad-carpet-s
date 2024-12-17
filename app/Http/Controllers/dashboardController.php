<?php

namespace App\Http\Controllers;

use App\Models\accounts;
use App\Models\expenses;
use App\Models\products;
use App\Models\purchase_details;
use App\Models\sale_details;
use App\Models\sales;
use Carbon\Carbon;
use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    public function index()
    {

        $months = [];

        for ($i = 0; $i < 12; $i++) {
            $date = Carbon::now()->subMonths($i);

            $firstDay = $date->copy()->firstOfMonth()->toDateString();

            $lastDay = $date->copy()->lastOfMonth()->toDateString();

            $monthName = $date->format('M');

            $months[] = ['first' => $firstDay, 'last' => $lastDay, 'name' => $monthName];
        }

            $months = array_reverse($months);

            $sales = [];
            $monthNames = [];
            $expenses = [];
            $products = products::all();
            $profits = [];

            $last_sale = 0;
            $last_expense = 0;
            $last_profit = 0;
            foreach($months as $key => $month)
            {

                 $first = $month['first'];
                $last = $month['last'];
                $sale = sales::whereBetween('date', [$first, $last])->count();
                $expense = expenses::whereBetween('date', [$first, $last])->sum('amount');
                $sales[] = $sale;
                $expenses[] = $expense;
                $monthNames [] = $month['name'];
                $profit = 0;
                foreach($products as $product)
                {
                    $purchaseRate = round(avgPurchasePrice($first, $last, $product->id));
                    $saleRate = round(avgSalePrice($first, $last, $product->id));
                    if($product->cat == "Kaleen")
                    {
                        $sold = sale_details::where('productID', $product->id)->whereBetween('date', [$first, $last])->sum('qty');
                    }
                    else
                    {
                        $sold = sale_details::where('productID', $product->id)->whereBetween('date', [$first, $last])->sum('totalsize');
                        $sold = sqFeets($sold);
                    }

                    $ppu = $saleRate - $purchaseRate;
                    $profit += $ppu * $sold;
                    $stock = getStock($product->id);
                    $stockValue = productStockValue($product->id);

                }

                $profits[] = $profit - $expense;

                $last_sale = $sale;
                $last_expense = $expense;
                $last_profit = $profit;

            }


            /// Top five products

            $topProducts = products::withSum('saleDetails', 'totalsize')->withSum('saleDetails', 'amount')
            ->orderByDesc('sale_details_sum_amount')
            ->take(5)
            ->get();

            $topProductsArray = [];

            foreach($topProducts as $product)
            {
                $stock = getStock($product->id);
                $price = avgSalePrice('all', 'all', $product->id);

                $topProductsArray [] = ['code' => $product->code, 'color' => $product->color,'price' => $price, 'stock' => $stock, 'amount' => $product->sale_details_sum_amount, 'sold' => $product->sale_details_sum_totalsize, 'cat' => $product->cat, 'id' => $product->id];
            }

            /// Top Customers

            $topCustomers = accounts::where('type', 'Customer')
            ->withSum('sale', 'net')
            ->orderByDesc('sale_sum_net')
            ->take(5)
            ->get();

            $topCustomersArray = [];

            foreach($topCustomers as $customer)
            {
                if($customer->id != 2)
                {
                    $balance = getAccountBalance($customer->id);
                    $customer_purchases = $customer->sale_sum_total;

                    $topCustomersArray [] = ['name' => $customer->title, 'purchases' => $customer_purchases, 'balance' => $balance];
                }

            }



        return view('dashboard.index', compact('sales', 'monthNames', 'expenses', 'profits', 'last_sale', 'last_expense', 'last_profit', 'topProductsArray', 'topCustomersArray'));
    }
}
