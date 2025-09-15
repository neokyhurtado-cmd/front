@props([
    'title' => null, 
    'subtitle' => null,
    'class' => '',
    'glow' => false,
    'hover' => true,
    'border' => true,
    'background' => 'panel', // 'panel', 'panelAlt', 'transparent'
    'padding' => 'normal' // 'tight', 'normal', 'loose'
])

@php
$backgroundClasses = [
    'panel' => 'bg-panel/80 backdrop-blur-sm',
    'panelAlt' => 'bg-panelAlt/80 backdrop-blur-sm', 
    'transparent' => 'bg-transparent'
];

$paddingClasses = [
    'tight' => 'p-4',
    'normal' => 'p-6',
    'loose' => 'p-8'
];

$selectedBg = $backgroundClasses[$background] ?? $backgroundClasses['panel'];
$selectedPadding = $paddingClasses[$padding] ?? $paddingClasses['normal'];

$baseClasses = [
    'rounded-2xl',
    'transition-all duration-300',
    $selectedPadding,
    $selectedBg
];

if ($border) {
    $baseClasses[] = 'border border-stroke/50';
}

if ($hover) {
    $baseClasses[] = 'hover:border-accent/30 hover:shadow-lg hover:shadow-accent/5';
}

if ($glow) {
    $baseClasses[] = 'relative group';
}

$finalClasses = implode(' ', $baseClasses) . ' ' . $class;
@endphp

<section {{ $attributes->merge(['class' => $finalClasses]) }}>
  
  @if($glow)
    {{-- Glow effect --}}
    <div class="absolute -inset-0.5 bg-gradient-to-r from-accent/20 to-lime/20 rounded-2xl blur opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10"></div>
  @endif

  @if($title || $subtitle)
    <div class="mb-4 pb-4 {{ $title && $subtitle ? 'border-b border-stroke/30' : '' }}">
      @if($title)
        <div class="flex items-center gap-3">
          <h3 class="text-lg font-semibold text-neutral-100 tracking-tight">{{ $title }}</h3>
          @if($glow)
            <div class="h-1.5 w-1.5 bg-accent rounded-full animate-pulse"></div>
          @endif
        </div>
      @endif
      
      @if($subtitle)
        <p class="text-sm text-neutral-400 mt-1">{{ $subtitle }}</p>
      @endif
    </div>
  @endif

  <div class="relative">
    {{ $slot }}
  </div>
</section>