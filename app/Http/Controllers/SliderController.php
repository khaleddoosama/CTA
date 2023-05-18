<?php

namespace App\Http\Controllers;

use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{

    public function index()
    {
        $sliders = Slider::all();
        return view('pages.slider.index', compact('sliders'));
    }


    public function create()
    {
        return view('pages.slider.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required',
        ]);

        try {
            // image
            $image = $request->file('image');
            $image_name = $request->title . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/sliders'), $image_name);

            //slug
            $slider = Slider::create([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $image_name,
                'status' => '1',
            ]);

            toastr()->success('تم إضافة السلايدر بنجاح');
            return redirect()->route('slider.index');
        } catch (\Exception $e) {
            toastr()->error('هناك خطأ ما');
            return redirect()->route('slider.index');
        }
    }


    // change status
    public function changeStatus(string $id)
    {
        try {
            $slider = Slider::findOrFail($id);
            if ($slider->status == '1') {
                $slider->update(['status' => '0']);
            } else {
                $slider->update(['status' => '1']);
            }
            toastr()->success('تم تفعيل السلايدر بنجاح');
            return redirect()->route('slider.index');
        } catch (\Exception $e) {
            toastr()->error('هناك خطأ ما');
            return redirect()->route('slider.index');
        }
    }


    public function edit(string $id)
    {
        $slider = Slider::findOrFail($id);
        return view('pages.slider.edit', compact('slider'));
    }


    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        try {
            $slider = Slider::findOrFail($id);
            $image_name = $slider->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $image_name = $request->title . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/sliders'), $image_name);
                // remove old image
                if (file_exists(public_path('uploads/sliders/' . $slider->image))) {
                    unlink(public_path('uploads/sliders/' . $slider->image));
                }
            }

            $slider->update([
                'title' => $request->title,
                'description' => $request->description,
                'image' => $image_name,
            ]);

            toastr()->success('تم تعديل السلايدر بنجاح');
            return redirect()->route('slider.index');
        } catch (\Exception $e) {
            toastr()->error('هناك خطأ ما');
            return redirect()->route('slider.index');
        }
    }


    public function destroy(Request $request)
    {
        try {
            $slider = Slider::findOrFail($request->id);
            // remove old image
            if (file_exists(public_path('uploads/sliders/' . $slider->image))) {
                unlink(public_path('uploads/sliders/' . $slider->image));
            }
            $slider->delete();
            toastr()->success('تم حذف السلايدر بنجاح');
            return redirect()->route('slider.index');
        } catch (\Exception $e) {
            toastr()->error('هناك خطأ ما');
            return redirect()->route('slider.index');
        }
    }
}
