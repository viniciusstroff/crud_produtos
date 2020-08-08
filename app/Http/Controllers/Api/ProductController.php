<?php

namespace App\Http\Controllers\Api;

use App\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductsCreateOrEditRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index()
    {
        return new ProductCollection(Product::orderBy('id','DESC')->paginate(10));
    }

    public function search($field,$query)
    {
        return new ProductCollection(Product::where($field,'LIKE',"%$query%")->latest()->paginate(10));
    }


    public function store(ProductsCreateOrEditRequest $request)
    {
        $product= new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->save();

        return new ProductResource($product);
    }


    public function show($id)
    {
        return new ProductResource(Product::findOrFail($id));
    }


    public function update(ProductsCreateOrEditRequest $request, $id)
    {

        $product = Product::findOrfail($id);
        $product->name = $request->name;
        $product->price = $request->price;
        $product->save();
        return new ProductResource($product);
    }


    public function destroy($id)
    {
        $product = Product::findOrfail($id);
        $product->delete();
        return new ProductResource($product);
    }
}
