<?php

namespace App\Http\Controllers;

use App\Models\CuttingList;
use App\Models\OrderProduct;
use Illuminate\Http\Request;

class CuttingListController extends Controller
{
    public function index()
    {
        $cuttingLists = CuttingList::all();
        #$orderProducts = \App\Models\OrderProduct::whereDoesntHave('cuttingSelections')->get();
        $orderProducts = \App\Models\OrderProduct::whereNotIn('id', function ($query) {
            $query->select('order_product_id')->from('cutting_selections');
        })->get();
        return view('cutting_lists.index', compact('cuttingLists', 'orderProducts'));

    }

    public function create()
    {
        $today = now()->format('Y-m-d');
        $dailyNumber = \App\Models\CuttingList::whereDate('list_date', $today)->count() + 1;

        $orders = \App\Models\Order::with('customer')
            ->whereHas('orderProducts', function ($query) {
                $query->whereDoesntHave('cuttingSelections');
            })->get();

        return view('cutting_lists.create', compact('orders', 'dailyNumber'));
    }



    public function secondStep(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
        ]);

        $orderProducts = \App\Models\OrderProduct::whereIn('order_id', $request->order_ids)
        ->whereNotIn('id', function ($query) {
            $query->select('order_product_id')->from('cutting_selections');
        })
        ->select('order_id', 'width', 'height', 'product_id', \DB::raw('COUNT(*) as quantity'))
        ->groupBy('order_id', 'width', 'height', 'product_id')
        ->with(['order', 'product'])
        ->get();
    
     
        $nextSevenDays = collect(range(0, 6))->map(function ($day) {
            return now()->addDays($day)->format('Y-m-d');
        });

        return view('cutting_lists.second-step', compact('orderProducts', 'nextSevenDays'));
    }


    public function store(Request $request)
{
    
    #$request->validate([
    #    'order_ids' => 'required|array',
    #    'cutting_dates' => 'required|array',
    #    'cutting_dates.*' => 'nullable|date',
    #]);
    
    // Create a new cutting list
    $cuttingList = CuttingList::create(['list_date' => now()]);
    

    $unprocessedItems = []; // Track items with missing dates
   
    foreach ($request->cutting_dates as $key => $cuttingDate) {
        // Track items without a cutting date
        if (empty($cuttingDate)) {
            $unprocessedItems[] = $key;
            continue;
        }

        // Extract order details from the key
        [$orderId, $width, $height] = explode('_', $key);

        // Fetch order products that match the order, width, and height
        $orderProducts = \App\Models\OrderProduct::where('order_id', $orderId)
            ->where('width', $width)
            ->where('height', $height)
            ->get();
        #dd($orderProducts);     
        // Save each matching product to the cutting list
        foreach ($orderProducts as $orderProduct) {
            $cuttingList->selections()->create([
                'order_product_id' => $orderProduct->id,
                'cutting_date' => $cuttingDate,
            ]);
        }
    }

    // Handle unprocessed items
    if (!empty($unprocessedItems)) {
        session()->flash('warning', 'Some products were not added to the cutting list due to missing cutting dates.');
    }

    return redirect()->route('cutting_lists.index')->with('success', 'Cutting list created successfully.');
}



public function show(CuttingList $cuttingList)
{
    $selections = $cuttingList->selections()
        ->with(['orderProduct.product.components', 'orderProduct.order.customer'])
        ->get()
        ->groupBy(function ($selection) {
            return $selection->orderProduct->width . '_' . $selection->orderProduct->height . '_' . $selection->orderProduct->product_id;
        });

    $groupedSelections = $selections->map(function ($group) {
        $product = $group->first()->orderProduct->product;

        $groupedComponents = collect();

        if ($product->type === 'configurable') {
            // Group components by their ID
            $groupedComponents = $product->components
                ->groupBy('id')
                ->map(function ($componentGroup) use ($group) {
                    $totalComponentQuantity = $componentGroup->sum('pivot.quantity');
                    $productCount = $group->count();

                    return [
                        'name' => $componentGroup->first()->name,
                        'totalQuantity' => $totalComponentQuantity,
                        'averageQuantity' => $productCount > 0 ? $totalComponentQuantity / $productCount : 0,
                    ];
                });
        }

        return [
            'width' => $group->first()->orderProduct->width,
            'height' => $group->first()->orderProduct->height,
            'product' => $product,
            'customers' => $group->pluck('orderProduct.order.customer.name')->unique(),
            'orderIds' => $group->pluck('orderProduct.order.id')->unique(),
            'totalQuantity' => $group->count(), // Count all items in the group
            'components' => $groupedComponents,
        ];
    });

    return view('cutting_lists.show', compact('cuttingList', 'groupedSelections'));
}




}
