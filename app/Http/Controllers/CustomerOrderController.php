<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    public function index(Customer $customer)
    {
        // Fetch all orders for the customer
        $orders = $customer->orders()->with('deliveryAddress')->get();

        return view('customers.orders.index', compact('customer', 'orders'));
    }
}
