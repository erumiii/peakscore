<?php

namespace App\Http\Controllers;

use App\Models\Hasil;
use App\Models\Jadwal;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PesertaController extends Controller
{
    public function index()
    {
        $jadwal = Jadwal::orderBy('mulai')->get();
        $dikerjakan = auth()->user()->hasil()->pluck('jadwalId');
        $aktif = $jadwal->first(fn ($j) => $j->status() === 'Ongoing' && !$dikerjakan->contains($j->id));
        return view('peserta.jadwal', compact('jadwal', 'dikerjakan', 'aktif'));
    }

    public function showTest($jadwalId)
    {
        $jadwal = Jadwal::findOrFail($jadwalId);
        if ($redirect = $this->guardJadwal($jadwal)) {
            return $redirect;
        }

        $soal = Soal::orderBy('kategori')->get();
        return view('peserta.test', compact('jadwal', 'soal'));
    }

    public function submitTest(Request $request, $jadwalId)
    {
        $jadwal = Jadwal::findOrFail($jadwalId);
        if ($redirect = $this->guardJadwal($jadwal)) {
            return $redirect;
        }

        $soal = Soal::all();
        $data = $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*' => 'required|in:A,B,C,D',
        ]);

        // Semua soal wajib dijawab
        if (array_diff($soal->pluck('soalId')->all(), array_keys($data['jawaban']))) {
            return back()->with('error', 'All questions must be answered.');
        }

        $skor = $this->hitungSkor($soal, $data['jawaban']);

        DB::transaction(function () use ($jadwal, $skor, $soal, $data) {
            // Unique(userId, jadwalId) sebagai pengaman kedua
            $hasil = Hasil::firstOrCreate(
                ['userId' => auth()->id(), 'jadwalId' => $jadwal->id],
                $skor
            );

            if ($hasil->wasRecentlyCreated) {
                $now = now();
                $rows = collect($soal)->map(fn ($s) => [
                    'jadwalId' => $jadwal->id,
                    'userId' => auth()->id(),
                    'soalId' => $s->soalId,
                    'opsiDipilih' => $data['jawaban'][$s->soalId],
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();
                \App\Models\Jawaban::insert($rows);
            }
        });

        return redirect('/')->with('success', 'Test submitted. The result awaits transcript publication.');
    }

    public function myResults()
    {
        $hasil = auth()->user()->hasil()->with('jadwal')->get();

        $totalFungsi = fn ($h) => collect([$h->skorVerbal, $h->skorNumerik, $h->skorLogika, $h->skorSpasial])->filter()->sum();
        $terbitTotals = $hasil->filter(fn ($h) => $h->diterbitkanPada)->map($totalFungsi);

        return view('peserta.results', [
            'hasil' => $hasil,
            'taken' => $hasil->count(),
            'best' => $terbitTotals->max() ?: null,
            'passed' => $terbitTotals->filter(fn ($t) => $t >= 700)->count(),
        ]);
    }

    private function guardJadwal(Jadwal $jadwal)
    {
        if (auth()->user()->hasil()->where('jadwalId', $jadwal->id)->exists()) {
            return redirect('/')->with('error', 'You have already taken this test.');
        }
        if ($jadwal->status() !== 'Ongoing') {
            return redirect('/')->with('error', 'Test locked. It can only be taken during its scheduled window.');
        }
        return null;
    }

    private function hitungSkor($soal, array $jawaban): array
    {
        $benar = ['Verbal' => 0, 'Numeric' => 0, 'Logic' => 0, 'Spatial' => 0];
        $jumlah = ['Verbal' => 0, 'Numeric' => 0, 'Logic' => 0, 'Spatial' => 0];

        foreach ($soal as $s) {
            $jumlah[$s->kategori]++;
            if (($jawaban[$s->soalId] ?? null) === $s->jawabanBenar) {
                $benar[$s->kategori]++;
            }
        }

        // Bobot sama rata: 4 kategori x 250 = 1000. Lulus jika total >= 700.
        return [
            'skorVerbal' => $jumlah['Verbal'] ? (int) round($benar['Verbal'] / $jumlah['Verbal'] * 250) : null,
            'skorNumerik' => $jumlah['Numeric'] ? (int) round($benar['Numeric'] / $jumlah['Numeric'] * 250) : null,
            'skorLogika' => $jumlah['Logic'] ? (int) round($benar['Logic'] / $jumlah['Logic'] * 250) : null,
            'skorSpasial' => $jumlah['Spatial'] ? (int) round($benar['Spatial'] / $jumlah['Spatial'] * 250) : null,
        ];
    }
}
