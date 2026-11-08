<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $users = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:ADMINISTRATOR,LOGISTICS_OPERATOR,CUSTOMER'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'entity_type' => 'User',
            'entity_id' => (string) $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Pengguna baru dibuat oleh Admin: {$user->name} dengan peran {$user->role}",
            'new_values' => ['email' => $user->email, 'role' => $user->role],
            'created_at' => now(),
        ]);

        return back()->with('status', 'Pengguna berhasil dibuat.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:ADMINISTRATOR,LOGISTICS_OPERATOR,CUSTOMER'],
            'status' => ['required', 'in:ACTIVE,INACTIVE'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', Password::defaults()],
        ]);

        $dataToUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $dataToUpdate['password'] = Hash::make($validated['password']);
        }

        $oldValues = $user->only(['name', 'email', 'role', 'status']);
        $user->update($dataToUpdate);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'entity_type' => 'User',
            'entity_id' => (string) $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Admin memperbarui data pengguna: {$user->name}",
            'old_values' => $oldValues,
            'new_values' => $dataToUpdate,
            'created_at' => now(),
        ]);

        return back()->with('status', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang login.']);
        }

        $name = $user->name;
        $user->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'entity_type' => 'User',
            'entity_id' => (string) $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => "Akun pengguna {$name} telah dihapus.",
            'created_at' => now(),
        ]);

        return back()->with('status', 'Akun pengguna berhasil dihapus.');
    }
}

