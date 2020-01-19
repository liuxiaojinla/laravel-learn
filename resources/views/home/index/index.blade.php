@extends('home.layouts.app')

@section('content')

	@component('components.carousel',[
		'data'=>[
			'http://www.33lc.com/article/UploadPic/2012-7/201272693919814.jpg',
			'http://www.deyu.ln.cn/images/o53xoltemvzwwy3boixgg33n/desktop/else/2011109114827/7.jpg',
		]
	])
	@endcomponent

	@each('home.index.tpl_post_list_item',$data,'item','home.index.tpl_post_list_empty')
	{{ $data->links() }}

@endsection

@push('foot')
	<script>
	new App({
		mounted: function() {
			$(function() {
				$('.carousel').carousel();
			});
		}
	});
	</script>
@endpush
