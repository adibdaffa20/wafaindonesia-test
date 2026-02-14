<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationDashboardController extends Controller
{
    // LIST + SEARCH (q)
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Registration::query()->latest();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('no_wa', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('nama_lembaga', 'like', "%{$q}%");
            });
        }

        if ($request->filled('date')) {
        $query->whereDate('created_at', $request->date);
        } elseif ($request->filled('month') && $request->filled('year')) {
            $query->whereMonth('created_at', $request->month)
                ->whereYear('created_at', $request->year);
        } elseif ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $leads = $query->paginate(10)->withQueryString();

        return response()->json($leads);
    }

    // DETAIL (optional)
    public function show(Registration $registration)
    {
        return response()->json($registration);
    }

    // UPDATE
    public function update(Request $request, Registration $registration)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'no_wa' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255', 'unique:registrations,email,' . $registration->id],
            'nama_lembaga' => ['required', 'string', 'max:255'],
        ]);

        // simpan data lama untuk deskripsi log (lebih informatif)
        $before = $registration->only(['nama', 'no_wa', 'email', 'nama_lembaga']);

        $registration->update($validated);

        // Activity Log
        if (function_exists('activity_log')) {
            $desc = "Update Lead #{$registration->id} | "
                . "Nama: {$before['nama']} → {$registration->nama}, "
                . "WA: {$before['no_wa']} → {$registration->no_wa}, "
                . "Email: {$before['email']} → {$registration->email}, "
                . "Lembaga: {$before['nama_lembaga']} → {$registration->nama_lembaga}";

            activity_log('update', $desc);
        }

        return response()->json([
            'message' => 'Data berhasil diupdate.',
            'data' => $registration,
        ]);
    }

    // DELETE
    public function destroy(Registration $registration)
    {
        // ambil info dulu sebelum delete (buat log)
        $snap = $registration->only(['nama', 'no_wa', 'email', 'nama_lembaga']);
        $id = $registration->id;

        $registration->delete();

        if (function_exists('activity_log')) {
            $desc = "Hapus Lead #{$id} | "
                . "Nama: {$snap['nama']}, WA: {$snap['no_wa']}, Email: {$snap['email']}, Lembaga: {$snap['nama_lembaga']}";
            activity_log('delete', $desc);
        }

        return response()->json([
            'message' => 'Data berhasil dihapus.',
        ]);
    }
}
