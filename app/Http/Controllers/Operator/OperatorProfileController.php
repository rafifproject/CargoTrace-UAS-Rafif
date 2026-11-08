<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class OperatorProfileController extends Controller
{
    /** Mockup 16: Profil & Pengaturan Akun */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('operator.profile.index', compact('user'));
    }

    /** Simpan perubahan profil */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:150'],
            'phone'                 => ['nullable', 'string', 'max:30'],
            'company_name'          => ['nullable', 'string', 'max:150'],
            'password'              => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required'         => 'Nama lengkap wajib diisi.',
            'password.min'          => 'Kata sandi minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user->name         = $validated['name'];
        $user->phone        = $validated['phone'] ?? $user->phone;
        $user->company_name = $validated['company_name'] ?? $user->company_name;

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'UPDATE',
            'entity_type' => 'User',
            'entity_id'   => (string) $user->id,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
            'description' => "Operator {$user->name} memperbarui data profil akun.",
            'new_values'  => ['name' => $validated['name'], 'phone' => $validated['phone']],
            'created_at'  => now(),
        ]);

        return back()->with('status', 'Profil akun berhasil diperbarui.');
    }
}

