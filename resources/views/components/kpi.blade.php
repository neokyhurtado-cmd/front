@props([
    'title' => 'KPI', 
    'value' => 0, 
    'suffix' => '%', 
    'range' => null,
    'trend' => null, // 'up', 'down', 'stable'
    'color' => 'accent', // 'accent', 'lime', 'orange', 'red'
    'icon' => null,
    'description' => null
])

@php
$colorClasses = [
    'accent' => 'text-accent border-accent/20 bg-gradient-to-br from-accent/5 to-accent/10',
    'lime' => 'text-lime border-lime/20 bg-gradient-to-br from-lime/5 to-lime/10',
    'orange' => 'text-orange-400 border-orange-400/20 bg-gradient-to-br from-orange-400/5 to-orange-400/10',
    'red' => 'text-red-400 border-red-400/20 bg-gradient-to-br from-red-400/5 to-red-400/10'
];

$progressColors = [
    'accent' => 'bg-gradient-to-r from-accent to-accent/80',
    'lime' => 'bg-gradient-to-r from-lime to-lime/80',
    'orange' => 'bg-gradient-to-r from-orange-400 to-orange-400/80',
    'red' => 'bg-gradient-to-r from-red-400 to-red-400/80'
];

$trendIcons = [
    'up' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>',
    'down' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>',
    'stable' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"/>'
];

$selectedColorClass = $colorClasses[$color] ?? $colorClasses['accent'];
$selectedProgressColor = $progressColors[$color] ?? $progressColors['accent'];
@endphp

<div class="relative group">
  {{-- Glow effect --}}
  <div class="absolute -inset-0.5 bg-gradient-to-r from-{{ $color }}/20 to-{{ $color }}/10 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
  
  {{-- Main card --}}
  <div class="relative rounded-2xl border border-stroke/50 bg-panel/80 backdrop-blur-sm p-6 hover:border-{{ $color }}/30 transition-all duration-300 {{ $selectedColorClass }}">
    
    {{-- Header con icono y trend --}}
    <div class="flex items-start justify-between mb-4">
      <div class="flex items-center gap-3">
        @if($icon)
          <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-{{ $color }}/10 border border-{{ $color }}/20">
            <svg class="h-5 w-5 {{ explode(' ', $selectedColorClass)[0] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              {!! $icon !!}
            </svg>
          </div>
        @endif
        <div>
          <h3 class="text-sm font-medium text-neutral-200">{{ $title }}</h3>
          @if($description)
            <p class="text-xs text-neutral-400 mt-0.5">{{ $description }}</p>
          @endif
        </div>
      </div>

      {{-- Trend indicator --}}
      @if($trend)
        <div class="flex items-center gap-1 px-2 py-1 rounded-full {{ $trend === 'up' ? 'bg-lime/10 border border-lime/20' : ($trend === 'down' ? 'bg-red-400/10 border border-red-400/20' : 'bg-neutral-400/10 border border-neutral-400/20') }}">
          <svg class="h-3 w-3 {{ $trend === 'up' ? 'text-lime' : ($trend === 'down' ? 'text-red-400' : 'text-neutral-400') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $trendIcons[$trend] ?? '' !!}
          </svg>
          @if($range)
            <span class="text-xs font-medium {{ $trend === 'up' ? 'text-lime' : ($trend === 'down' ? 'text-red-400' : 'text-neutral-400') }}">{{ $range }}</span>
          @endif
        </div>
      @endif
    </div>

    {{-- Valor principal --}}
    <div class="mb-4">
      <div class="text-3xl font-bold {{ explode(' ', $selectedColorClass)[0] }} mb-1">
        {{ $value }}{{ $suffix }}
      </div>
      @if($range && !$trend)
        <div class="text-xs text-neutral-400 font-medium">Rango: {{ $range }}</div>
      @endif
    </div>

    {{-- Barra de progreso mejorada --}}
    <div class="space-y-2">
      <div class="flex justify-between text-xs">
        <span class="text-neutral-400">Progreso</span>
        <span class="text-neutral-300 font-medium">{{ $value }}{{ $suffix }}</span>
      </div>
      
      <div class="relative h-2 w-full overflow-hidden rounded-full bg-stroke/50">
        {{-- Background glow --}}
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-{{ $color }}/5 to-transparent"></div>
        
        {{-- Progress bar --}}
        <div class="relative h-full transition-all duration-1000 ease-out {{ $selectedProgressColor }} rounded-full" 
             style="width: {{ max(0, min(100, $value)) }}%">
          {{-- Shine effect --}}
          <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-pulse"></div>
        </div>
      </div>
    </div>

    {{-- Puntos de datos adicionales --}}
    <div class="mt-4 pt-4 border-t border-stroke/30">
      <div class="grid grid-cols-3 gap-4 text-center">
        <div>
          <div class="text-xs text-neutral-400">Min</div>
          <div class="text-sm font-semibold text-neutral-300">0{{ $suffix }}</div>
        </div>
        <div>
          <div class="text-xs text-neutral-400">Actual</div>
          <div class="text-sm font-semibold {{ explode(' ', $selectedColorClass)[0] }}">{{ $value }}{{ $suffix }}</div>
        </div>
        <div>
          <div class="text-xs text-neutral-400">Max</div>
          <div class="text-sm font-semibold text-neutral-300">100{{ $suffix }}</div>
        </div>
      </div>
    </div>
  </div>
</div>