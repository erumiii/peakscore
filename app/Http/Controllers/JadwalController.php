<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::withCount('hasil')->orderBy('mulai', 'desc')->paginate(10);
        return view('schedules.index', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Jadwal::create($data);
        return redirect()->route('schedules.index')->with('success', 'Schedule created.');
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->update($this->validated($request));
        return redirect()->route('schedules.index')->with('success', 'Schedule updated.');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();
        return redirect()->route('schedules.index')->with('success', 'Schedule deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'judul' => 'required|max:150',
            'deskripsi' => 'nullable',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after:mulai',
        ]);
    }
}
