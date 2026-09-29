<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pengguna.index', compact('users'));
    }

    public function create()
    {
        return view('admin.pengguna.create');
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);
        ActivityLog::record('user.create', "Menambah user {$user->email}");

        return redirect()->route('admin.pengguna.index')->with('success', "Akun {$user->name} berhasil dibuat.");
    }

    public function edit(User $pengguna)
    {
        return view('admin.pengguna.edit', ['user' => $pengguna]);
    }

    public function update(UpdateUserRequest $request, User $pengguna)
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $pengguna->update($data);
        ActivityLog::record('user.update', "Mengubah user {$pengguna->email}");

        return redirect()->route('admin.pengguna.index')->with('success', "Akun {$pengguna->name} berhasil diperbarui.");
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun sendiri.');
        }

        $pengguna->update(['status' => 'inactive']);
        ActivityLog::record('user.deactivate', "Menonaktifkan user {$pengguna->email}");

        return redirect()->route('admin.pengguna.index')->with('success', "Akun {$pengguna->name} berhasil dinonaktifkan.");
    }
}
