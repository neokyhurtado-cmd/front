@props([
    'title' => null,
    'excerpt' => null,
    'image' => null,
    'date' => null,
    'author' => null,
    'category' => null,
    'readTime' => null,
    'url' => '#',
    'featured' => false,
    'layout' => 'card' // 'card', 'horizontal', 'minimal'
])

@php
$layoutClasses = [
    'card' => 'flex flex-col',
    'horizontal' => 'flex flex-row',
    'minimal' => 'border-l-2 border-accent/30 pl-4'
];

$selectedLayout = $layoutClasses[$layout] ?? $layoutClasses['card'];

$baseClasses = [
    'group',
    'transition-all duration-300',
    'hover:transform hover:scale-[1.02]'
];

if ($layout !== 'minimal') {
    $baseClasses[] = 'bg-panel/80 backdrop-blur-sm rounded-2xl border border-stroke/50 hover:border-accent/30 hover:shadow-xl hover:shadow-accent/5 overflow-hidden';
}

$finalClasses = implode(' ', $baseClasses) . ' ' . $selectedLayout;
@endphp

<article class="{{ $finalClasses }}">
  @if($layout === 'card' && $image)
    <div class="relative overflow-hidden {{ $featured ? 'h-64' : 'h-48' }}">
      <img 
        src="{{ $image }}" 
        alt="{{ $title }}"
        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
      />
      
      @if($featured)
        <div class="absolute top-4 left-4">
          <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-accent/90 text-neutral-900 backdrop-blur-sm">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            Destacado
          </span>
        </div>
      @endif

      @if($category)
        <div class="absolute bottom-4 left-4">
          <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-lime/90 text-neutral-900 backdrop-blur-sm">
            {{ $category }}
          </span>
        </div>
      @endif
    </div>
  @endif

  <div class="{{ $layout === 'card' ? 'p-6' : ($layout === 'horizontal' ? 'p-4 flex-1' : 'py-2') }}">
    @if($layout === 'horizontal' && $image)
      <div class="w-24 h-24 rounded-xl overflow-hidden mr-4 flex-shrink-0">
        <img 
          src="{{ $image }}" 
          alt="{{ $title }}"
          class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
        />
      </div>
    @endif

    <div class="flex-1">
      @if($layout !== 'card' && $category)
        <div class="mb-2">
          <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-accent/20 text-accent border border-accent/30">
            {{ $category }}
          </span>
        </div>
      @endif

      @if($title)
        <h3 class="font-bold text-neutral-100 group-hover:text-accent transition-colors duration-300 {{ $featured ? 'text-xl mb-3' : 'text-lg mb-2' }} leading-tight">
          <a href="{{ $url }}" class="hover:underline">
            {{ $title }}
          </a>
        </h3>
      @endif

      @if($excerpt)
        <p class="text-neutral-400 mb-4 {{ $featured ? 'text-base' : 'text-sm' }} line-clamp-3 leading-relaxed">
          {{ $excerpt }}
        </p>
      @endif

      {{-- Meta information --}}
      <div class="flex items-center justify-between text-xs text-neutral-500">
        <div class="flex items-center space-x-4">
          @if($author)
            <div class="flex items-center">
              <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
              </svg>
              {{ $author }}
            </div>
          @endif

          @if($date)
            <div class="flex items-center">
              <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
              </svg>
              {{ $date }}
            </div>
          @endif
        </div>

        @if($readTime)
          <div class="flex items-center text-accent">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
            </svg>
            {{ $readTime }} min
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Hover glow effect --}}
  @if($layout !== 'minimal')
    <div class="absolute -inset-0.5 bg-gradient-to-r from-accent/5 to-lime/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10 blur-sm"></div>
  @endif
</article>