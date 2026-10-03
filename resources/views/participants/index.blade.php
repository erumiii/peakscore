@extends('layouts.admin')

@section('title', 'Participants — PeakScore')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700">{{ session('success') }}</div>
    @elseif(session('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-600">{{ session('error') }}</div>
    @endif
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight">Participants</h1>
            <p class="mt-1 text-sm text-muted">{{ $peserta->total() }} participants registered</p>
        </div>

        <button type="button" onclick="openCreate()"
            class="inline-flex items-center gap-1.5 rounded-lg bg-ink px-4 py-2 text-sm font-medium text-white hover:opacity-90">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add Participant
        </button>
    </div>

    <div class="flex justify-end">
        <form method="GET" action="{{ route('participants.index') }}" class="flex items-center gap-2">
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                    </svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name / username..."
                    class="w-64 rounded-lg border border-canvas bg-white py-2 pl-9 pr-4 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-ink/10">
            </div>
        </form>
    </div>

    <div class="overflow-hidden rounded-xl border border-canvas bg-white">
        @if($peserta->isEmpty())
            <div class="px-6 py-16 text-center">
                <p class="text-sm font-medium">{{ request('search') ? 'No matching participants.' : 'No participants registered yet' }}</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-line text-left text-[11px] uppercase tracking-wider text-muted">
                        <th class="px-5 py-3 font-medium">Participant</th>
                        <th class="px-5 py-3 font-medium">Joined</th>
                        <th class="px-5 py-3 text-center font-medium">Tests Taken</th>
                        <th class="px-5 py-3 text-right font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($peserta as $item)
                        <tr class="border-b border-line last:border-0 hover:bg-canvas/50">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-ink/10 text-xs font-semibold">
                                        {{ strtoupper(substr($item->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium">{{ $item->name }}</p>
                                        <p class="text-xs text-muted">{{ $item->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-5 py-3.5 text-ink/70">{{ $item->created_at->format('d M Y') }}</td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="inline-flex items-center rounded-full bg-black/5 px-2.5 py-1 text-xs font-medium text-ink/70">
                                    {{ $item->hasil_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button" onclick='openEdit(this)'
                                        data-id="{{ $item->id }}"
                                        data-url="{{ route('participants.update', $item->id) }}"
                                        data-name="{{ $item->name }}"
                                        data-username="{{ $item->username }}"
                                        title="Edit participant"
                                        class="rounded-lg p-2 text-muted hover:bg-black/5 hover:text-ink">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>
                                    </button>
                                    @if($item->hasil_count === 0)
                                        <form method="POST" action="{{ route('participants.destroy', $item->id) }}"
                                              data-confirm="Delete participant {{ $item->name }}?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete participant"
                                                    class="rounded-lg p-2 text-muted hover:bg-red-50 hover:text-red-600">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" disabled title="Participant has test history"
                                                class="rounded-lg p-2 text-muted/40 cursor-not-allowed">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{ $peserta->appends(request()->query())->links() }}
</div>

<dialog id="pesertaDialog" @if($errors->any()) open @endif
        class="w-[440px] max-w-[90vw] rounded-xl border border-canvas bg-white p-0 backdrop:bg-ink/40">
    <form id="pesertaForm" method="POST"
          action="{{ $errors->any() && old('id') ? route('participants.update', old('id')) : route('participants.store') }}"
          class="flex flex-col">
        @csrf
        <input type="hidden" name="id" id="pesertaId" value="{{ old('id') }}">
        <input type="hidden" name="_method" id="httpMethod" value="{{ $errors->any() && old('id') ? 'PUT' : 'POST' }}">

        <div class="flex items-center justify-between border-b border-line px-6 py-4">
            <h2 id="dialogTitle" class="text-base font-semibold">New Participant</h2>
            <button type="button" onclick="document.getElementById('pesertaDialog').close()"
                    class="rounded-lg p-1.5 text-muted hover:bg-black/5 hover:text-ink">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="space-y-4 px-6 py-5">
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
                <label for="pName" class="mb-1.5 block text-sm font-medium">Name</label>
                <input type="text" id="pName" name="name" value="{{ old('name') }}" required maxlength="100"
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-ink focus:outline-none focus:ring-2 focus:ring-ink/10"
                    placeholder="e.g. John Doe">
            </div>

            <div>
                <label for="pUsername" class="mb-1.5 block text-sm font-medium">Username</label>
                <input type="text" id="pUsername" name="username" value="{{ old('username') }}" required maxlength="50"
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-ink focus:outline-none focus:ring-2 focus:ring-ink/10"
                    placeholder="letters, numbers, dot, underscore">
            </div>

            <div>
                <label for="pPassword" class="mb-1.5 block text-sm font-medium">
                    Password <span id="passwordHint" class="font-normal text-muted">(min. 8 characters)</span>
                </label>
                <input type="password" id="pPassword" name="password" minlength="8"
                    class="w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-ink focus:outline-none focus:ring-2 focus:ring-ink/10">
            </div>
        </div>

        <div class="flex justify-end gap-2 border-t border-line px-6 py-4">
            <button type="button" onclick="document.getElementById('pesertaDialog').close()"
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
    const pesertaDialog = document.getElementById('pesertaDialog');
    const pesertaForm = document.getElementById('pesertaForm');
    const storeUrl = @js(route('participants.store'));

    function openCreate() {
        pesertaForm.reset();
        pesertaForm.action = storeUrl;
        document.getElementById('pesertaId').value = '';
        document.getElementById('httpMethod').value = 'POST';
        document.getElementById('dialogTitle').textContent = 'New Participant';
        document.getElementById('passwordHint').textContent = '(min. 8 characters)';
        document.getElementById('pPassword').required = true;
        pesertaDialog.showModal();
    }

    function openEdit(btn) {
        pesertaForm.reset();
        pesertaForm.action = btn.dataset.url;
        document.getElementById('pesertaId').value = btn.dataset.id;
        document.getElementById('httpMethod').value = 'PUT';
        document.getElementById('dialogTitle').textContent = 'Edit Participant';
        document.getElementById('pName').value = btn.dataset.name;
        document.getElementById('pUsername').value = btn.dataset.username;
        document.getElementById('passwordHint').textContent = '(leave blank to keep current)';
        document.getElementById('pPassword').required = false;
        pesertaDialog.showModal();
    }
</script>
@endsection
