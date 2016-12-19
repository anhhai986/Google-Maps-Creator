@extends('app.layouts.frontend')

@section('page_title')
404
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-4 col-md-offset-4">
			<h1>404</h1>
			<div class="panel panel-primary">
				<div class="panel-body">
					{{ trans('global.page_not_found') }} <a href="javascript:history.go(-1);">{{ trans('global.click_to_go_back') }}</a>
				</div>
			</div>
		</div>
	</div>
</div>
@stop