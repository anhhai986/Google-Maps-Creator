@extends('app.layouts.backend')

@section('page_title')
{{ trans('global.dashboard') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')

          <h1 class="page-header">{{ trans('global.dashboard') }}</h1>

          <div class="row placeholders">
            <div class="col-xs-6 col-sm-3 placeholder">
				<div class="peterriver">
				  <h4>{{ $categories }}</h4>
				  <span>{{ trans('global.categories') }}</span>
				 </div>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
				<div class="turquoise">
				  <h4>{{ $active_items }}</h4>
				  <span>{{ trans('global.active_items') }}</span>
				 </div>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
				<div class="alizarin">
				  <h4>{{ $inactive_items }}</h4>
				  <span>{{ trans('global.inactive_items') }}</span>
				 </div>
            </div>
            <div class="col-xs-6 col-sm-3 placeholder">
				<div class="sunflower">
				  <h4>{{ $options }}</h4>
				  <span>{{ trans('global.options') }}</span>
				 </div>
            </div>
          </div>

          <h2 class="sub-header">{{ trans('global.categories') }} ({{ $oCats->count() }})</h2>

<?php
if($error)
{
	echo '<div class="alert alert-danger">' . $error . '</div>';
}

if($message)
{
	echo '<div class="alert alert-success">' . $message . '</div>';
}

if(isset($_GET['restore']))
{
	Storefinder\Model\Category::withTrashed()->where('user_id', $parent_user_id)->restore();
}
?>
 	<div class="row">

		<div class="col-md-12">

<?php 
if(isset($_GET['deleted']))
{ 
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_deleted'), trans('global.category_s_')) . '</div>';
}
elseif(isset($_GET['restore']))
{
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_restored'), trans('global.category_s_')) . '</div>';
}
elseif(isset($_GET['saved']))
{
	echo '<div class="alert alert-success">' . trans('global.save_success') . '</div>';
}
elseif(isset($_GET['trash']))
{
	echo '<div class="alert alert-success">' . sprintf(trans('global.items_trashed'), trans('global.category_s_')) . '</div>';
}
?>

<?php /*
			<ul class="nav nav-pills pull-right">
			 <li class="dropdown">
				<a class="dropdown-toggle js-activated" data-toggle="dropdown" href="#">
				  <i class="fa fa-cog"></i> <span class="caret"></span>
				</a>
				<ul class="dropdown-menu pull-right">
				  <li><a href="{{ url('/api/v1/cat/download-csv') }}">{{ trans('global.download_csv') }}</a></li>
				</ul>
			  </li>
			</ul>
*/ ?>
            <div class="list-actions">
			  <a href="{{ url('/dashboard/category') }}" class="btn btn-success"><i class="fa fa-plus-square"></i> {{ trans('global.new_category') }}</a>
<?php
$iCount = count($oDeleted);

if($iCount > 0)
{
?>
			  <a href="{{ url('/dashboard?restore') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('global.restore_items') }} <span class="label label-danger">{{ $iCount }}</span></a>
			  <a href="{{ url('/dashboard?trash') }}" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('global.empty_trash') }}</a>
<?php
}
?>
            </div>

<form role="form" action="{{ url('/api/v1/category/batch-delete') }}" method="post">
<?php echo Form::token(); ?>
<div class="table-responsive table-bordered">
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th style="width:30px;"><input type="checkbox" id="select_all"></th>
			<th>{{ trans('global.name') }}</th>
			<th class="text-center">{{ trans('global.items') }}</th>
			<th class="text-center">{{ trans('global.marker') }}</th>
			<th class="hidden-xs">{{ trans('global.updated') }}</th>
			<th style="width:150px;"></th>
		</tr>
	</thead>
	<tbody>
<?php 

foreach($oCats as $cat) { 

	$created = \Carbon\Carbon::createFromTimeStamp(strtotime($cat->created_at), Auth::user()->timezone)->diffForHumans();
	$updated = \Carbon\Carbon::createFromTimeStamp(strtotime($cat->updated_at), Auth::user()->timezone)->diffForHumans();
    $item_count = \GMC\Model\Item::where('category_id', '=', $cat->id)->count();
	$marker = \GMC\Core\CategoryHelpers::getMarker($cat->marker);

	$row_class = ($cat->active == 0) ? ' class="danger"' : '';
?>
		<tr{{ $row_class }}>
			<td class="text-center">
<?php if($cat->undeletable == 0) { ?>
				<input type="checkbox" name="id[]" value="{{ $cat->id }}">
<?php } else { ?>
				<i class="fa fa-lock"></i>	
<?php } ?>
			</td>
			<td><a href="{{ url('/dashboard/items?category_id=' . $cat->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.edit_items') }}">{{ $cat->name }}</a></td>
			<td class="text-center">{{ $item_count }}</td>
			<td class="text-center"><a href="{{ url('/dashboard/category?id=' . $cat->id) }}"><img src="{{ $marker }}" style="height:32px"></a></td>
			<td class="hidden-xs"><div class="label label-default">{{ $updated }}</div></td>
			<td class="text-center">
				<a href="{{ url('/dashboard/category?id=' . $cat->id) }}" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.edit_category') }}"><i class="fa fa-pencil"></i></a>
				<a href="{{ \GMC\Core\CategoryHelpers::getLink($cat->id) }}" class="btn btn-info btn-xs" target="_blank" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.view_category') }}"><i class="fa fa-external-link-square"></i></a>
				<a href="{{ url('/dashboard/options?category_id=' . $cat->id) }}" class="btn btn-warning btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.options') }}"><i class="fa fa-tags"></i></a>
				<span data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.embed_map') }}"><button type="button" class="btn btn-danger btn-xs" data-toggle="popover" data-container="body" data-placement="left" data-content="<?php echo \GMC\Core\CategoryHelpers::getEmbed($cat->id) ?>"><i class="fa fa-code"></i></button></span>
				<a href="{{ url('/dashboard/import?category_id=' . $cat->id) }}" class="btn btn-default btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.import_csv') }}"><i class="fa fa-upload"></i></a>
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

		</div>	

	</div>

@stop

@section('custom_script')

<script type="text/javascript">
<?php
// Change init email
/*
if(\Auth::user()->email == 'info@example.com') {
?>
document.location = "{{ url('/dashboard/user/settings?change_mail') }}";
<?php
}
*/
?>
$(function() {
	$('#select_all').click(function() {
		var checkBoxes = $('input[name=id\\[\\]]');
		checkBoxes.prop('checked', $(this).prop('checked'));
		checkBoxesState();
	});

	$('input[name=id\\[\\]]').click(checkBoxesState);
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