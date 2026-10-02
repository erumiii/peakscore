@extends('layouts.admin')

@section('title', 'Test Schedules — PeakScore')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Test Schedules</h1>
            <p class="mt-1 text-sm text-muted">{{ $jadwal->total() }} schedules registered</p>
        </div>
        <button type="button" onclick="openCreate()"
                class="inline-flex items-center gap-1.5 rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Schedule
        </button>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-xl border border-canvas bg-white">
        @if($jadwal->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-medium">No test schedules yet</p>
                <p class="mt-1 text-sm text-muted">Create the first schedule with the "New Schedule" button.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line text-left text-[11px] uppercase tracking-wider text-muted">
                        <th class="px-5 py-3 font-medium">Title</th>
                        <th class="px-5 py-3 font-medium">Starts</th>
                        <th class="px-5 py-3 font-medium">Ends</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 text-center font-medium">Participants</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwal as $item)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas/50">
                            <td class="px-5 py-3.5">
                                <p class="font-medium">{{ $item->judul }}</p>
                                @if($item->deskripsi)
                                    <p class="mt-0.5 text-xs text-muted">{{ $item->deskripsi }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">{{ $item->mulai->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5 whitespace-nowrap">{{ $item->selesai->format('d M Y, H:i') }}</td>
                            <td class="px-5 py-3.5">
                                @php($status = $item->status())
                                <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ match($status) {
                                        'Scheduled' => 'bg-blue-50 text-blue-700',
                                        'Ongoing' => 'bg-emerald-50 text-emerald-700',
                                        default => 'bg-neutral-100 text-neutral-500',
                                    } }}">
                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center rounded-full bg-black/5 px-2.5 py-1 text-xs font-medium text-ink/70">
                                    {{ $item->hasil_count }} taken
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button"
                                            onclick="openEdit(this)"
                                            data-url="{{ route('schedules.update', $item->id) }}"
                                            data-judul="{{ $item->judul }}"
                                            data-deskripsi="{{ $item->deskripsi }}"
                                            data-mulai="{{ $item->mulai->format('Y-m-d\TH:i') }}"
                                            data-selesai="{{ $item->selesai->format('Y-m-d\TH:i') }}"
                                            title="Edit"
                                            class="rounded-lg p-2 text-muted hover:bg-black/5 hover:text-ink">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route('schedules.destroy', $item->id) }}"
                                          onsubmit="return confirm('Delete this schedule?')">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" title="Delete"
                                                class="rounded-lg p-2 text-muted hover:bg-red-50 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{ $jadwal->links() }}
</div>

<dialog id="jadwalDialog" @if($errors->any()) open @endif
        class="w-[480px] max-w-[90vw] rounded-xl border border-canvas bg-white p-0 backdrop:bg-ink/40">
    <form id="jadwalForm"
          method="POST"
          action="{{ $errors->any() && old('id') ? route('schedules.update', old('id')) : route('schedules.store') }}"
          class="flex max-h-[85vh] flex-col">
        @csrf
        <input type="hidden" name="_method" id="httpMethod"
               value="{{ $errors->any() && old('id') ? 'PUT' : 'POST' }}">

        <div class="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 id="dialogTitle" class="text-base font-semibold">
                {{ $errors->any() && old('id') ? 'Edit Schedule' : 'New Schedule' }}
            </h2>
            <button type="button" onclick="document.getElementById('jadwalDialog').close()"
                    class="rounded-lg p-1.5 text-muted hover:bg-black/5 hover:text-ink">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="space-y-4 overflow-y-auto px-6 py-5">
            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label for="judul" class="mb-1.5 block text-sm font-medium">Title</label>
                <input type="text" id="judul" name="judul" value="{{ old('judul') }}" required maxlength="150"
                       class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-ink focus:outline-none focus:ring-2 focus:ring-ink/10"
                       placeholder="e.g. TPA Wave 1">
            </div>

            <div>
                <label for="deskripsi" class="mb-1.5 block text-sm font-medium">Description <span class="font-normal text-muted">(optional)</span></label>
                <textarea id="deskripsi" name="deskripsi" rows="3"
                          class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-ink focus:outline-none focus:ring-2 focus:ring-ink/10"
                          placeholder="Small note about this test session">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="mulai" class="mb-1.5 block text-sm font-medium">Start</label>
                    <input type="datetime-local" id="mulai" name="mulai" required
                           value="{{ str_replace(' ', 'T', old('mulai')) }}"
                           class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-ink focus:outline-none focus:ring-2 focus:ring-ink/10">
                </div>
                <div>
                    <label for="selesai" class="mb-1.5 block text-sm font-medium">End</label>
                    <input type="datetime-local" id="selesai" name="selesai" required
                           value="{{ str_replace(' ', 'T', old('selesai')) }}"
                           class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-ink focus:outline-none focus:ring-2 focus:ring-ink/10">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-line px-6 py-4">
            <button type="button" onclick="document.getElementById('jadwalDialog').close()"
                    class="rounded-lg border border-line px-4 py-2 text-sm font-medium hover:bg-black/5">
                Cancel
            </button>
            <button type="submit"
                    class="rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">
                Save
            </button>
        </div>
    </form>
</dialog>

<script>
    const dialog = document.getElementById('jadwalDialog');
    const form = document.getElementById('jadwalForm');
    const storeUrl = @js(route('schedules.store'));

    function openCreate() {
        form.reset();
        form.action = storeUrl;
        document.getElementById('httpMethod').value = 'POST';
        document.getElementById('dialogTitle').textContent = 'New Schedule';
        dialog.showModal();
    }

    function openEdit(btn) {
        form.reset();
        form.action = btn.dataset.url;
        document.getElementById('httpMethod').value = 'PUT';
        document.getElementById('dialogTitle').textContent = 'Edit Schedule';
        document.getElementById('judul').value = btn.dataset.judul;
        document.getElementById('deskripsi').value = btn.dataset.deskripsi || '';
        document.getElementById('mulai').value = btn.dataset.mulai;
        document.getElementById('selesai').value = btn.dataset.selesai;
        dialog.showModal();
    }
</script>
@endsection
