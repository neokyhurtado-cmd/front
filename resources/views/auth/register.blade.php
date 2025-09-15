@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-cyan-300 mb-2">Create Account</h1>
            <p class="text-zinc-400">Join the Panorama system</p>
        </div>

        {{-- Registration Form --}}
        <div class="rounded-2xl border border-zinc-800 bg-zinc-900/50 p-6 backdrop-blur-sm">
            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-zinc-300 mb-2">
                        Full Name
                    </label>
                    <input id="name" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}"
                           required 
                           autofocus 
                           autocomplete="name"
                           class="w-full h-12 rounded-xl bg-zinc-900 border border-zinc-800 px-4 text-zinc-100 placeholder-zinc-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-colors" 
                           placeholder="John Doe">
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

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
                           autocomplete="new-password"
                           class="w-full h-12 rounded-xl bg-zinc-900 border border-zinc-800 px-4 text-zinc-100 placeholder-zinc-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-colors" 
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-zinc-300 mb-2">
                        Confirm Password
                    </label>
                    <input id="password_confirmation" 
                           type="password" 
                           name="password_confirmation" 
                           required 
                           autocomplete="new-password"
                           class="w-full h-12 rounded-xl bg-zinc-900 border border-zinc-800 px-4 text-zinc-100 placeholder-zinc-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 focus:outline-none transition-colors" 
                           placeholder="••••••••">
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="space-y-4">
                    <button type="submit" 
                            class="w-full h-12 rounded-xl bg-gradient-to-r from-cyan-600 to-cyan-500 text-white font-semibold hover:from-cyan-700 hover:to-cyan-600 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:ring-offset-2 focus:ring-offset-zinc-900 transition-all">
                        Create Account
                    </button>

                    <div class="text-center">
                        <span class="text-sm text-zinc-500">Already have an account? </span>
                        <a href="{{ route('login') }}" 
                           class="text-sm text-cyan-400 hover:text-cyan-300 transition-colors">
                            Sign in
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
