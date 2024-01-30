<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        return Product::select('id', 'name', 'description', 'price', 'image')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'required',
            'price'       => 'required|numeric',
            'image'       => 'required|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $file_path = $request->image->store('product', 'public');

        $product = new Product;
        $product->name        = $request->name;
        $product->description = $request->description;
        $product->price       = $request->price;
        $product->image       = $file_path;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product has been created!'
        ]);
    }

    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'product' => $product
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required',
            'description' => 'required',
            'price'       => 'required|numeric',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        $product->name        = $request->name;
        $product->description = $request->description;
        $product->price       = $request->price;

        if ($request->hasFile('image')) {
            // hapus gambar lama
            $exists = Storage::disk('public')->exists($product->image);
            if ($exists) {
                Storage::disk('public')->delete($product->image);
            }

            // upload gambar baru
            $file_path = $request->image->store('product', 'public');

            $product->image = $file_path;
        }

        $product->update();

        return response()->json([
            'success' => true,
            'message' => 'Product has been updated!'
        ]);
    }

    public function destroy(Product $product)
    {
        // hapus gambar
        $exists = Storage::disk('public')->exists($product->image);
        if ($exists) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product has been deleted!'
        ]);
    }
}
