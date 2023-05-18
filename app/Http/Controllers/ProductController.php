<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::all();
        return view('pages.product.index', compact('products'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('pages.product.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'selling_price' => 'required',
            'description' => 'required',
            'quantity' => 'required',
            'image' => 'required',
        ]);
        try {
            // image
            $image = $request->file('image');
            $image_name = $request->name . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $image_name);

            //slug
            $slug = str_replace(' ', '-', $request->name);
            $Product = Product::create([
                'name' => $request->name,
                'slug' => $slug,
                'code' => $request->code,
                'selling_price' => $request->selling_price,
                'discount_price' => $request->discount_price,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'image' => $image_name,
                'status' => '1',
            ]);

            toastr()->success('تم إضافة المنتج بنجاح');
            return redirect()->route('product.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    // change status
    public function changeStatus(string $id)
    {
        try {
            $product = Product::find($id);
            if ($product->status == '1') {
                $product->update(['status' => '0']);
            } else {
                $product->update(['status' => '1']);
            }
            toastr()->success('تم تغيير حالة المنتج بنجاح');
            return redirect()->route('product.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function edit(string $id)
    {
        $product = Product::find($id);
        $categories = Category::all();
        return view('pages.product.edit', compact('product', 'categories'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'selling_price' => 'required',
            'category_id' => 'required | exists:categories,id',
            'description' => 'required',
            'quantity' => 'required',
        ]);

        try {
            $product = Product::find($id);
            $image_name = $product->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image_name = $request->name . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/products'), $image_name);
                // remove old image
                if (file_exists(public_path('uploads/products/' . $product->image))) {
                    unlink(public_path('uploads/products/' . $product->image));
                }
            }

            //slug
            $slug = str_replace(' ', '-', $request->name);
            $product->update([
                'name' => $request->name,
                'slug' => $slug,
                'code' => $request->code,
                'selling_price' => $request->selling_price,
                'discount_price' => $request->discount_price,
                'category_id' => $request->category_id,
                'description' => $request->description,
                'quantity' => $request->quantity,
                'image' => $image_name,
                'status' => '1',
            ]);

            toastr()->success('تم تعديل المنتج بنجاح');
            return redirect()->route('product.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $product = Product::find($request->id);
            // remove old image
            if (file_exists(public_path('uploads/products/' . $product->image))) {
                unlink(public_path('uploads/products/' . $product->image));
            }
            $product->delete();
            toastr()->success('تم حذف المنتج بنجاح');
            return redirect()->route('product.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
