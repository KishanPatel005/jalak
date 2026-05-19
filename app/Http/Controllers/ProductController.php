<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('stocks')->latest()->get();
        return view('products', compact('products'));
    }

    public function create()
    {
        return view('manage_product');
    }

    public function edit(Request $request)
    {
        $product = Product::with('stocks')->findOrFail($request->id);
        return view('manage_product', compact('product'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();
        return redirect('/products')->with('success', 'Product deleted successfully!');
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|unique:products,code,' . $request->id,
            'gender' => 'required|in:male,female,children',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->id) {
            $product = Product::findOrFail($request->id);
            $imagePath = $product->image;
            if ($request->hasFile('image')) {
                if ($imagePath) Storage::disk('public')->delete($imagePath);
                $imagePath = $request->file('image')->store('products', 'public');
            }
            $product->update([
                'name' => $request->product_name,
                'code' => $request->product_code,
                'gender' => $request->gender,
                'image' => $imagePath,
                'note' => $request->note,
            ]);
            // Delete old stock to replace with new
            $product->stocks()->delete();
        } else {
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('products', 'public');
            }

            $product = Product::create([
                'name' => $request->product_name,
                'code' => $request->product_code,
                'gender' => $request->gender,
                'image' => $imagePath,
                'note' => $request->note,
            ]);
        }

        // Handing dynamic size inputs
        $sizes = ['XXS', 'XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL', '6XL', '7XL', '32', '34', '36', '38', '40', '42', '44', '46', '48'];

        foreach ($sizes as $size) {
            $qtyKey = 'qty_' . (string)$size;
            $rentKey = 'rent_' . (string)$size;
            $depositKey = 'deposit_' . (string)$size;

            if ($request->has($qtyKey) && $request->input($qtyKey) > 0) {
                ProductStock::create([
                    'product_id' => $product->id,
                    'size' => (string)$size,
                    'qty' => $request->input($qtyKey),
                    'rent_price' => $request->input($rentKey, 0),
                    'deposit_amount' => $request->input($depositKey, 0),
                ]);
            }
        }

        return redirect('/products')->with('success', 'Product ' . ($request->id ? 'updated' : 'added') . ' successfully!');
    }
}
