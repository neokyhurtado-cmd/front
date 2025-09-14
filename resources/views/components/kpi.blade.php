@props(['title','value'=>0,'suffix'=>'%','range'=>null])
<x-card>
  <div class="flex items-start justify-between">
    <h3 class="text-sm text-neutral-300">{{ $title }}</h3>
    @if($range)<span class="text-xs text-neutral-400">{{ $range }}</span>@endif
  </div>
  <div class="mt-2 text-2xl font-semibold text-accent">{{ $value }}{{ $suffix }}</div>
  <div class="mt-3 h-2 w-full overflow-hidden rounded bg-panelAlt">
    <div class="h-full bg-lime" style="width: {{ max(0,min(100,$value)) }}%"></div>
  </div>
</x-card>