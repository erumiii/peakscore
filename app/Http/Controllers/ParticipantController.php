<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'peserta');

        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                    ->orWhere('username', 'like', "%{$q}%");
            });
        }

        $peserta = $query->withCount('hasil')->latest()->paginate(10);

        return view('participants.index', compact('peserta'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|regex:/^[A-Za-z0-9._-]+$/|unique:users,username',
            'password' => 'required|string|min:8',
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'role' => 'peserta',
        ]);

        return redirect()->route('participants.index')->with('success', 'Participant registered.');
    }

    public function update(Request $request, $id)
    {
        $peserta = User::where('role', 'peserta')->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|max:50|regex:/^[A-Za-z0-9._-]+$/|unique:users,username,' . $peserta->id,
            'password' => 'nullable|string|min:8',
        ]);

        $peserta->name = $data['name'];
        $peserta->username = $data['username'];
        if ($data['password'] !== null && $data['password'] !== '') {
            $peserta->password = bcrypt($data['password']);
        }
        $peserta->save();

        return redirect()->route('participants.index')->with('success', 'Participant updated.');
    }

    public function destroy($id)
    {
        $peserta = User::where('role', 'peserta')->findOrFail($id);

        // Riwayat hasil + rekam jawaban adalah data audit - jangan terhapus senyap
        if ($peserta->hasil()->exists()) {
            return redirect()->route('participants.index')
                ->with('error', 'Participant has test history and cannot be deleted.');
        }

        $peserta->delete();

        return redirect()->route('participants.index')->with('success', 'Participant deleted.');
    }
}
