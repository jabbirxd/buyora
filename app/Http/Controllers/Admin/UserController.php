<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roll_as' => 'required|integer|in:0,1',
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);
        return redirect()->route('admin.users.index')->with('status', 'User created successfully');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        
        $orders = \App\Models\Order::where('user_id', $user->id)
                    ->with('orderItems')
                    ->latest()
                    ->get();
                    
        $totalOrders = $orders->count();
        $totalAmountSpent = 0;
        
        foreach ($orders as $order) {
            foreach ($order->orderItems as $item) {
                $totalAmountSpent += ($item->quantity * $item->price);
            }
        }
        
        return view('admin.users.show', compact('user', 'orders', 'totalOrders', 'totalAmountSpent'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roll_as' => 'required|integer|in:0,1',
        ]);

        // An admin cannot demote their own account (they would lock themselves out).
        if ((int) $user->id === (int) auth()->id() && (int) $data['roll_as'] !== 1) {
            return back()->withInput()->withErrors(['roll_as' => 'You cannot remove admin access from your own account.']);
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return redirect()->route('admin.users.index')->with('status', 'User updated successfully');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // An admin cannot delete their own account.
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'User deleted successfully');
    }
}
