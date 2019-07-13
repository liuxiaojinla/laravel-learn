@extends('layouts.app')

@section('content')
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">{{ $info->title }}</h5>
			<p class="card-text">{{ $info->user->name }}
				<small class="text-muted"> 发表于 {{ $info->created_at }}</small>
			</p>
			<p class="blockquote-footer">{{ $info->description }}</p>
			<div class="card-text">{{ $info->content }}</div>
		</div>
	</div>

	<div class="card mt-4">
		<h5 class="card-header">评论</h5>
		<form class="card-body">
			<textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
			<button type="submit" class="btn btn-primary mt-2">提交</button>
		</form>
	</div>

	<div class="card mt-4">
		<h5 class="card-header">评论列表</h5>
		<div class="list-group list-group-flush">
			@for($i=0;$i<rand(10,20);$i++)
				<a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
					<div class="d-flex w-100 justify-content-between">
						<h5 class="mb-1">{{ $info->title }}</h5>
						<small><strong>{{ $info->user->name }}</strong>发表于 {{ $info->created_at }}</small>
					</div>
					<p class="mb-1">{{ $info->description }}</p>
					<small>45人觉得很赞</small>
				</a>
			@endfor
		</div>
	</div>
@endsection
