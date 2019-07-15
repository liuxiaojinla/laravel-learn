<div class="card my-4">
	<div class="card-body">
		<h4 class="card-title"><a href="{{ url("/posts/{$item->id}") }}">{{ $item->title }}</a></h4>
		<p class="card-text"><small class="text-muted"><strong>{{ $item->user->name }}</strong> 发表于 {{ $item->created_at }}</small></p>
		<p class="card-text">
			{{ $item->description }}
		</p>
	</div>
	<div class="card-footer" style="padding: 0">
		<ul class="nav nav-fill">
			<li class="nav-item">
				<a class="nav-link active" href="javascript:void(0)">点赞({{ $item->praise_count }})</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="javascript:void(0)">浏览({{ $item->view_count }})</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="javascript:void(0)">评论({{ $item->comment_count }})</a>
			</li>
		</ul>
	</div>
</div>
