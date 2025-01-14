<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerDashboardController extends Controller
{
    
    public function index(Customer $customer)
{
    // Monthly Statistics
    $monthlyOrderSummary = $customer->orders()
        ->selectRaw('DATE_FORMAT(ordering_date, "%Y-%m") as month, COUNT(*) as total_orders')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();

    $monthlySquareMeterSummary = $customer->orders()
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->selectRaw('DATE_FORMAT(ordering_date, "%Y-%m") as month, SUM(squaremeter) as total_squaremeter')
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->get();

    $monthlyOrdersByProduct = $customer->orders()
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->join('products', 'order_products.product_id', '=', 'products.id')
        ->selectRaw('DATE_FORMAT(ordering_date, "%Y-%m") as month, products.name as product_name, COUNT(*) as total_orders')
        ->groupBy('month', 'product_name')
        ->orderBy('month', 'desc')
        ->get();

    $monthlySquareMeterByProduct = $customer->orders()
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->join('products', 'order_products.product_id', '=', 'products.id')
        ->selectRaw('DATE_FORMAT(ordering_date, "%Y-%m") as month, products.name as product_name, SUM(squaremeter) as total_squaremeter')
        ->groupBy('month', 'product_name')
        ->orderBy('month', 'desc')
        ->get();

    // Yearly Statistics
    $yearlyOrderSummary = $customer->orders()
        ->selectRaw('YEAR(ordering_date) as year, COUNT(*) as total_orders')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->get();

    $yearlySquareMeterSummary = $customer->orders()
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->selectRaw('YEAR(ordering_date) as year, SUM(squaremeter) as total_squaremeter')
        ->groupBy('year')
        ->orderBy('year', 'desc')
        ->get();

    $yearlyOrdersByProduct = $customer->orders()
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->join('products', 'order_products.product_id', '=', 'products.id')
        ->selectRaw('YEAR(ordering_date) as year, products.name as product_name, COUNT(*) as total_orders')
        ->groupBy('year', 'product_name')
        ->orderBy('year', 'desc')
        ->get();

    $yearlySquareMeterByProduct = $customer->orders()
        ->join('order_products', 'orders.id', '=', 'order_products.order_id')
        ->join('products', 'order_products.product_id', '=', 'products.id')
        ->selectRaw('YEAR(ordering_date) as year, products.name as product_name, SUM(squaremeter) as total_squaremeter')
        ->groupBy('year', 'product_name')
        ->orderBy('year', 'desc')
        ->get();

    return view('customers.dashboard', compact(
        'customer',
        'monthlyOrderSummary',
        'monthlySquareMeterSummary',
        'monthlyOrdersByProduct',
        'monthlySquareMeterByProduct',
        'yearlyOrderSummary',
        'yearlySquareMeterSummary',
        'yearlyOrdersByProduct',
        'yearlySquareMeterByProduct'
    ));
}

}

