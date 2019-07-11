<div class="card">
	@isset($title)
		<div class="card-header">{{ $title }}</div>
	@endisset

	{!! $slot !!}

	@isset($footer)
		<div class="card-footer">{!! $footer !!}</div>
	@endisset
</div>
