@php
  $featured = ($featured ?? null) ?: (($posts ?? collect())->take(3));
@endphp

@if($featured->count())
<section class="news-featured mb-8">
  @foreach($featured as $post)
    @include('partials.news-card-horizontal', ['post' => $post])
  @endforeach
</section>
@endif