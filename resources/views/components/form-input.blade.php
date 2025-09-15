@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => null,
    'required' => false,
    'error' => null,
    'helper' => null,
    'icon' => null,
    'size' => 'normal', // 'sm', 'normal', 'lg'
    'variant' => 'default', // 'default', 'accent', 'minimal'
])

@php
$inputId = $name ?? 'input_' . rand(1000, 9999);

$sizeClasses = [
    'sm' => 'px-4 py-2 text-sm',
    'normal' => 'px-4 py-3 text-base',
    'lg' => 'px-6 py-4 text-lg'
];

$variantClasses = [
    'default' => 'bg-panel border-stroke/50 focus:border-accent/50 focus:ring-accent/20',
    'accent' => 'bg-panel border-accent/30 focus:border-accent focus:ring-accent/30',
    'minimal' => 'bg-transparent border-stroke/30 focus:border-accent/50 focus:ring-accent/10'
];

$selectedSize = $sizeClasses[$size] ?? $sizeClasses['normal'];
$selectedVariant = $variantClasses[$variant] ?? $variantClasses['default'];

$inputClasses = [
    'w-full',
    'rounded-xl',
    'border',
    'transition-all duration-300',
    'text-neutral-100',
    'placeholder:text-neutral-500',
    'focus:outline-none focus:ring-2',
    $selectedSize,
    $selectedVariant
];

if ($error) {
    $inputClasses[] = 'border-red-500/50 focus:border-red-500 focus:ring-red-500/20';
}

$finalInputClasses = implode(' ', $inputClasses);
@endphp

<div class="space-y-2">
  @if($label)
    <label for="{{ $inputId }}" class="block text-sm font-medium text-neutral-200">
      {{ $label }}
      @if($required)
        <span class="text-accent ml-1">*</span>
      @endif
    </label>
  @endif

  <div class="relative group">
    @if($icon)
      <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
        <div class="text-neutral-400 group-focus-within:text-accent transition-colors duration-200">
          {!! $icon !!}
        </div>
      </div>
    @endif

    <input
      id="{{ $inputId }}"
      name="{{ $name }}"
      type="{{ $type }}"
      placeholder="{{ $placeholder }}"
      value="{{ old($name, $value) }}"
      @if($required) required @endif
      class="{{ $finalInputClasses }} {{ $icon ? 'pl-12' : '' }}"
      {{ $attributes->except(['class']) }}
    />

    {{-- Focus glow effect --}}
    <div class="absolute -inset-0.5 bg-gradient-to-r from-accent/10 to-lime/10 rounded-xl opacity-0 group-focus-within:opacity-100 transition-opacity duration-300 -z-10 blur-sm"></div>
  </div>

  @if($helper && !$error)
    <p class="text-xs text-neutral-400 flex items-center gap-1">
      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      {{ $helper }}
    </p>
  @endif

  @if($error)
    <p class="text-xs text-red-400 flex items-center gap-1">
      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      {{ $error }}
    </p>
  @endif
</div>