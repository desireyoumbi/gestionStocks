@extends('layouts.app')

@section('header_subtitle', 'Centre de alertes')
@section('header_title', 'Notifications')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- En-tête avec action globale -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white dark:bg-gray-950 p-6 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-800">
        <div>
            <h2 class="text-lg font-black text-gray-900 dark:text-white">Centre de notifications</h2>
            <p class="text-xs text-gray-400 mt-0.5">Retrouvez ici l'historique de vos alertes et de votre activité.</p>
        </div>

        @if(isset($unreadMessages) && $unreadMessages > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                @method('PATCH')
                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Tout marquer comme lu
                </button>
            </form>
        @endif
    </div>

    <!-- Message informatif sur la suppression automatique -->
    <div class="bg-amber-500/10 border border-amber-500/20 text-amber-500 text-xs px-4 py-3 rounded-2xl font-medium flex items-center gap-2">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Note : Les notifications marquées comme lues sont automatiquement supprimées après 24 heures.</span>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs px-4 py-3 rounded-xl font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 p-6 rounded-3xl shadow-xl flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Total Notifications</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalNotifications ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 p-6 rounded-3xl shadow-xl flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 uppercase font-bold tracking-wider">Messages non lus</p>
                <h3 class="text-2xl font-black text-rose-500 mt-1">{{ $unreadMessages ?? 0 }}</h3>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Liste des notifications -->
    <div class="bg-white dark:bg-gray-950 border border-gray-100 dark:border-gray-800 rounded-3xl overflow-hidden shadow-xl divide-y divide-gray-100 dark:divide-gray-900">
        @forelse($notifications as $notification)
            <div class="p-5 flex items-start justify-between gap-4 transition hover:bg-gray-50/50 dark:hover:bg-gray-900/50 {{ is_null($notification->read_at) ? 'bg-rose-500/5 dark:bg-rose-500/5' : '' }}">
                <div class="flex items-start gap-3.5">
                    <!-- Icône selon l'état -->
                    <div class="w-10 h-10 rounded-2xl flex items-center justify-center shrink-0 {{ is_null($notification->read_at) ? 'bg-rose-500/10 text-rose-500 border border-rose-500/20' : 'bg-gray-100 dark:bg-gray-900 text-gray-400' }}">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h4 class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $notification->title ?? ($notification->data['title'] ?? 'Alerte Système') }}
                            </h4>
                            @if(is_null($notification->read_at))
                                <span class="bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider">Non lu</span>
                            @else
                                <span class="text-emerald-500 text-[10px] font-bold">✓ Lu</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-600 dark:text-gray-300 mt-1">
                            {{ $notification->message ?? ($notification->data['message'] ?? $notification->type) }}
                        </p>
                        <span class="block text-[10px] text-gray-400 mt-2">
                            {{ $notification->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <!-- Bouton action individuel -->
                @if(is_null($notification->read_at))
                    <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-600 bg-rose-500/10 hover:bg-rose-500/20 px-3 py-2 rounded-xl transition">
                            Marquer comme lu
                        </button>
                    </form>
                @endif
            </div>
        @empty
            <div class="p-12 text-center text-gray-400 text-sm">
                Aucune notification pour le moment.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($notifications) && $notifications->hasPages())
        <div class="mt-4">
            {{ $notifications->links() }}
        </div>
    @endif

</div>
@endsection