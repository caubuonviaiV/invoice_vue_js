<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customer;
    public function __construct()
    {
        $this->customer = new Customer();
    }

    public function index()
    {
        try {
            return response()->json($this->customer->orderBy('id', 'ASC')->get(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
