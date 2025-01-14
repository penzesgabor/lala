<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerProductPrice;
use App\Models\Product;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('deliveryAddresses')->get();
        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
            'contact_name' => 'required|string|max:255',
            'bank_account_nr' => 'required|string|max:255',
            'tax_number' => 'required|string|max:255',
            'booking_id' => 'required|string|max:255',
            'delivery_addresses' => 'array',
            'delivery_addresses.*.city' => 'required|string|max:255',
            'delivery_addresses.*.street' => 'required|string|max:255',
            'delivery_addresses.*.zip' => 'required|string|max:10',
        ]);

        $customer = Customer::create($validated);

        if ($request->has('delivery_addresses')) {
            foreach ($validated['delivery_addresses'] as $address) {
                $customer->deliveryAddresses()->create($address);
            }
        }

        return redirect()->route('customers.index')->with('success', 'Partner rögzítése sikeres volt.');
    }

    public function edit(Customer $customer)
    {
        $customer->load('deliveryAddresses');
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
            'contact_name' => 'required|string|max:255',
            'bank_account_nr' => 'required|string|max:255',
            'tax_number' => 'required|string|max:255',
            'booking_id' => 'required|string|max:255',
            'delivery_addresses' => 'array',
            'delivery_addresses.*.id' => 'nullable|exists:delivery_addresses,id',
            'delivery_addresses.*.city' => 'required|string|max:255',
            'delivery_addresses.*.street' => 'required|string|max:255',
            'delivery_addresses.*.zip' => 'required|string|max:10',
        ]);

        // Update customer details
        $customer->update($validated);

        // Get existing address IDs
        $existingAddresses = $customer->deliveryAddresses()->pluck('id')->toArray();

        // Update or create delivery addresses
        foreach ($validated['delivery_addresses'] as $address) {
            if (isset($address['id']) && in_array($address['id'], $existingAddresses)) {
                // Update existing address
                $customer->deliveryAddresses()->where('id', $address['id'])->update($address);
            } else {
                // Create new address
                $customer->deliveryAddresses()->create($address);
            }
        }

        // Soft delete removed addresses
        $newAddressIds = array_column($validated['delivery_addresses'], 'id');
        $addressesToSoftDelete = array_diff($existingAddresses, $newAddressIds);
        $customer->deliveryAddresses()->whereIn('id', $addressesToSoftDelete)->delete();

        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }



    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Partner törlése sikeres volt.');
    }

    public function updateCustomPrices(Request $request, Customer $customer)
    {
        foreach ($request->custom_prices as $productId => $customPrice) {
            if (!empty($customPrice)) {
                CustomerProductPrice::updateOrCreate(
                    ['customer_id' => $customer->id, 'product_id' => $productId],
                    ['custom_price' => $customPrice]
                );
            }
        }

        return redirect()->route('customers.edit', $customer)->with('success', 'Partner árainak módosítása sikeres volt.');
    }

    public function editPrices(Customer $customer)
    {
        $products = Product::all();
        return view('customers.prices', compact('customer', 'products'));
    }
    public function updatePrices(Request $request, Customer $customer)
    {
        foreach ($request->custom_prices as $productId => $customPrice) {
            if (!empty($customPrice)) {
                CustomerProductPrice::updateOrCreate(
                    ['customer_id' => $customer->id, 'product_id' => $productId],
                    ['custom_price' => $customPrice]
                );
            }
        }

        return redirect()->route('customers.prices.edit', $customer)->with('success', 'Árak módosítása sikeres volt.');
    }

    public function updateAllPrices(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'percentage' => 'required|numeric',
        ]);

        $percentageFactor = 1 + ($validated['percentage'] / 100);

        foreach ($customer->customPrices as $customPrice) {
            $customPrice->update([
                'custom_price' => $customPrice->custom_price * $percentageFactor,
            ]);
        }

        return redirect()->route('customers.prices.edit', $customer)
            ->with('success', 'Partner árainak módosítása sikeres volt.');
    }

    public function getDeliveryAddresses($customerId)
    {
        $deliveryAddresses = DeliveryAddress::where('customer_id', $customerId)->get(['id', 'street', 'city', 'zip']);
        return response()->json($deliveryAddresses);
    }

}

