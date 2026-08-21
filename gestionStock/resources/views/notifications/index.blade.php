@extends('layouts.app') <!-- Adapte selon ton layout -->

@section('content')
<div class="p-6 text-gray-900 dark:text-gray-100">
    <!-- En-tête -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">E-STOCKLY / NOTIFICATIONS</span>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Centre de notifications</h1>
        </div>
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit" class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-xl text-xs font-semibold shadow-sm flex items-center gap-2 transition">
                ✓ Tout marquer comme lu
            </button>
        </form>
    </div>

    <!-- Cartes de statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-950 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">TOTAL NOTIFICATIONS</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $totalNotifications }}</h3>
            </div>
            <div class="p-3 bg-amber-500/10 text-amber-500 rounded-xl">
                🔔
            </div>
        </div>
        <div class="bg-white dark:bg-gray-950 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">MESSAGES NON LUS</p>
                <h3 class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $unreadMessages }}</h3>
            </div>
            <div class="p-3 bg-amber-500/10 text-amber-500 rounded-xl">
                ✉️
            </div>
        </div>
    </div>

    <!-- Liste des notifications -->
    <div class="bg-white dark:bg-gray-950 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
        @forelse($notifications as $notification)
            {{-- Utilisation de is_null($notification->read_at) pour Laravel --}}
            <div class="p-4 border-b border-gray-100 dark:border-gray-900 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-900/50 transition {{ is_null($notification->read_at) ? 'bg-amber-500/5' : '' }}">
                <div class="flex items-center gap-4">
                    <!-- Icône selon le type -->
                    <div class="p-2.5 rounded-xl {{ isset($notification->data['type']) && $notification->data['type'] == 'warning' ? 'bg-red-500/10 text-red-500' : 'bg-amber-500/10 text-amber-500' }}">
                        🔔
                    </div>
                    <div>
                        {{-- Adaptation aux clés du tableau de données de la notification Laravel (data) ou attribut direct --}}
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $notification->data['title'] ?? $notification->title ?? 'Notification' }}</h4>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $notification->data['message'] ?? $notification->message ?? '' }}</p>
                        <span class="text-[10px] text-gray-400 mt-1 block">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div>
                    @if(is_null($notification->read_at))
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 px-3 py-1.5 rounded-xl text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 flex items-center gap-1 shadow-sm transition">
                                ✓ Lu
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-amber-500 font-medium flex items-center gap-1">✓ Lu</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-400 text-xs">
                Aucune notification pour le moment.
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-900">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
@endsection