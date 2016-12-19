@extends('app.layouts.backend')

@section('page_title')
{{ $page_title }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
 	<div class="row">
		<div class="col-md-12">

			<h1 class="page-header">{{ $page_title }} ({{ $oOptions->getTotal() }})</h1>

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.categories') }}</a></li>
			  <li><a href="{{ url('/dashboard/items?category_id=' . $category_id) }}">{{ $oCheck->name }}</a></li>
			  <li>{{ trans('global.options') }} ({{ $oOptions->getTotal() }})</li>
			</ol>

		</div>
	</div>
 
 	<div class="row">

		<div class="col-md-12">

<?php
$error = Session::get('error', false);
if($error)
{
	echo '<div class="alert alert-danger">' . $error . '</div>';
}

$message = Session::get('message', false);
if($message)
{
	echo '<div class="alert alert-success">' . $message . '</div>';
}

if(isset($_GET['deleted']))
{ 
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_deleted'), trans('global.item_s_')) . '</div>';
}
elseif(isset($_GET['restore']))
{
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_restored'), trans('global.item_s_')) . '</div>';
}
elseif(isset($_GET['saved']))
{
	echo '<div class="alert alert-success">' . trans('global.save_success') . '</div>';
}
elseif(isset($_GET['trash']))
{
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_trashed'), trans('global.item_s_')) . '</div>';
}
?>

<?php /*
			<ul class="nav nav-pills pull-right">
			 <li class="dropdown">
				<a class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">
				  <i class="fa fa-cog"></i> <span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
				  <li><a href="{{ url('/api/v1/option/download-csv') }}">{{ trans('global.download_csv') }}</a></li>
				</ul>
			  </li>
			</ul>
*/ ?>
            <div class="list-actions">
			  <a href="{{ url('/dashboard/items?category_id=' . $category_id) }}" class="btn btn-default"><i class="fa fa-arrow-left"></i> {{ trans('global.back') }}</a>
<?php
$oDeleted = \GMC\Model\Option::onlyTrashed()->where('user_id', $parent_user_id)->where('category_id', $category_id)->get();
$iCount = count($oDeleted);

if($iCount > 0)
{
?>
			  <a href="{{ url('/dashboard/options?category_id=' . $category_id . '&restore') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('global.restore_items') }} <span class="label label-danger">{{ $iCount }}</span></a>
			  <a href="{{ url('/dashboard/options?category_id=' . $category_id . '&trash') }}" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('global.empty_trash') }}</a>
<?php
}
?>
            </div>

<form role="form" action="{{ url('/api/v1/option/batch-delete') }}" method="post">
<?php echo Form::token(); ?>
<input type="hidden" name="category_id" value="{{ $category_id }}">
<div class="table-responsive table-bordered">
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th style="width:30px;"><input type="checkbox" id="select_all"></th>
			<th>{{ trans('global.option') }}</th>
			<th class="hidden-xs">{{ trans('global.updated') }}</th>
			<th class="hidden-xs">{{ trans('global.created') }}</th>
			<th style="width:65px;">
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
if(count($oOptions) == 0)
{
	echo '<tr><td colspan="5"><h4 class="text-muted">' . trans('global.no_options') . '</h4></td></tr>';
}

foreach($oOptions as $opt) { 

	$created = \Carbon\Carbon::createFromTimeStamp(strtotime($opt->created_at), Auth::user()->timezone)->diffForHumans();
	$updated = \Carbon\Carbon::createFromTimeStamp(strtotime($opt->updated_at), Auth::user()->timezone)->diffForHumans();

	$row_class = ($opt->active == 0) ? ' class="danger"' : '';

?>
		<tr{{ $row_class }}>
			<td class="text-center">
<?php if($opt->undeletable == 0) { ?>
				<input type="checkbox" name="id[]" value="{{ $opt->id }}">
<?php } else { ?>
				<i class="fa fa-lock"></i>	
<?php } ?>
			</td>
			<td>{{ $opt->name }}</td>
			<td class="hidden-xs"><div class="label label-default">{{ $updated }}</div></td>
			<td class="hidden-xs"><div class="label label-default">{{ $created }}</div></td>
			<td class="text-center">
				<a href="{{ url('/dashboard/option?id=' . $opt->id . '&category_id=' . $category_id) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.edit_option') }}"><i class="fa fa-pencil"></i></a>
			</td>
		</tr>
<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">

				<button type="submit" name="switch" class="btn btn-warning btn-xs btn-action" disabled>{{ trans('global.switch_visibility') }}</button>
				<button type="submit" name="delete" class="btn btn-danger btn-xs btn-action" disabled>{{ trans('global.delete_selected') }}</button>

			</td>
		</tr>
	</tfoot>
</table>
</div>
</form>

<?php echo $oOptions->links(); ?>

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
		document.location = href + '&pp=' + $(this).val();
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