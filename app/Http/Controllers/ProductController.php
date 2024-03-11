<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return response()->json($products);
    }

    public function store(Request $request)
{
    $validatedData = $request->validate([
        'product_name' => 'required',
        'product_price' => 'required|numeric',
        'description' => 'required',
    ], [
        'product_name.required' => 'Ürün adı alanı boş bırakılamaz.',
        'product_price.required' => 'Ürün fiyatı alanı boş bırakılamaz.',
        'product_price.numeric' => 'Ürün fiyatı sadece sayısal bir değer olmalıdır.',
        'description.required' => 'Açıklama alanı boş bırakılamaz.',
    ]);

    $product = new Product([
        'product_name' => $validatedData['product_name'],
        'product_price' => $validatedData['product_price'],
        'description' => $validatedData['description'],
    ]);

    if ($product->save()) {
        return response()->json([
            'status' => 'success',
            'message' => 'Ürün başarıyla oluşturuldu.',
            'data' => $product,
        ], 201);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Ürün oluşturulurken bir hata oluştu.'
        ], 500);
    }
}

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'product_name' => 'required',
            'product_price' => 'required|numeric',
            'description' => 'required',
        ], [
            'product_price.numeric' => 'Ürün fiyatı sadece sayısal değerler içermelidir.',
            'product_name.required' => 'Ürün adı alanı boş bırakılamaz.',
            'product_price.required' => 'Ürün fiyatı alanı boş bırakılamaz.',
            'description.required' => 'Ürün açıklama alanı boş bırakılamaz.',
        ]);

        $product->update($validatedData);

        return response()->json($product, 200);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json(null, 204);
    }
}
