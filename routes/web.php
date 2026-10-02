<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HasilController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\SoalController;
use App\Models\Hasil;
use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

Route::get('/', function () {
    if (auth()->user()->isPeserta()) {
        $jadwal = Jadwal::orderBy('mulai')->get();
        $dikerjakan = auth()->user()->hasil()->pluck('jadwalId');
        $aktif = $jadwal->first(fn ($j) => $j->status() === 'Ongoing' && !$dikerjakan->contains($j->id));
        return view('peserta.jadwal', compact('jadwal', 'dikerjakan', 'aktif'));
    }

    $totalHasil = Hasil::count();
    $lulus = (int) Hasil::selectRaw('SUM(CASE WHEN COALESCE(skorVerbal,0)+COALESCE(skorNumerik,0)+COALESCE(skorLogika,0)+COALESCE(skorSpasial,0) >= 700 THEN 1 ELSE 0 END) as lulus')->first()->lulus;

    return view('index', [
        'totalJadwal' => Jadwal::count(),
        'totalSoal' => Soal::count(),
        'totalPeserta' => User::where('role', 'peserta')->count(),
        'aktivitas' => Hasil::with(['user', 'jadwal'])->latest()->limit(5)->get(),
        'totalHasil' => $totalHasil,
        'passRate' => $totalHasil ? (int) round($lulus / $totalHasil * 100) : 0,
        'soalPerKategori' => Soal::selectRaw('kategori, COUNT(*) as jumlah')->groupBy('kategori')->pluck('jumlah', 'kategori'),
    ]);
})->middleware('auth')->name('dashboard');

Route::middleware(['auth', 'role:peserta'])->group(function () {
    Route::get('/test/{jadwalId}', [PesertaController::class, 'showTest'])->name('peserta.test.show');
    Route::post('/test/{jadwalId}', [PesertaController::class, 'submitTest'])->name('peserta.test.submit');
    Route::get('/my-results', [PesertaController::class, 'myResults'])->name('peserta.results');
});

Route::get('/test-results/{id}/transcript', [HasilController::class, 'transcript'])
    ->middleware('auth')->name('results.transcript');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/questions', [SoalController::class, 'index'])->name('questions.index');
    Route::get('/questions/add', [SoalController::class, 'create'])->name('questions.create');
    Route::post('/questions/add', [SoalController::class, 'store'])->name('questions.store');
    Route::get('/questions/{id}/edit', [SoalController::class, 'edit'])->name('questions.edit');
    Route::put('/questions/{id}', [SoalController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{id}', [SoalController::class, 'destroy'])->name('questions.destroy');

    Route::get('/schedules', [JadwalController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [JadwalController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{id}', [JadwalController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [JadwalController::class, 'destroy'])->name('schedules.destroy');

    Route::get('/participants', [ParticipantController::class, 'index'])->name('participants.index');
    Route::post('/participants', [ParticipantController::class, 'store'])->name('participants.store');
    Route::put('/participants/{id}', [ParticipantController::class, 'update'])->name('participants.update');
    Route::delete('/participants/{id}', [ParticipantController::class, 'destroy'])->name('participants.destroy');
    Route::get('/test-results', [HasilController::class, 'index'])->name('results.index');
    Route::get('/test-results/{id}', [HasilController::class, 'show'])->name('results.show');
    Route::post('/test-results/{id}/publish', [HasilController::class, 'publish'])->name('results.publish');
});
