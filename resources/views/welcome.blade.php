@extends('layouts.app')

@section('title',$title)

@section('content')
	<p>此处取域是内容信息</p>

	@component('components.alert',['size'=>'large','scene'=>'info'])
		@slot('title')
			Hello world
		@endslot
		{!! $tips !!}
	@endcomponent

	@alert(['scene'=>'warning'])
	这个警告提示的框
	@endalert

	@{{ name }}.

	@verbatim
		<div class="container">
			Hello, {{ name }}.
		</div>
	@endverbatim

	@input(['type'=>'email'])

	@each('includes.each_item',$jobs,'job','includes.each_empty')


@endsection

@push('foot')
	<script>
	var app = @json([]);
	</script>
@endpush


@prepend('foot')
	<script>
	console.log('init success.');
	</script>
@endprepend
