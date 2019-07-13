<div class="card my-4">
	<div class="card-header">{{ $item->title }}</div>
	<div class="card-body">
		{{ $item->description }}
	</div>
	<div class="card-footer">
		<ul class="nav nav-fill">
			<li class="nav-item">
				<a class="nav-link active" href="javascript:void(0)">点赞</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="javascript:void(0)">浏览数</a>
			</li>
			<li class="nav-item">
				<a class="nav-link disabled" href="javascript:void(0)">评论</a>
			</li>
		</ul>
	</div>
</div>
