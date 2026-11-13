<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);

        return view('admin.contact-messages.index', compact('messages'));
    }

    public function show($id)
    {
        $contactMessage = ContactMessage::findOrFail($id);

        return view('admin.contact-messages.show', compact('contactMessage'));
    }

    public function destroy($id)
    {
        $contactMessage = ContactMessage::findOrFail($id);
        $contactMessage->delete();

        return redirect('admin/contact-messages')->with('message', 'Message Deleted Successfully');
    }
}
