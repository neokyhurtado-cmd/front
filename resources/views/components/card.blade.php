@props(['title' => null, 'class' => ''])
<section {{ $attributes->merge(['class' => "rounded-2xl border border-stroke bg-panel p-4 shadow-md $class"]) }}>
  @if($title)
    <h3 class="text-sm text-neutral-300">{{ $title }}</h3>
  @endif
  {{ $slot }}
</section>