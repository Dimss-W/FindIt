@extends('layouts.app')

@section('title', 'Pusat Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-bell text-amber-500"></i>
                Pusat Notifikasi Mahasiswa
            </h1>
            <p class="text-xs text-slate-500 mt-1">Pemberitahuan terkait kecocokan barang, hasil klaim, dan pengambilan</p>
        </div>

        <form method="POST" action="{{ route('mahasiswa.notifikasi.read_all') }}">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                Tandai Semua Dibaca
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden divide-y divide-slate-100">
        @forelse($notifikasis as $notif)
            <div class="p-5 hover:bg-slate-50 transition-colors flex items-start gap-4 {{ !$notif->is_read ? 'bg-blue-50/40' : '' }}">
                <div class="w-10 h-10 rounded-2xl {{ !$notif->is_read ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500' }} flex items-center justify-center flex-shrink-0 text-sm font-bold">
                    <i class="fa-solid fa-bell"></i>
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-slate-900 {{ !$notif->is_read ? 'text-blue-900' : '' }}">
                            {{ $notif->judul }}
                        </h3>
                        <span class="text-[11px] text-slate-400 flex-shrink-0">
                            {{ $notif->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $notif->pesan }}</p>

                    <div class="flex items-center gap-4 mt-3">
                        @if($notif->link)
                            <form method="POST" action="{{ route('mahasiswa.notifikasi.read', $notif->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-blue-600 hover:underline inline-flex items-center gap-1">
                                    <span>Buka Tautan Terkait</span>
                                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </button>
                            </form>
                        @endif

                        @if(!$notif->is_read)
                            <form method="POST" action="{{ route('mahasiswa.notifikasi.read', $notif->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-[11px] font-semibold text-slate-400 hover:text-slate-600">
                                    Tandai telah dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-16 text-center text-slate-400">
                <i class="fa-regular fa-bell-slash text-4xl mb-3 block text-slate-300"></i>
                Belum ada notifikasi untuk Anda.
            </div>
        @endforelse
    </div>

    <div class="pt-2">
        {{ $notifikasis->links() }}
    </div>

</div>
@endsection
