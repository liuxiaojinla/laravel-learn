<div class="card {{$class ?? ''}}">
	@isset($title)
		<div class="card-header {{$headerClass ?? ''}}">{{ $title }}</div>
	@endisset

	<div class="card-body {{$bodyClass ?? ''}}">
		{!! $slot !!}
	</div>

	@isset($footer)
		<div class="card-footer {{$footerClass ?? ''}}">{!! $footer !!}</div>
	@endisset
</div>
