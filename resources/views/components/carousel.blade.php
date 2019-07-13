@php ($id = \Illuminate\Support\Str::uuid())

<div id="{{ $id }}" class="carousel slide" data-ride="carousel">
	<ol class="carousel-indicators">
		@foreach ($data as $item)
			<li data-target="#{{ $id }}" data-slide-to="{{ $loop->index }}" class="{{ $loop->first?'active':'' }}"></li>
		@endforeach
	</ol>
	<div class="carousel-inner">
		@foreach ($data as $item)
		<div class="carousel-item {{ $loop->first?'active':'' }}">
			<img class="d-block w-100" src="{{ $item }}">
		</div>
		@endforeach
	</div>
	<a class="carousel-control-prev" href="#{{ $id }}" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="sr-only">←</span>
	</a>
	<a class="carousel-control-next" href="#{{ $id }}" role="button" data-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="sr-only">→</span>
	</a>
</div>
