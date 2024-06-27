<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{

    protected $product;
    public function __construct()
    {
        $this->product = new Product();
    }
    public function index()
    {
        try {
            return response()->json($this->product->orderBy('id', 'ASC')->get(), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal Server Error'], 500);
        }
    }
}
