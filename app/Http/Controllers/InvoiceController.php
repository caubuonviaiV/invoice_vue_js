<?php

namespace App\Http\Controllers;

use App\Models\Counter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoice;
    public function __construct()
    {
        $this->invoice = new Invoice();
    }

    public function index()
    {
        try {
            return response()->json($this->invoice->with('customer')->orderBy('id', 'DESC')->get(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->get('s');
            if ($search != null) {
                $invoices = Invoice::with('customer')
                    ->where('id', 'LIKE', "%$search%")
                    ->get();
                return response()->json($invoices, 200);
            } else {
                return response()->json($this->invoice->with('customer')->orderBy('id', 'DESC')->get(), 200);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }


    public function create()
    {
        try {
            $counter = Counter::where('key', 'invoice')->first();
            // $ramdom = Counter::where('key', 'invoice')->first();
            $invoice =  $this->invoice->orderBy('id', 'ASC')->first();
            if ($invoice) {
                $invoice = $invoice->id + 1;
                $counters = $counter->value + $invoice;
            } else {
                $counters = $counter->value;
            }

            $formData = [
                'number' => $counter->prefix . $counters,
                'customer_id' => null,
                'customer' => null,
                'date' => date('Y-m-d'),
                'due_date' => null,
                'reference' => null,
                'discount' => 0,
                'term_and_condition' => 'Default Terms and Condition',
                'item' => [
                    [
                        'product_id' => null,
                        'product' => null,
                        'unit_price' => 0,
                        'quantity' => 1
                    ]
                ]
            ];
            return response()->json($formData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $invoiceItem = $request->input("invoice_item");

            $invoice_data = [
                // 'invoice_item' => json_decode($request->input('invoice_item'), true),
                // 'invoice_id' => $request->input('invoice_id'),
                'customer_id' => $request->input('customer_id'),
                'date' => $request->input('date'),
                'due_date' => $request->input('due_date'),
                'number' => $request->input('number'),
                'reference' => $request->input('reference'),
                'discount' => $request->input('discount'),
                'sub_total' => $request->input('subtotal'),
                'total' => $request->input('total'),
                'terms_and_conditions' => $request->input('terms_and_conditions'),
            ];

            $invoice = $this->invoice->create($invoice_data);
            foreach (json_decode($invoiceItem) as $item) {
                $itemdata['product_id'] = $item->id;
                $itemdata['invoice_id'] = $invoice->id;
                $itemdata['quantity'] = $item->quantity;
                $itemdata['unit_price'] = $item->unit_price;
                InvoiceItem::create($itemdata);
            }
            return response()->json($invoice, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e], 500);
        }
    }

    public function show($id)
    {
        try {
            $invoice = $this->invoice->with('customer', 'invoice_items.product')->find($id);
            if (!$invoice) {
                return response()->json(['error' => 'invoice not found'], 404);
            }
            return response()->json($invoice, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function edit($id)
    {
        try {
            $invoice = $this->invoice->with('customer', 'invoice_items.product')->find($id);
            if (!$invoice) {
                return response()->json(['error' => 'invoice not found'], 404);
            }
            return response()->json($invoice, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function update(Request $request, $id)
    {
        // Validate request data
        $request->validate([
            'customer_id' => 'required|integer',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'number' => 'required|string',
            'reference' => 'nullable|string',
            'discount' => 'nullable|numeric',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
            'terms_and_conditions' => 'nullable|string',
            'invoice_item' => 'required|string', // expecting JSON string
        ]);

        // Find invoice by ID
        $invoice = Invoice::findOrFail($id);

        // Update invoice fields
        $invoice->customer_id = $request->customer_id;
        $invoice->date = $request->date;
        $invoice->due_date = $request->due_date;
        $invoice->number = $request->number;
        $invoice->reference = $request->reference;
        $invoice->discount = $request->discount;
        $invoice->sub_total = $request->subtotal;
        $invoice->total = $request->total;
        $invoice->terms_and_conditions = $request->terms_and_conditions;

        // Save updated invoice
        $invoice->save();

        // Decode invoice items JSON string
        $invoiceItems = json_decode($request->invoice_item, true);

        // Delete existing invoice items
        $invoice->invoice_items()->delete();

        // Save new invoice items
        foreach ($invoiceItems as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['id'],
                'unit_price' => $item['unit_price'],
                'quantity' => $item['quantity'],
            ]);
        }

        return response()->json(['message' => 'Invoice updated successfully']);
    }
    public function destroy(String $id)
    {
        try {
            $invoice = Invoice::firstOrFail($id);
            // $invoice = $this->invoice->find($id);
            if (!$invoice) {
                return response()->json(['error' => 'invoice not found'], 404);
            }

            $invoice->invoice_items()->delete();
            $invoice->delete();
            return response()->json(['message' => 'invoice deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
    public function destroy_items(String $id)
    {
        try {
            $invoice = Invoice::firstOrFail($id);
            if (!$invoice) {
                return response()->json(['error' => 'invoice not found'], 404);
            }
            $invoice->delete();
            return response()->json(['message' => 'invoice deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
