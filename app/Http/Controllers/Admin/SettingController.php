<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('admin.setting.index', compact('setting'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'website_name' => 'nullable|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'page_title' => 'nullable|string|max:255',
            'meta_keyword' => 'nullable|string|max:1000',
            'meta_description' => 'nullable|string|max:1000',
            'address' => 'nullable|string|max:1000',
            'phone1' => 'nullable|string|max:30',
            'phone2' => 'nullable|string|max:30',
            'email1' => 'nullable|email|max:255',
            'email2' => 'nullable|email|max:255',
        ]);

        $setting = Setting::first();

        if ($setting) {
            $setting->update([
                'website_name' => $request->website_name,
                'website_url' => $request->website_url,
                'page_title' => $request->page_title,
                'meta_keyword' => $request->meta_keyword,
                'meta_description' => $request->meta_description,
                'address' => $request->address,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'email1' => $request->email1,
                'email2' => $request->email2,
            ]);
            return redirect()->back()->with('message', 'Settings Updated Successfully');
        } else {
            Setting::create([
                'website_name' => $request->website_name,
                'website_url' => $request->website_url,
                'page_title' => $request->page_title,
                'meta_keyword' => $request->meta_keyword,
                'meta_description' => $request->meta_description,
                'address' => $request->address,
                'phone1' => $request->phone1,
                'phone2' => $request->phone2,
                'email1' => $request->email1,
                'email2' => $request->email2,
            ]);
            return redirect()->back()->with('message', 'Settings Created Successfully');
        }
    }
}
