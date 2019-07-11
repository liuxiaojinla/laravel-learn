<div class="alert alert-{{ $scene??'danger' }}">
	@isset($title)
		<div class="alert-title">{{ $title }}</div>
	@endisset

	{!! $slot !!}
</div>
