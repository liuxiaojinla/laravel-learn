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

		<form class="card" method="post" action="{{ route('posts.store') }}">
			<div class="card-body">
				<h4 class="card-title">发布文章</h4>

				@csrf

				<div class="form-group">
					<label for="title">标题</label>
					<input id="title" type="text" name="title" class="form-control input-w" placeholder="请输入标题" maxlength="48"/>
					<small class="form-text text-muted">不少于3个字符和不多于48个字符</small>
				</div>

				<div class="form-group">
					<label for="category_id">所属分类</label>
					<select class="form-control" id="category_id" name="category_id">
						@foreach($categorys as $category)
							<option value="{{ $category->id }}">{{ $category->title }}</option>
						@endforeach
					</select>
				</div>

				<div class="form-group">
					<label for="description">描述</label>
					<textarea id="description" name="description" class="form-control" placeholder="请输入描述" rows="3" maxlength="128"></textarea>
				</div>

				<div class="form-group">
					<label for="content">正文</label>
					<textarea id="content" name="content" class="form-control" placeholder="请输入描述" rows="4"></textarea>
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

				<div class="form-group">
					<label for="view_count">浏览数</label>
					<input id="view_count" type="number" name="view_count" class="form-control input-sw"/>
					<small class="form-text text-muted"></small>
				</div>

				<div class="form-group">
					<label for="praise_count">点赞数</label>
					<input id="praise_count" type="number" name="praise_count" class="form-control input-sw"/>
					<small class="form-text text-muted"></small>
				</div>

				<div class="form-group">
					<label for="comment_count">评论数</label>
					<input id="comment_count" type="number" name="comment_count" class="form-control input-sw"/>
					<small class="form-text text-muted"></small>
				</div>

				<button type="submit" class="btn btn-primary btn-lg" style="width: 200px">发布</button>
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
