<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Soal;
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
        if ($error = $this->bankSoalKurang()) {
            return back()->withErrors(['soal' => $error])->withInput();
        }
        Jadwal::create($data);

        return redirect()->route('schedules.index')->with('success', 'Schedule created.');
    }

    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $data = $this->validated($request);
        if ($error = $this->bankSoalKurang()) {
            return back()->withErrors(['soal' => $error])->withInput();
        }
        $jadwal->update($data);

        return redirect()->route('schedules.index')->with('success', 'Schedule updated.');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);

        if ($jadwal->hasil()->exists()) {
            return redirect()->route('schedules.index')
                ->with('error', 'Schedule has recorded test results and cannot be deleted.');
        }

        $jadwal->delete();

        return redirect()->route('schedules.index')->with('success', 'Schedule deleted.');
    }

    private function bankSoalKurang(): ?string
    {
        $jumlah = Soal::count();
        if ($jumlah >= 4) {
            return null;
        }

        return "The question bank needs at least 4 soal before a schedule can be saved. Currently: {$jumlah}.";
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
