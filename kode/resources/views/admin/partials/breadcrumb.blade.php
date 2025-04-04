
<div class="page-title-box">
	<h4 class="page-title">	{{$title}}</h4>
	<div class="page-title-right">
		<ol class="breadcrumb m-0">
			@if(@$breadcrumbs)
				@foreach($breadcrumbs as $text => $url)
					<li class='breadcrumb-item {{$url? "active" :""}}'>
						@if($url) 
							  	@php
								  if (is_string($url) && app('router')->has($url)) {
									$url = route($url);
								  } 
								@endphp
							<a href="{{$url}}">{{translate($text)}}</a>
						@else
							{{translate($text)}}
						@endif
					</li>
				@endforeach
			@else
				<li class="breadcrumb-item"><a href="javascript: void(0);">{{translate('Home')}}</a></li>
			@endif
		</ol>
	</div>
 </div>