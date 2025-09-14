@extends('layouts.app')

@section('content')
<section class="news-scope">
  <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
    <aside class="col col-left md:col-span-3 lg:col-span-2">
      @includeIf('partials.left')
    </aside>

    <main class="col center md:col-span-6 lg:col-span-8">
      @includeIf('partials.featured-row')

      <div class="news-grid md:gap-8" style="grid-auto-rows:1fr;">
        @foreach( ($posts ?? collect())->take(6) as $post )
          <div class="flex">@include('partials.news-card', ['post' => $post])</div>
        @endforeach
      </div>
    </main>

    <aside class="col col-right md:col-span-3 lg:col-span-2">
      @includeIf('partials.right')
    </aside>
  </div>
</section>
@endsection