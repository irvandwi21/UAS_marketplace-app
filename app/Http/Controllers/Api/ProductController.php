<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // GET ALL
    public function index()
    {
        return response()->json(
            Product::with('category')->get()
        );
    }

    // STORE
       public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required',
        'name' => 'required',
        'description' => 'required',
        'price' => 'required',
        'stock' => 'required',
        'image' => 'nullable|image'
    ]);

    $imageName = null;

    if ($request->hasFile('image')) {

        $imageName = time().'.'.$request->image->extension();

        $request->image->move(
            public_path('products'),
            $imageName
        );
    }

    $product = Product::create([
        'category_id' => $request->category_id,
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'image' => $imageName
    ]);

    return response()->json([
        'message' => 'Produk berhasil ditambahkan',
        'data' => $product
    ]);
}

    // SHOW
    public function show($id)
    {
        return response()->json(
            Product::with('category')->findOrFail($id)
        );
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->update($request->all());

        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ]);
    }

    // DELETE
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}