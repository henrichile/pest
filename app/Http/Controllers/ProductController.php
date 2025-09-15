<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return view("products.index", compact("products"));
    }

    public function create()
    {
        return view("products.create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "active_ingredient" => "required|string|max:255",
            "service_type" => "required|in:desratizacion,desinsectacion,sanitizacion",
            "sag_registration" => "nullable|string|max:100",
            "isp_registration" => "nullable|string|max:100",
            "stock" => "required|integer|min:0",
            "unit" => "required|string|max:50",
            "description" => "nullable|string",
        ]);

        Product::create($request->all());

        return redirect()->route("admin.products.index")->with("success", "Producto creado exitosamente");
    }

    public function show(Product $product)
    {
        return view("products.show", compact("product"));
    }

    public function edit(Product $product)
    {
        return view("products.edit", compact("product"));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            "name" => "required|string|max:255",
            "active_ingredient" => "required|string|max:255",
            "service_type" => "required|in:desratizacion,desinsectacion,sanitizacion",
            "sag_registration" => "nullable|string|max:100",
            "isp_registration" => "nullable|string|max:100",
            "stock" => "required|integer|min:0",
            "unit" => "required|string|max:50",
            "description" => "nullable|string",
        ]);

        $product->update($request->all());

        return redirect()->route("admin.products.index")->with("success", "Producto actualizado exitosamente");
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route("admin.products.index")->with("success", "Producto eliminado exitosamente");
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            "stock" => "required|integer|min:0",
        ]);

        $product->update(["stock" => $request->stock]);

        return redirect()->back()->with("success", "Stock actualizado exitosamente");
    }
}
