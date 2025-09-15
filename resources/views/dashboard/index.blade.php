@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- Welcome Section --}}
    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-cyan-300 mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="text-zinc-400">Manage your Panorama system from this dashboard</p>
            </div>
            <div class="text-right text-sm text-zinc-500">
                <p>Last login: {{ Auth::user()->updated_at->format('M d, Y - H:i') }}</p>
                <p>Member since: {{ Auth::user()->created_at->format('M Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
            <div class="flex items-center">
                <div class="rounded-full bg-cyan-500/20 p-3">
                    <svg class="w-6 h-6 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-zinc-400">Total Users</p>
                    <p class="text-2xl font-bold text-cyan-300">{{ $stats['users'] ?? 1 }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
            <div class="flex items-center">
                <div class="rounded-full bg-lime-500/20 p-3">
                    <svg class="w-6 h-6 text-lime-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-zinc-400">Blog Posts</p>
                    <p class="text-2xl font-bold text-lime-300">{{ $stats['posts'] ?? 12 }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
            <div class="flex items-center">
                <div class="rounded-full bg-purple-500/20 p-3">
                    <svg class="w-6 h-6 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-zinc-400">API Requests</p>
                    <p class="text-2xl font-bold text-purple-300">{{ $stats['requests'] ?? 1547 }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-5">
            <div class="flex items-center">
                <div class="rounded-full bg-orange-500/20 p-3">
                    <svg class="w-6 h-6 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-zinc-400">System Status</p>
                    <p class="text-2xl font-bold text-orange-300">Online</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Cards --}}
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
        {{-- Content Management --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 hover:bg-zinc-900/70 transition-colors">
            <div class="flex items-start">
                <div class="rounded-full bg-cyan-500/20 p-3">
                    <svg class="w-8 h-8 text-cyan-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-cyan-300 mb-2">Content Management</h3>
                    <p class="text-zinc-400 text-sm mb-4">Create and manage blog posts, pages and media content</p>
                    <div class="space-y-2">
                        <a href="/admin/posts" class="block text-cyan-400 hover:text-cyan-300 text-sm transition-colors">→ Manage Posts</a>
                        <a href="/admin" class="block text-cyan-400 hover:text-cyan-300 text-sm transition-colors">→ Filament Admin</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Analytics --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 hover:bg-zinc-900/70 transition-colors">
            <div class="flex items-start">
                <div class="rounded-full bg-lime-500/20 p-3">
                    <svg class="w-8 h-8 text-lime-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-lime-300 mb-2">Analytics</h3>
                    <p class="text-zinc-400 text-sm mb-4">View traffic, performance metrics and user engagement</p>
                    <div class="space-y-2">
                        <a href="/" class="block text-lime-400 hover:text-lime-300 text-sm transition-colors">→ View KPIs</a>
                        <a href="#" class="block text-lime-400 hover:text-lime-300 text-sm transition-colors">→ Traffic Reports</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- System Tools --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 hover:bg-zinc-900/70 transition-colors">
            <div class="flex items-start">
                <div class="rounded-full bg-purple-500/20 p-3">
                    <svg class="w-8 h-8 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-purple-300 mb-2">System Tools</h3>
                    <p class="text-zinc-400 text-sm mb-4">Configure settings, manage users and system maintenance</p>
                    <div class="space-y-2">
                        <a href="/healthz" class="block text-purple-400 hover:text-purple-300 text-sm transition-colors">→ Health Check</a>
                        <a href="#" class="block text-purple-400 hover:text-purple-300 text-sm transition-colors">→ Cache Clear</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6">
        <h2 class="text-xl font-semibold text-zinc-100 mb-4">Recent Activity</h2>
        <div class="space-y-4">
            <div class="flex items-center justify-between py-3 border-b border-zinc-800/50">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-cyan-400 rounded-full mr-3"></div>
                    <div>
                        <p class="text-zinc-200">System started successfully</p>
                        <p class="text-sm text-zinc-500">{{ now()->format('M d, Y - H:i:s') }}</p>
                    </div>
                </div>
                <span class="text-xs text-cyan-400 bg-cyan-400/10 px-2 py-1 rounded">SYSTEM</span>
            </div>
            
            <div class="flex items-center justify-between py-3 border-b border-zinc-800/50">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-lime-400 rounded-full mr-3"></div>
                    <div>
                        <p class="text-zinc-200">User {{ Auth::user()->name }} logged in</p>
                        <p class="text-sm text-zinc-500">{{ Auth::user()->updated_at->format('M d, Y - H:i:s') }}</p>
                    </div>
                </div>
                <span class="text-xs text-lime-400 bg-lime-400/10 px-2 py-1 rounded">AUTH</span>
            </div>

            <div class="flex items-center justify-between py-3">
                <div class="flex items-center">
                    <div class="w-2 h-2 bg-purple-400 rounded-full mr-3"></div>
                    <div>
                        <p class="text-zinc-200">Database connection established</p>
                        <p class="text-sm text-zinc-500">{{ now()->subMinutes(5)->format('M d, Y - H:i:s') }}</p>
                    </div>
                </div>
                <span class="text-xs text-purple-400 bg-purple-400/10 px-2 py-1 rounded">DATABASE</span>
            </div>
        </div>
    </div>

        {{-- Mobility Stage (mock cards) --}}
        <div class="news-scope">
            <section class="stage rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-zinc-100">Movilidad — Destacadas</h2>
                    <div>
                        <button x-data @click="$dispatch('refresh-mobility')" class="text-sm text-cyan-300">Actualizar</button>
                    </div>
                </div>

                <div x-data="dashboard()" x-init="fetchNews()" class="space-y-4">
                    <div x-show="loading" class="animate-pulse">
                        <div class="h-48 bg-zinc-800 rounded-md"></div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" x-show="!loading">
                        <template x-for="item in filtered" :key="item.id">
                            <article class="card-mock rounded-lg overflow-hidden border border-zinc-800 bg-zinc-900/40">
                                <div class="relative">
                                    <div class="w-full h-40 bg-zinc-800 object-cover"></div>
                                    <div class="badge-breaking absolute top-2 left-2 text-xs px-2 py-1 rounded">ALERTA</div>
                                </div>
                                <div class="p-3">
                                    <h3 class="text-sm font-semibold text-zinc-100" x-text="item.title"></h3>
                                    <div class="meta-row text-xs text-zinc-400 mt-2"> <span x-text="item.minutesAgo + ' min' "></span> · <span x-text="(new URL(item.href||'#')).hostname"></span></div>
                                </div>
                            </article>
                        </template>
                    </div>

                    <div x-show="error" class="text-sm text-red-400"> <span x-text="error"></span> </div>
                </div>
            </section>
        </div>
</div>
@endsection