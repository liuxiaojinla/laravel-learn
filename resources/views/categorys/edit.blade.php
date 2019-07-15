@extends('layouts.app')

@section('container')
	<div class="container py-4">

		@if ($errors->any())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form class="card" method="post" action="{{ route('categorys.store') }}">
			<div class="card-body">
				<h4 class="card-title">创建分类</h4>

				@csrf

				<div class="form-group">
					<label for="title">标题</label>
					<input id="title" type="text" name="title" class="form-control input-w" placeholder="请输入标题" maxlength="48"/>
					<small class="form-text text-muted">不少于3个字符和不多于48个字符</small>
				</div>

				<div class="form-group">
					<label for="keywords">网页关键字</label>
					<input id="keywords" type="text" name="keywords" class="form-control input-w" placeholder="请输入关键字" maxlength="48"/>
					<small class="form-text text-muted">不少于3个字符和不多于48个字符</small>
				</div>

				<div class="form-group">
					<label for="description">网页描述</label>
					<textarea id="description" name="description" class="form-control" placeholder="请输入描述" rows="3" maxlength="128"></textarea>
				</div>

				<div class="form-group">
					<label>状态</label>
					<div>
						<div class="form-check form-check-inline">
							<label class="form-check-label">
								<input class="form-check-input" type="radio" name="status" value="0"> 隐藏
							</label>
						</div>
						<div class="form-check form-check-inline">
							<label class="form-check-label">
								<input class="form-check-input" type="radio" name="status" value="1" checked> 显示
							</label>
						</div>
					</div>
				</div>

				<button type="submit" class="btn btn-primary btn-lg" style="width: 200px">创建</button>
			</div>
		</form>
	</div>
@endsection

@push('foot')
	<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}"></script>
	<script>
	editor('#content');
	</script>
@endpush
