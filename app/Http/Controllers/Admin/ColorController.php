<?php

namespace App\Http\Controllers\Admin;

use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ColorFormRequest;

class ColorController extends Controller
{
    public function index()  
    {
        $colors = Color::all();
        return view('admin.colors.index',compact('colors'));
    }
     public function create()  
    {
        return view('admin.colors.create');
    }
    public function store(ColorFormRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['status'] = 0; // new colours start Visible
        Color::create($validatedData);
         return redirect('admin/colors')->with('message', 'Color Added Successfully');
    }
    public function edit(Color $color)  
    {
        return view('admin.colors.edit',compact('color'));
    }
    public function update(ColorFormRequest $request,$color_id)
    {
        $validatedData = $request->validated();
        unset($validatedData['status']); // editing must never change Visible/Hidden
        Color::findOrFail($color_id)->update($validatedData);
        return redirect('admin/colors')->with('message', 'Color Updated Successfully');
    }

    /** One-click Visible/Hidden switch (status '1' = Hidden, '0' = Visible). */
    public function toggleStatus($color_id)
    {
        $color = Color::findOrFail($color_id);
        $color->status = $color->status == '1' ? 0 : 1;
        $color->save();

        return redirect('admin/colors')->with('message', 'Color is now ' . ($color->status == 1 ? 'Hidden' : 'Visible'));
    }
    public function destroy($color_id)  
    {
        $color = Color::findOrFail($color_id);
        $color->delete();
        return redirect('admin/colors')->with('message', 'Color Deleted Successfully');
    }
}
