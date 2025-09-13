@extends('layouts.app')

@section('title', 'Blog de Movilidad - Panorama Ingeniería IA')

@section('content')
  <div class="min-h-screen bg-gray-50">
    <!-- Header elegante -->
    <div class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
          <h1 class="text-4xl font-bold text-gray-900 mb-4" style="font-family: 'Montserrat', sans-serif;">
            Panorama Ingeniería IA
          </h1>
          <p class="text-xl text-gray-600 max-w-3xl mx-auto" style="font-family: 'Montserrat', sans-serif;">
            Noticias de Movilidad y Señalización Vial
          </p>
          <p class="text-gray-500 mt-2" style="font-family: 'Montserrat', sans-serif;">
            Las últimas noticias sobre movilidad urbana, señalización vial y transporte en Bogotá y Colombia
          </p>
        </div>
      </div>
    </div>

    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

      @if($posts->count() > 0)
        <!-- Stats cards elegantes como Filament -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate" style="font-family: 'Montserrat', sans-serif;">Total Posts</dt>
                  <dd class="text-2xl font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">{{ $posts->count() }}</dd>
                </dl>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate" style="font-family: 'Montserrat', sans-serif;">Automatización</dt>
                  <dd class="text-2xl font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">RSS + IA</dd>
                </dl>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                  <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate" style="font-family: 'Montserrat', sans-serif;">Actualización</dt>
                  <dd class="text-2xl font-bold text-gray-900" style="font-family: 'Montserrat', sans-serif;">24/7</dd>
                </dl>
              </div>
            </div>
          </div>

          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <a href="/admin" class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center hover:bg-orange-200 transition-colors">
                  <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </a>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate" style="font-family: 'Montserrat', sans-serif;">Panel Admin</dt>
                  <dd class="text-sm font-medium text-gray-900" style="font-family: 'Montserrat', sans-serif;">
                    <a href="/admin" class="text-orange-600 hover:text-orange-800 transition-colors">Gestionar →</a>
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <!-- Grid de posts estilo Filament table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900" style="font-family: 'Montserrat', sans-serif;">
              Últimas Noticias
            </h2>
          </div>
          
          <div class="divide-y divide-gray-200">
            @foreach($posts->take(10) as $index => $post)
              <article class="p-6 hover:bg-gray-50 transition-colors {{ $index < 6 ? 'border-l-4 border-l-blue-500' : '' }}">
                <div class="flex items-start space-x-4">
                  <!-- Imagen -->
                  @if($post->image_url)
                    <div class="flex-shrink-0">
                      <img src="{{ $post->image_url }}" 
                           class="w-16 h-16 rounded-lg object-cover border border-gray-200" 
                           alt="{{ $post->title }}"
                           loading="lazy">
                    </div>
                  @endif
                  
                  <!-- Contenido -->
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2" style="font-family: 'Montserrat', sans-serif;">
                          <a href="{{ route('post.show', $post->slug) }}" class="hover:text-blue-600 transition-colors">
                            {{ $post->title }}
                          </a>
                        </h3>
                        
                        @if($post->excerpt)
                          <p class="text-gray-600 text-sm mb-3 leading-relaxed" style="font-family: 'Montserrat', sans-serif;">
                            {{ Str::limit(strip_tags($post->excerpt), 150) }}
                          </p>
                        @endif
                        
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                          <span class="flex items-center" style="font-family: 'Montserrat', sans-serif;">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $post->published_at?->format('d M Y, H:i') }}
                          </span>
                          
                          @if($post->source)
                            <span class="flex items-center" style="font-family: 'Montserrat', sans-serif;">
                              <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                              </svg>
                              {{ $post->source }}
                            </span>
                          @endif
                        </div>
                        
                        <!-- Tags -->
                        @if($post->tags && count($post->tags) > 0)
                          <div class="flex flex-wrap gap-2 mt-3">
                            @foreach(array_slice($post->tags, 0, 3) as $tag)
                              <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" style="font-family: 'Montserrat', sans-serif;">
                                {{ $tag }}
                              </span>
                            @endforeach
                          </div>
                        @endif
                      </div>
                      
                      <!-- Status badges y acciones -->
                      <div class="flex flex-col items-end space-y-2 ml-4">
                        @if($post->type === 'educational')
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800" style="font-family: 'Montserrat', sans-serif;">
                            Educativo
                          </span>
                        @endif
                        
                        @if($index < 6)
                          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" style="font-family: 'Montserrat', sans-serif;">
                            Destacado
                          </span>
                        @endif
                        
                        <a href="{{ route('post.show', $post->slug) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                           style="font-family: 'Montserrat', sans-serif;">
                          Ver artículo
                          <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                          </svg>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        </div>

      @else
        <!-- Estado vacío elegante -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
          <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-900 mb-2" style="font-family: 'Montserrat', sans-serif;">
            No hay publicaciones disponibles
          </h3>
          <p class="text-gray-500 mb-6" style="font-family: 'Montserrat', sans-serif;">
            Los posts se están procesando automáticamente. Vuelve pronto para ver las últimas noticias.
          </p>
          <div class="flex justify-center space-x-4">
            <a href="/admin" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
              Ir al Panel Admin
            </a>
          </div>
        </div>
      @endif

      <!-- Paginación elegante -->
      @if($posts->hasPages())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 px-6 py-4">
          <div class="flex items-center justify-center">
            {{ $posts->links() }}
          </div>
        </div>
      @endif
    </div>

    <!-- Footer elegante -->
    <div class="bg-white border-t border-gray-200 mt-16">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center">
          <p class="text-gray-500 text-sm" style="font-family: 'Montserrat', sans-serif;">
            © 2025 Panorama Ingeniería IA. Sistema automatizado de noticias con RSS + IA.
          </p>
          <div class="mt-4 flex justify-center space-x-6">
            <a href="https://www.panoramaingenieria.com" 
               class="text-blue-600 hover:text-blue-800 text-sm font-medium transition-colors"
               style="font-family: 'Montserrat', sans-serif;">
              Sitio Principal
            </a>
            <a href="/admin" 
               class="text-orange-600 hover:text-orange-800 text-sm font-medium transition-colors"
               style="font-family: 'Montserrat', sans-serif;">
              Panel de Administración
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
