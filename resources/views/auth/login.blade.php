@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-cyan-300 mb-2">Access Panel</h1>
            <p class="text-zinc-400">Enter your credentials to continue</p>
        </div>

        {{-- Session Status --}}
        @if (session('status'))
            <div class="mb-4 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        {{-- Login Form --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 backdrop-blur-sm">
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                {{-- Email Address --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-zinc-300 mb-2">
                        Email Address
                    </label>
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}"
                           required 
                           autofocus 
                           autocomplete="username"
                           class="w-full h-12 rounded-xl bg-zinc-900 border border-zinc-800 px-4 text-zinc-100 placeholder-zinc-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-colors" 
                           placeholder="your@email.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-2">
                        Password
                    </label>
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           class="w-full h-12 rounded-xl bg-zinc-900 border border-zinc-800 px-4 text-zinc-100 placeholder-zinc-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-colors" 
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center">
                    <input id="remember_me" 
                           type="checkbox" 
                           name="remember"
                           class="h-4 w-4 rounded border-zinc-800 bg-zinc-900 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-0">
                    <label for="remember_me" class="ml-3 text-sm text-zinc-300">
                        Remember me
                    </label>
                </div>

                {{-- Actions --}}
                <div class="space-y-4">
                    <button type="submit" 
                            class="w-full h-12 rounded-xl bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold hover:from-cyan-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all">
                        Access System
                    </button>

                    @if (Route::has('password.request'))
                        <div class="text-center">
                            <a href="{{ route('password.request') }}" 
                               class="text-sm text-zinc-400 hover:text-cyan-400 transition-colors">
                                Forgot your password?
                            </a>
                        </div>
                    @endif

                    @if (Route::has('register'))
                        <div class="text-center">
                            <span class="text-sm text-zinc-500">Don't have an account? </span>
                            <a href="{{ route('register') }}" 
                               class="text-sm text-cyan-400 hover:text-cyan-300 transition-colors">
                                Create one
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
