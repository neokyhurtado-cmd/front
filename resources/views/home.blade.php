@extends('layouts.app')
@section('title','PANORAMA INGENIERIA IA')

@section('content')
  <!-- Columna izquierda -->
  <aside class="col-span-12 lg:col-span-3 bg-white border border-gray-200 rounded-2xl p-6 space-y-4">
    <div class="bg-blue-50 rounded-2xl border border-blue-200 p-4">
      <h3 class="font-semibold text-blue-900 mb-2">🛠️ HERRAMIENTAS IA</h3>
      <ul class="space-y-2 text-sm text-blue-700">
        <li>• Análisis de Datos</li>
        <li>• Modelos Predictivos</li>
        <li>• Visualización</li>
      </ul>
    </div>

    <div class="bg-green-50 rounded-2xl border border-green-200 p-4">
      <h3 class="font-semibold text-green-900 mb-2">📊 DASHBOARDS</h3>
      <ul class="space-y-2 text-sm text-green-700">
        <li>• Métricas en Tiempo Real</li>
        <li>• KPIs Estratégicos</li>
        <li>• Reportes Automatizados</li>
      </ul>
    </div>
  </aside>

  <!-- Columna centro -->
  <main class="col-span-12 lg:col-span-6 space-y-6">
    @foreach ([
      ['t' => 'Nueva IA para Análisis Estructural', 'h' => 'Hace 2 horas', 'p' => 'Implementamos algoritmos avanzados de machine learning para optimizar el diseño de estructuras ingenieriles con un 40% más de precisión.'],
      ['t' => 'Proyecto Smart City - Medellín', 'h' => 'Hace 5 horas', 'p' => 'Colaboración con la alcaldía para implementar sensores IoT y análisis predictivo en la infraestructura urbana de la ciudad.'],
      ['t' => 'Certificación ISO 9001:2015', 'h' => 'Hace 1 día', 'p' => 'PANORAMA INGENIERÍA IA obtiene la certificación internacional de calidad para todos nuestros procesos de desarrollo.'],
    ] as $card)
      <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-3">{{ $card['t'] }}</h2>
          <p class="text-gray-600 mb-4">{{ $card['p'] }}</p>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">{{ $card['h'] }}</span>
            <div class="flex gap-2">
              <button class="text-blue-600 hover:text-blue-800">👍</button>
              <button class="text-gray-600 hover:text-gray-800">💬</button>
              <button class="text-gray-600 hover:text-gray-800">🔗</button>
            </div>
          </div>
        </div>
      </article>
    @endforeach
  </main>

  <!-- Columna derecha -->
  <aside class="col-span-12 lg:col-span-3 bg-gray-50 rounded-2xl p-6">
    <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl p-6 text-white mb-6">
      <h3 class="font-bold text-lg mb-2">🚀 PANORAMA PRO</h3>
      <p class="text-sm opacity-90 mb-4">Accede a herramientas avanzadas de IA y análisis predictivo</p>
      <button class="bg-white text-purple-600 px-4 py-2 rounded-lg font-semibold text-sm hover:bg-gray-100">
        Actualizar a PRO
      </button>
    </div>

    <div class="space-y-4">
      <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">🔬 Laboratorio IA</div>
      <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">📈 Analytics Avanzado</div>
      <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">🤖 AutoML Builder</div>
      <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">☁️ Cloud Computing</div>
      <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">🎯 Predicción de Fallos</div>
      <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">¿Necesitas un PMT online?</div>
    </div>
  </aside>
@endsection
