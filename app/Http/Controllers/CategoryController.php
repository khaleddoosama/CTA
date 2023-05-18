<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    public function index()
    {
        $categories = Category::all();
        return view('pages.category.index', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        try {
            Category::create($request->all());

            toastr()->success('تم إضافة القسم بنجاح');

            return redirect()->route('category.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


   

    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required'
        ]);
        try {
            $category = Category::findOrFail($request->id);
            $category->update($request->all());

            toastr()->success('تم تعديل القسم بنجاح');

            return redirect()->route('category.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    public function destroy(Request $request)
    {
        try {
            // check if category has products
            $category = Category::findOrFail($request->id);
            if ($category->products->count() > 0) {
                toastr()->error('لا يمكن حذف القسم لوجود منتجات مرتبطة به');

                return redirect()->route('category.index');
            } else {
                $category = Category::findOrFail($request->id);
                $category->delete();

                toastr()->success('تم حذف القسم بنجاح');

                return redirect()->route('category.index');
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
