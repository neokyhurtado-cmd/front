@foreach ($posts as $post)
  <article class="card hover-raise">
    @if($post->featured_image || $post->image_url)
      <figure class="relative aspect-[16/9] max-h-[200px] overflow-hidden">
        <img src="{{ $post->featured_image ? asset('storage/'.$post->featured_image) : $post->image_url }}" 
             alt="{{ $post->title }}"
             width="1280"
             height="720"
             class="h-full w-full object-cover block"
             loading="lazy"
             onerror="this.onerror=null;this.src='{{ asset('img/placeholder.svg') }}';">
      </figure>
    @endif
    <div class="body">
      <div class="badge">publicado</div>
      <h3 class="title" style="margin:0 0 6px 0;font-size:16px">
        <a href="{{ url('/blog/'.$post->slug) }}">{{ $post->title }}</a>
      </h3>
      <p class="meta">{{ optional($post->publish_at ?? $post->published_at)->diffForHumans() }}</p>
      <p style="margin:8px 0 0;color:#bdbdbd;font-size:13px;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden">
        {{ $post->excerpt }}
      </p>
    </div>
  </article>
@endforeach