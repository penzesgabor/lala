<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerImportController extends Controller
{
    public function showImportForm()
    {
        return view('customers.import');
    }

    public function processImport(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
    
        dd($request);
        $file = $request->file('csv_file');
       # $data = array_map('str_getcsv', file($file->getRealPath()));
    
       $fileContent = file_get_contents($file->getRealPath());
       $fileContent = mb_convert_encoding($fileContent, 'UTF-8', 'ISO-8859-1'); // Adjust 'ISO-8859-1' as needed for your source encoding
       $lines = explode(PHP_EOL, $fileContent);

       $data = array_map(function ($line) {
        return str_getcsv($line, ';');
    }, file($file->getRealPath()));
        // Extract header row
        $header = array_map('strtolower', $data[0]);
        #dd($header);
        unset($data[0]); // Remove header row from data
    
        // Validate header structure
        $expectedHeader = ['name', 'city', 'street', 'zip', 'deliveryaddress_city', 'deliveryaddress_street', 'deliveryaddress_zip', 'phone', 'bank_account_nr', 'tax_number', 'booking_id'];

        if ($header !== $expectedHeader) {
            return back()->withErrors(['csv_file' => 'The CSV header does not match the expected format.']);
        }
    
        // Process each row
        foreach ($data as $row) {
            $row = array_combine($header, $row);
            #dd($row);
            // Validate each row
            $validator = Validator::make($row, [
                'name' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'street' => 'required|string|max:255',
                'zip' => 'required|string|max:10',
                'deliveryaddress_city' => 'nullable|string|max:255',
                'deliveryaddress_street' => 'nullable|string|max:255',
                'deliveryaddress_zip' => 'nullable|string|max:10',
                'phone' => 'nullable|string|max:15',
                'bank_account_nr' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:255',
                'booking_id' => 'nullable|string|max:255',
            ]);
    
            if ($validator->fails()) {
                continue; // Skip invalid rows
            }
    
            // Save Customer
            $customer = Customer::create([
                'name' => $row['name'],
                'city' => $row['city'],
                'street' => $row['street'],
                'zip' => $row['zip'],
                'phone' => $row['phone'] ?: "0",
                'contact_name' => '0',
                'bank_account_nr' => $row['bank_account_nr']  ?: "0",
                'tax_number' => $row['tax_number']  ?: "0",
                'booking_id' => $row['booking_id']  ?: "0",
            ]);
            #dd($customer);
            // Determine Delivery Address
            $deliveryAddressCity = $row['deliveryaddress_city'] ?: $row['city'];
            $deliveryAddressStreet = $row['deliveryaddress_street'] ?: $row['street'];
            $deliveryAddressZip = $row['deliveryaddress_zip'] ?: $row['zip'];
    
            // Save Delivery Address
            DeliveryAddress::create([
                'customer_id' => $customer->id,
                'city' => $deliveryAddressCity,
                'street' => $deliveryAddressStreet,
                'zip' => $deliveryAddressZip,
            ]);
        }
    
        return redirect()->route('customers.index')->with('success', 'Customers imported successfully.');
    }
    
}
