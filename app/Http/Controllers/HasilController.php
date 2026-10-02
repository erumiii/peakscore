<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\Jawaban;

class HasilController extends Controller
{
    public function index()
    {
        $hasil = Hasil::with(['user', 'jadwal'])->latest()->paginate(10);

        $totalHasil = Hasil::count();
        $lulus = (int) Hasil::selectRaw('SUM(CASE WHEN COALESCE(skorVerbal,0)+COALESCE(skorNumerik,0)+COALESCE(skorLogika,0)+COALESCE(skorSpasial,0) >= 700 THEN 1 ELSE 0 END) as lulus')->first()->lulus;
        $passRate = $totalHasil ? (int) round($lulus / $totalHasil * 100) : 0;

        return view('results.index', compact('hasil', 'totalHasil', 'lulus', 'passRate'));
    }

    public function show($id)
    {
        $hasil = Hasil::with(['user', 'jadwal'])->findOrFail($id);
        $jawaban = Jawaban::with('soal')
            ->where('userId', $hasil->userId)
            ->where('jadwalId', $hasil->jadwalId)
            ->get();

        return view('results.show', compact('hasil', 'jawaban'));
    }

    public function publish($id)
    {
        $hasil = Hasil::findOrFail($id);
        if (!$hasil->diterbitkanPada) {
            $hasil->update(['diterbitkanPada' => now()]);
        }
        return redirect()->route('results.index')->with('success', 'Transcript published.');
    }

    public function transcript($id)
    {
        $hasil = Hasil::with(['user', 'jadwal'])->findOrFail($id);
        $user = auth()->user();
        if (!$user->isAdmin() && $hasil->userId !== $user->id) {
            abort(403);
        }
        if (!$hasil->diterbitkanPada) {
            abort(403);
        }
        return view('results.transcript', compact('hasil'));
    }
}
