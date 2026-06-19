<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Support\AdminPermissions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = Admin::orderBy('name')->get();
        return view('admin.admin-users.index', compact('admins'));
    }

    public function create()
    {
        $roles = AdminPermissions::ROLES;
        return view('admin.admin-users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:admins,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', Rule::in(AdminPermissions::ROLES)],
        ]);

        Admin::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);

        auth('admin')->user()->log('created_admin', 'Admin', null, ['email' => $data['email'], 'role' => $data['role']]);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin account created.');
    }

    public function edit(Admin $adminUser)
    {
        $roles = AdminPermissions::ROLES;
        return view('admin.admin-users.edit', compact('adminUser', 'roles'));
    }

    public function update(Request $request, Admin $adminUser)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', Rule::unique('admins', 'email')->ignore($adminUser->id)],
            'role'     => ['required', Rule::in(AdminPermissions::ROLES)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $adminUser->name  = $data['name'];
        $adminUser->email = $data['email'];
        $adminUser->role  = $data['role'];

        if (!empty($data['password'])) {
            $adminUser->password = Hash::make($data['password']);
        }

        $adminUser->save();

        auth('admin')->user()->log('updated_admin', 'Admin', $adminUser->id, ['role' => $data['role']]);

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin account updated.');
    }

    public function destroy(Admin $adminUser)
    {
        if ($adminUser->id === auth('admin')->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        auth('admin')->user()->log('deleted_admin', 'Admin', $adminUser->id, ['email' => $adminUser->email]);

        $adminUser->delete();

        return redirect()->route('admin.admin-users.index')->with('success', 'Admin account deleted.');
    }
}
