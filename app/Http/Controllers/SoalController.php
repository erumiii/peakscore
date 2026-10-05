<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Soal;

class SoalController extends Controller
{
    // Kolom gambar pada tabel soal + aturan file-nya
    private const GAMBAR_FIELDS = ['gambarSoal', 'gambarOpsiA', 'gambarOpsiB', 'gambarOpsiC', 'gambarOpsiD'];

    private function validateSoal(Request $request)
    {
        $rules = [
            'kategori'      => 'required|in:Verbal,Numeric,Logic,Spatial',
            'isiSoal'       => 'nullable|string',
            'opsiA'         => 'nullable|string',
            'opsiB'         => 'nullable|string',
            'opsiC'         => 'nullable|string',
            'opsiD'         => 'nullable|string',
            'jawabanBenar'  => 'required|in:A,B,C,D',
        ];
        foreach (self::GAMBAR_FIELDS as $field) {
            $rules[$field] = 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048';
        }

        return Validator::make($request->all(), $rules)->after(function ($v) use ($request) {
            if (!$request->filled('isiSoal') && !$request->hasFile('gambarSoal')) {
                $v->errors()->add('isiSoal', 'Question text or image is required.');
            }
            foreach (['A', 'B', 'C', 'D'] as $opt) {
                if (!$request->filled('opsi' . $opt) && !$request->hasFile('gambarOpsi' . $opt)) {
                    $v->errors()->add('opsi' . $opt, "Option {$opt} text or image is required.");
                }
            }
        });
    }

    private function simpanGambar(Request $request, array &$data, ?array $existing = null): void
    {
        foreach (self::GAMBAR_FIELDS as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $name = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/soal'), $name);
                $data[$field] = 'uploads/soal/' . $name;
            } elseif ($existing === null) {
                $data[$field] = null;
            }
            // Pada update ($existing !== null): file kosong = pertahankan gambar lama
        }
    }

    public function index(Request $request)
    {
        $query = Soal::query();

        // Filter by category (untuk dropdown "All Category")
        if ($request->filled('category')) {
            $query->where('kategori', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('isiSoal', 'LIKE', '%' . $request->search . '%');
        }

        $soal = $query->paginate(10); // 10 per halaman
        $total = Soal::count();
        $kategoriCount = Soal::selectRaw('kategori, COUNT(*) as jumlah')->groupBy('kategori')->pluck('jumlah', 'kategori');

        return view('questions.index', compact('soal', 'total', 'kategoriCount'));
    }

    public function create()
    {
        return view('questions.create');
    }

    public function store(Request $request)
    {
        $validator = $this->validateSoal($request);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['kategori', 'isiSoal', 'opsiA', 'opsiB', 'opsiC', 'opsiD', 'jawabanBenar']);
        $this->simpanGambar($request, $data);
        Soal::create($data);

        return redirect()->route('questions.index')->with('success', 'Successfully added a question!');
    }

    public function edit($id)
    {
        $question = Soal::findOrFail($id);
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        $question = Soal::findOrFail($id);

        $validator = $this->validateSoal($request);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = $request->only(['kategori', 'isiSoal', 'opsiA', 'opsiB', 'opsiC', 'opsiD', 'jawabanBenar']);
        $this->simpanGambar($request, $data, $question->getOriginal());
        $question->update($data);

        return redirect()->route('questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy($id)
    {
        $question = Soal::findOrFail($id);

        if ($question->jawaban()->exists()) {
            return redirect()->route('questions.index')
                ->with('error', 'Question has recorded participant answers and cannot be deleted.');
        }

        $question->delete();

        return redirect()->route('questions.index')->with('success', 'Question deleted successfully.');
    }
}
