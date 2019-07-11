<!doctype html>
<html lang="zh-CN">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>App Name - @yield('title')</title>
	<link rel="stylesheet" type="text/css" href="/css/app.css">
	@stack('head')
</head>
<body>

@section('header')

@show

@section('sidebar')
	<nav class="navbar navbar-fixed-top">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">Project name</a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<li class="active"><a href="#">Home</a></li>
				<li><a href="#about">About</a></li>
				<li><a href="#contact">Contact</a></li>
			</ul>
		</div><!--/.nav-collapse -->
	</nav>
@show

<div class="container">
	@yield('content')
</div>

@section('footer')
	这是页脚
@show

</body>
<script src="js/app.js"></script>
@stack('foot')
</html>
