<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transkrip — PeakScore</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .sheet { border: 0; box-shadow: none; }
        }
    </style>
</head>
<body class="bg-[#F7F5F2] py-10 text-[#1C1C1A]">
    <div class="mx-auto max-w-2xl px-4">
        <div class="no-print mb-4 flex justify-end">
            <button onclick="window.print()"
                class="rounded-lg bg-[#1C1C1A] px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                Print PDF
            </button>
            <a href="{{ auth()->user()->isAdmin() ? route('results.index') : route('peserta.results') }}"
                class="ml-2 rounded-lg border border-[#E7E2DC] bg-white px-4 py-2 text-sm font-medium hover:bg-[#F7F5F2]">
                Back
            </a>
        </div>

        <div class="sheet rounded-xl border border-[#E7E2DC] bg-white p-10 shadow-sm">
            <div class="border-b border-[#E7E2DC] pb-6 text-center">
                <h1 class="text-2xl font-semibold tracking-tight">PeakScore</h1>
                <p class="mt-1 text-sm text-[#8A857C]">Transkrip Hasil Tes Potensi Akademik</p>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                    <p class="text-[#8A857C]">Nama Peserta</p>
                    <p class="font-medium">{{ $hasil->user->name }}</p>
                </div>
                <div>
                    <p class="text-[#8A857C]">Username</p>
                    <p class="font-medium">{{ $hasil->user->username }}</p>
                </div>
                <div>
                    <p class="text-[#8A857C]">Jadwal Tes</p>
                    <p class="font-medium">{{ $hasil->jadwal->judul }}</p>
                </div>
                <div>
                    <p class="text-[#8A857C]">Tanggal Tes</p>
                    <p class="font-medium">{{ $hasil->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>

            @php($total = collect([$hasil->skorVerbal, $hasil->skorNumerik, $hasil->skorLogika, $hasil->skorSpasial])->filter()->sum())
            <table class="mt-8 w-full text-sm">
                <thead>
                    <tr class="border-b border-[#E7E2DC] text-left text-[11px] uppercase tracking-wider text-[#8A857C]">
                        <th class="py-2 font-medium">Kategori</th>
                        <th class="py-2 text-right font-medium">Skor</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-[#E7E2DC]">
                        <td class="py-2.5">Verbal</td>
                        <td class="py-2.5 text-right">{{ $hasil->skorVerbal ?? '—' }} <span class="text-xs text-[#8A857C]">/ 250</span></td>
                    </tr>
                    <tr class="border-b border-[#E7E2DC]">
                        <td class="py-2.5">Numerik</td>
                        <td class="py-2.5 text-right">{{ $hasil->skorNumerik ?? '—' }} <span class="text-xs text-[#8A857C]">/ 250</span></td>
                    </tr>
                    <tr class="border-b border-[#E7E2DC]">
                        <td class="py-2.5">Logika</td>
                        <td class="py-2.5 text-right">{{ $hasil->skorLogika ?? '—' }} <span class="text-xs text-[#8A857C]">/ 250</span></td>
                    </tr>
                    <tr class="border-b border-[#E7E2DC]">
                        <td class="py-2.5">Spasial</td>
                        <td class="py-2.5 text-right">{{ $hasil->skorSpasial ?? '—' }} <span class="text-xs text-[#8A857C]">/ 250</span></td>
                    </tr>
                    <tr class="font-semibold">
                        <td class="py-3">Total Skor</td>
                        <td class="py-3 text-right text-lg">{{ $total }} <span class="text-xs font-normal text-[#8A857C]">/ 1000</span></td>
                    </tr>
                </tbody>
            </table>

            <div class="mt-4">
                @if($total >= 700)
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        Lulus &mdash; Passing Grade 700
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-medium text-red-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                        Belum Lulus &mdash; Passing Grade 700
                    </span>
                @endif
            </div>

            <div class="mt-10 flex items-end justify-between text-sm">
                <div>
                    <p class="text-[#8A857C]">Diterbitkan pada</p>
                    <p class="font-medium">{{ $hasil->diterbitkanPada->format('d M Y') }}</p>
                </div>
                <div class="w-48 border-t border-[#1C1C1A] pt-1 text-center text-xs text-[#8A857C]">
                    Tanda tangan Penyelenggara
                </div>
            </div>
        </div>
    </div>
</body>
</html>
