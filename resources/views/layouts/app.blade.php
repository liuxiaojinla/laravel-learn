<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name') }} - {{ $_META['title'] ?? '' }}</title>
	<meta name="keywords" content="{{ $_META['keywords'] ?? '' }}">
	<meta name="description" content="{{ $_META['description'] ?? '' }}">

	<!-- Fonts -->
	<link rel="dns-prefetch" href="//fonts.gstatic.com">
	<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

	<!-- Styles -->
	<link href="{{ asset('css/app.css') }}" rel="stylesheet">
	@yield('head')
</head>
<body>
<div id="app">
	@include('includes.app_header')

	@section('container')
		<main class="container py-4">
			<div class="row">
				<div class="col-sm app-aside">
					@section('aside')
						@include('includes.app_aside')
					@show
				</div>
				<div class="col-sm">
					@yield('content')
				</div>
			</div>
		</main>
	@show

</div>
</body>

<!-- Scripts -->
<script src="{{ asset('js/manifest.js') }}"></script>
<script src="{{ asset('js/vendor.js') }}"></script>
@yield('foot')
<script src="{{ asset('js/app.js') }}"></script>
</html>
