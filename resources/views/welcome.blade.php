@extends('layouts.app')

@section('title',$title)

@section('head')
	<style>

	</style>
@endsection

@section('content')

	@component('components.panel')
		@slot('title')
			基本示例
		@endslot
		<p>此处取域是内容信息</p>
	@endcomponent

	@component('components.panel',['class'=>'my-4'])
		@slot('title')
			定义组件
		@endslot
		@component('components.alert',['size'=>'large','scene'=>'info'])
			@slot('title')
				Hello world
			@endslot
			{!! $tips !!}
		@endcomponent
	@endcomponent

	@component('components.panel',['class'=>'my-4'])
		@slot('title')
			定义组件别名
		@endslot
		@component('components.alert',['scene'=>'warning'])
			这个警告提示的框
		@endcomponent
	@endcomponent

	@component('components.panel',['class'=>'my-4'])
		@slot('title')
			禁用双重编码
		@endslot
		<span v-pre>@{{ name }}.</span>

		@verbatim
			<div class="container" v-pre>
				Hello, {{ name }}.
			</div>
		@endverbatim
	@endcomponent

	@component('components.panel',['class'=>'my-4'])
		@slot('title')
			包含文件
		@endslot
		@input(['type'=>'email'])

		@each('includes.each_item',$jobs,'job','includes.each_empty')
	@endcomponent

	@component('components.panel',['class'=>'my-4'])
		@slot('title')
			自定义指令
		@endslot
		@datetime(now());
	@endcomponent

	@component('components.panel',['class'=>'my-4'])
		@slot('title')
			本地化
		@endslot

		@env('local')
		// 应用在本地环境中运行...
		@elseenv('testing')
		// 应用在测试环境中运行...
		@else
			// 应用没有在本地和测试环境中运行...
			@endenv

			{{ __('common.welcome') }}

			@lang('hello world')

			@endcomponent
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
