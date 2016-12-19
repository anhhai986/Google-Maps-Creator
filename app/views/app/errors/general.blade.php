@extends('app.layouts.frontend')

@section('page_title')
Something requires your attention
@endsection

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="panel panel-primary">
				<div class="panel-heading">{{ $title }}</div>
				<div class="panel-body">

			{{ $msg }}
<?php
if(isset($error))
{
?>
			<br>
			<div class="alert alert-danger">{{ $error }}</div>
<?php
}
?>
				</div>
			</div>
		</div>
	</div>
</div>
@stop