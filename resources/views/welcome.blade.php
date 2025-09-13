@extends('layouts.app')

@section('content')
  <!-- Left Sidebar - Tools -->
  <aside class="w-full lg:w-[438px] bg-white border-r border-gray-200 p-6">
    <div class="bg-blue-50 rounded-2xl border border-blue-200 p-4 mb-4">
      <h3 class="font-semibold text-blue-900 mb-2">🛠️ HERRAMIENTAS IA</h3>
      <div class="space-y-2 text-sm text-blue-700">
        <div>• Análisis de Datos</div>
        <div>• Modelos Predictivos</div>
        <div>• Visualización</div>
      </div>
    </div>
    
    <div class="bg-green-50 rounded-2xl border border-green-200 p-4">
      <h3 class="font-semibold text-green-900 mb-2">📊 DASHBOARDS</h3>
      <div class="space-y-2 text-sm text-green-700">
        <div>• Métricas en Tiempo Real</div>
        <div>• KPIs Estratégicos</div>
        <div>• Reportes Automatizados</div>
      </div>
    </div>
  </aside>

  <!-- Center Content - News Cards -->
  <main class="flex-1 max-w-2xl p-6">
    <div class="space-y-6">
      <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-3">Nueva IA para Análisis Estructural</h2>
          <p class="text-gray-600 mb-4">Implementamos algoritmos avanzados de machine learning para optimizar el diseño de estructuras ingenieriles con un 40% más de precisión.</p>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">Hace 2 horas</span>
            <div class="flex gap-2">
              <button class="text-blue-600 hover:text-blue-800">👍</button>
              <button class="text-gray-600 hover:text-gray-800">💬</button>
              <button class="text-gray-600 hover:text-gray-800">🔗</button>
            </div>
          </div>
        </div>
      </article>

      <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-3">Proyecto Smart City - Medellín</h2>
          <p class="text-gray-600 mb-4">Colaboración con la alcaldía para implementar sensores IoT y análisis predictivo en la infraestructura urbana de la ciudad.</p>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">Hace 5 horas</span>
            <div class="flex gap-2">
              <button class="text-blue-600 hover:text-blue-800">👍</button>
              <button class="text-gray-600 hover:text-gray-800">💬</button>
              <button class="text-gray-600 hover:text-gray-800">🔗</button>
            </div>
          </div>
        </div>
      </article>

      <article class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
        <div class="p-6">
          <h2 class="text-xl font-bold text-gray-900 mb-3">Certificación ISO 9001:2015</h2>
          <p class="text-gray-600 mb-4">PANORAMA INGENIERÍA IA obtiene la certificación internacional de calidad para todos nuestros procesos de desarrollo.</p>
          <div class="flex items-center justify-between">
            <span class="text-sm text-gray-500">Hace 1 día</span>
            <div class="flex gap-2">
              <button class="text-blue-600 hover:text-blue-800">👍</button>
              <button class="text-gray-600 hover:text-gray-800">💬</button>
              <button class="text-gray-600 hover:text-gray-800">🔗</button>
            </div>
          </div>
        </div>
      </article>
    </div>
  </main>

  <!-- Right Sidebar - PRO Features -->
  <aside class="w-full lg:w-80 bg-gray-50 p-6">
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
    </div>
    
    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">¿Necesitas un PMT online?</div>
  </aside>
@endsection