@extends('layouts.app')

@section('content')

	<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
		<ol class="carousel-indicators">
			<li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
			<li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
		</ol>
		<div class="carousel-inner">
			<div class="carousel-item active">
				<img class="d-block w-100" src="https://ss0.bdstatic.com/94oJfD_bAAcT8t7mm9GUKT-xh_/timg?image&quality=100&size=b4000_4000&sec=1563002835&di=96d671ad3f1d972994a31e1336c59864&src=http://img4.duitang.com/uploads/blog/201405/05/20140505184427_kF4dM.jpeg" alt="First slide">
			</div>
			<div class="carousel-item">
				<img class="d-block w-100" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1563012917800&di=3ff9d5ddaafa47c2bf8467cb8a8f301f&imgtype=0&src=http%3A%2F%2Fwww.33lc.com%2Farticle%2FUploadPic%2F2012-7%2F201272693919814.jpg" alt="Second slide">
			</div>
			<div class="carousel-item">
				<img class="d-block w-100" src="https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1563012917799&di=2de4657bde454ffba5bae627f6626120&imgtype=0&src=http%3A%2F%2Fwww.deyu.ln.cn%2Fimages%2Fo53xoltemvzwwy3boixgg33n%2Fdesktop%2Felse%2F2011109114827%2F7.jpg" alt="Third slide">
			</div>
		</div>
		<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	</div>

	@each('index.tpl_post_list_item',$data,'item','index.tpl_post_list_empty')
	{{ $data->links() }}

@endsection
