@props([
    'type' => 'button',
    'variant' => 'primary', // 'primary', 'secondary', 'accent', 'ghost', 'danger'
    'size' => 'normal', // 'sm', 'normal', 'lg', 'xl'
    'icon' => null,
    'iconPosition' => 'left', // 'left', 'right'
    'loading' => false,
    'glow' => false,
    'fullWidth' => false
])

@php
$variantClasses = [
    'primary' => 'bg-gradient-to-r from-accent to-accent/80 hover:from-accent/90 hover:to-accent/70 text-neutral-900 font-semibold shadow-lg shadow-accent/25',
    'secondary' => 'bg-panel border border-stroke/50 hover:border-accent/50 text-neutral-100 hover:bg-panel/80',
    'accent' => 'bg-gradient-to-r from-lime to-lime/80 hover:from-lime/90 hover:to-lime/70 text-neutral-900 font-semibold shadow-lg shadow-lime/25',
    'ghost' => 'bg-transparent hover:bg-panel/50 text-neutral-300 hover:text-neutral-100 border border-transparent hover:border-stroke/30',
    'danger' => 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-semibold shadow-lg shadow-red-500/25'
];

$sizeClasses = [
    'sm' => 'px-4 py-2 text-sm rounded-lg',
    'normal' => 'px-6 py-3 text-base rounded-xl',
    'lg' => 'px-8 py-4 text-lg rounded-xl',
    'xl' => 'px-10 py-5 text-xl rounded-2xl'
];

$selectedVariant = $variantClasses[$variant] ?? $variantClasses['primary'];
$selectedSize = $sizeClasses[$size] ?? $sizeClasses['normal'];

$buttonClasses = [
    'inline-flex items-center justify-center',
    'transition-all duration-300',
    'focus:outline-none focus:ring-2 focus:ring-accent/50 focus:ring-offset-2 focus:ring-offset-bg',
    'disabled:opacity-50 disabled:cursor-not-allowed',
    'relative overflow-hidden',
    $selectedSize,
    $selectedVariant
];

if ($fullWidth) {
    $buttonClasses[] = 'w-full';
}

if ($glow) {
    $buttonClasses[] = 'group';
}

$finalClasses = implode(' ', $buttonClasses);
@endphp

<button type="{{ $type }}" class="{{ $finalClasses }}" {{ $attributes->except(['class']) }}>
  
  @if($glow)
    {{-- Animated glow effect --}}
    <div class="absolute -inset-0.5 bg-gradient-to-r from-accent via-lime to-accent opacity-0 group-hover:opacity-75 transition-opacity duration-500 blur-sm -z-10 animate-pulse"></div>
  @endif

  @if($loading)
    {{-- Loading spinner --}}
    <div class="absolute inset-0 flex items-center justify-center">
      <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
      </svg>
    </div>
  @endif

  <span class="{{ $loading ? 'opacity-0' : '' }} flex items-center gap-2">
    @if($icon && $iconPosition === 'left')
      <span class="flex-shrink-0">
        {!! $icon !!}
      </span>
    @endif

    {{ $slot }}

    @if($icon && $iconPosition === 'right')
      <span class="flex-shrink-0">
        {!! $icon !!}
      </span>
    @endif
  </span>
</button>