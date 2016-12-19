@extends('app.layouts.backend')

@section('page_title')
{{ trans('global.user_management') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')

 	<div class="row">
		<div class="col-md-12">

			<h1 class="page-header">{{ trans('global.users') }} ({{ $oUsers->getTotal() }})</h1>

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.dashboard') }}</a></li>
			  <li class="active">{{ trans('global.user_management') }}</li>
			</ol>
		</div>
	</div>
 
 	<div class="row">

		<div class="col-md-12">

<?php 
if(isset($_GET['deleted']))
{ 
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_deleted'), trans('global.user_s_')) . '</div>';
}
elseif(isset($_GET['restore']))
{
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_restored'), trans('global.user_s_')) . '</div>';
}
elseif(isset($_GET['saved']))
{
	echo '<div class="alert alert-success">' . trans('global.save_success') . '</div>';
}
elseif(isset($_GET['trash']))
{
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_trashed'), trans('global.user_s_')) . '</div>';
}
?>


			<ul class="nav nav-pills pull-right">
			 <li class="dropdown">
				<a class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">
				  <i class="fa fa-cog"></i> <span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
				  <li><a href="{{ url('/api/v1/auth/users-csv') }}">{{ trans('global.download_csv') }}</a></li>
				</ul>
			  </li>
			</ul>

            <div class="list-actions">
			  <a href="{{ url('/dashboard/users/user') }}" class="btn btn-success"><i class="fa fa-plus-square"></i> {{ trans('global.new_user') }}</a>
<?php
$iCount = count($oDeleted);

if($iCount > 0)
{
?>
			  <a href="{{ url('/dashboard/users?restore') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('global.restore_items') }} <span class="label label-danger">{{ $iCount }}</span></a>
			  <a href="{{ url('/dashboard/users?trash') }}" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('global.empty_trash') }}</a>
<?php
}
?>
            </div>
<br>

<form role="form" action="{{ url('/api/v1/auth/batch-user-delete') }}" method="post">
<?php echo Form::token(); ?>
<div class="table-responsive table-bordered">
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th style="width:30px;"><input type="checkbox" id="select_all"></th>
			<th>{{ trans('global.name') }}</th>
			<th>{{ trans('global.email') }}</th>
			<th>{{ trans('global.role') }}</th>
			<th class="hidden-xs">{{ trans('global.created') }}</th>
			<th class="hidden-xs">{{ trans('global.logins') }}</th>
			<th class="hidden-xs">{{ trans('global.last_login') }}</th>
			<th style="width:80px;">
			<div class="pull-right">
<?php
echo '<select name="pp" id="pp">';
foreach(trans('global.per_page_array') as $perpage)
{
	$text = ($perpage == 10000) ? trans('global.all') : $perpage;

	$selected = ($perpage == $pp) ? ' selected="selected"' : '';
	echo '<option value="' . $perpage . '"' . $selected . '>' . $text . '</option>';
}
echo '</select>';
?>
			</div>
			</th>
		</tr>
	</thead>
	<tbody>
<?php 

foreach($oUsers as $user) { 

	$aRole = trans('global.roles');
	$role = $aRole[$user->role];

	$created = \Carbon\Carbon::createFromTimeStamp(strtotime($user->created_at), Auth::user()->timezone)->diffForHumans();
	$last_login = ($user->last_login != '') ? \Carbon\Carbon::createFromTimeStamp(strtotime($user->last_login), Auth::user()->timezone)->diffForHumans() : trans('global.never');

	$row_class = ($user->active == 0) ? ' class="danger"' : '';
?>
		<tr{{ $row_class }}>
			<td class="text-center">
<?php if($user->id != 1) { ?>
				<input type="checkbox" name="id[]" value="{{ $user->id }}">
<?php } else { ?>
				<i class="fa fa-lock"></i>	
<?php } ?>
			</td>
			<td>{{ $user->name }}</td>
			<td>{{ $user->email }}</td>
			<td>{{ $role }}</td>
			<td class="hidden-xs">{{ $created }}</td>
			<td class="hidden-xs">{{ $user->logins }}</td>
			<td class="hidden-xs">{{ $last_login }}</td>
			<td class="text-center">
				<a href="{{ url('/dashboard/users/user?id=' . $user->id) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.edit_user') }}"><i class="fa fa-pencil"></i></a>
				<a href="{{ url('/api/v1/auth/login-as/' . $user->id) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.login_as_user') }}"><i class="fa fa-sign-in"></i></a>
			</td>
		</tr>
<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">

				<button type="submit" class="btn btn-danger btn-xs btn-action" disabled>{{ trans('global.delete_selected') }}</button>

			</td>
		</tr>
	</tfoot>
</table>
</div>
</form>

<?php echo $oUsers->links(); ?>

		</div>	

	</div>

@stop

@section('custom_script')

<script type="text/javascript">
$(function() {
	$('#select_all').click(function() {
		var checkBoxes = $('input[name=id\\[\\]]');
		checkBoxes.prop('checked', $(this).prop('checked'));
		checkBoxesState();
	});

	$('input[name=id\\[\\]]').click(checkBoxesState);

	$('#pp').on('change', function() {
		var href = document.location.href;
		href = removeParam('pp', href);
		href = removeParam('page', href);
		document.location = href + '?pp=' + $(this).val();
	});
});

function checkBoxesState()
{
	if($('input[name=id\\[\\]]:checked').length)
	{
		$('.btn-action').removeAttr('disabled');
	}
	else
	{
		$('.btn-action').attr('disabled', true);
	}
}
</script>

@stop