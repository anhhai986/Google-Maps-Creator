@extends('app.layouts.backend')

@section('page_title')
{{ trans('global.items') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')
<?php
if(isset($_GET['restore']))
{
	\GMC\Model\Item::withTrashed()->where('category_id', $category_id)->restore();
}

if(isset($_GET['trash']))
{
	$oTrash = \GMC\Model\Item::onlyTrashed()->where('category_id', $category_id)->forceDelete();
}

$oItems = \GMC\Model\Item::where('category_id', '=', $category_id)->orderBy('name', 'ASC')->paginate(1000000);

?>
 	<div class="row">
		<div class="col-md-12">

			<h1 class="page-header">{{ $oCheck->name }}</h1>

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.categories') }}</a></li>
			  <li><a href="{{ url('/dashboard/category?id=' . $category_id) }}">{{ $oCheck->name }}</a></li>
			  <li>{{ trans('global.items') }} ({{ $oItems->getTotal() }})</li>
			</ol>
		</div>
	</div>
 
 	<div class="row">
		<div class="col-md-12">
<?php
if($error)
{
	echo '<div class="alert alert-danger">' . $error . '</div>';
}

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

            <div class="list-actions">
			  <a href="{{ url('/dashboard/item?category_id=' . $category_id) }}" class="btn btn-success"><i class="fa fa-plus-square"></i> {{ trans('global.new_item') }}</a>
			  <a href="{{ \GMC\Core\CategoryHelpers::getLink($category_id) }}" class="btn btn-info" target="_blank"><i class="fa fa-external-link-square"></i> {{ trans('global.view_category') }}</a>
			  <a href="{{ url('/dashboard/options?category_id=' . $category_id) }}" class="btn btn-warning" rel="tooltip" title="{{ trans('global.options') }}"><i class="fa fa-tags"></i></a>
			  <button type="button" class="btn btn-danger" rel="tooltip" data-placement="top" title="{{ trans('global.embed_map') }}" data-toggle="popover" data-container="body" data-placement="bottom" data-content="<?php echo \GMC\Core\CategoryHelpers::getEmbed($category_id) ?>"><i class="fa fa-code"></i></button>
			  <a href="{{ url('/dashboard/import?category_id=' . $category_id) }}" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="{{ trans('global.import_csv') }}"><i class="fa fa-upload"></i></a>
			  <a href="{{ url('/api/v1/item/download-csv?category_id=' . $category_id) }}" class="btn btn-default" data-toggle="tooltip" data-placement="top" title="{{ trans('global.download_csv') }}"><i class="fa fa-download"></i></a>

<?php
$oDeleted = \GMC\Model\Item::onlyTrashed()->where('user_id', $parent_user_id)->get();
$iCount = count($oDeleted);

if($iCount > 0)
{
?>
			  <a href="{{ url('/dashboard/items?category_id=' . $category_id . '&restore') }}" class="btn btn-default"><i class="fa fa-undo"></i> {{ trans('global.restore_items') }} <span class="label label-danger">{{ $iCount }}</span></a>
			  <a href="{{ url('/dashboard/items?category_id=' . $category_id . '&trash') }}" class="btn btn-danger"><i class="fa fa-trash-o"></i> {{ trans('global.empty_trash') }}</a>
<?php
}
?>
            </div>

<form role="form" action="{{ url('/api/v1/item/batch-delete') }}" method="post">
<?php echo Form::token(); ?>
<input type="hidden" name="category_id" value="{{ $category_id }}">

<div class="table-bordered">
<table class="table table-striped table-hover"<?php if(count($oItems) > 0) echo ' id="grid"'; ?>>
	<thead>
		<tr>
			<th data-column-id="id" data-type="numeric" data-identifier="true" data-visible="false"></th>
			<th data-column-id="name">{{ trans('global.name') }}</th>
			<th data-column-id="address">{{ trans('global.address') }}</th>
			<th data-column-id="options">{{ trans('global.options') }}</th>
			<th data-column-id="marker" data-formatter="marker">{{ trans('global.marker') }}</th>
			<th data-column-id="commands" data-column-sortable="false" data-formatter="commands"></th>
		</tr>
	</thead>
	<tbody>
<?php 

if(count($oItems) == 0)
{
	echo '<tr><td colspan="6"><h4 class="text-muted">' . trans('global.empty') . '</h4></td></tr>';
}

foreach($oItems as $item) { 

	$created = \Carbon\Carbon::createFromTimeStamp(strtotime($item->created_at), Auth::user()->timezone)->diffForHumans();
	$marker = \GMC\Core\CategoryHelpers::getMarker($item->marker, false, $item->category_id);
	$row_class = ($item->active == 0) ? ' class="danger"' : '';

	$options = implode(', ', $item->optionsList(true));
	if($options == '') $options = '-';
?>
		<tr{{ $row_class }}>
			<td>{{ $item->id }}</td>
			<td><a href="{{ url('/dashboard/item?category_id=' . $category_id . '&id=' . $item->id) }}" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.edit_item') }}">{{ $item->name }}</a></td>
			<td>{{ $item->address }}</td>
			<td>{{ $options }}</td>
			<td>{{ $marker }}</td>
			<td>{{ $created }}</td>
			<td> </td>
		</tr>
<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6">

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
$(function() {
	$("#grid").bootgrid({
		css: {
            icon: "fa",
            iconColumns: "fa-filter",
            iconDown: "fa-caret-down",
            iconRefresh: "fa-refresh",
            iconUp: "fa-caret-up",
            iconSearch: "fa-search",
            columnHeaderText: "text pull-left",
        },
		selection: true,
		multiSelect: true,
		rowCount: [10, 25, 50, 100, 200, 500, 1000, -1],
		formatters: {
			"marker": function(column, row)
			{
				return '<a href="{{ url('/dashboard/item?category_id=' . $category_id) }}&id=' + row.id + '"><img src="' + row.marker + '" style="height:32px"></a>';
			},
			"commands": function(column, row)
			{
				return '<div class="text-right"><a href="{{ url('/dashboard/item?category_id=' . $category_id) }}&id=' + row.id + '" class="btn btn-primary btn-xs" data-toggle="tooltip" data-placement="top" data-original-title="{{ trans('global.edit_item') }}"><i class="fa fa-pencil"></i></a></div>';
			}
		}
	})
	.on("selected.rs.jquery.bootgrid", checkBoxesState)
	.on("deselected.rs.jquery.bootgrid", checkBoxesState)
	.on("loaded.rs.jquery.bootgrid", onLoaded);

	setTimeout(function() {
		// Tooltips
		$('[data-toggle=tooltip],[rel=tooltip]').tooltip();
	}, 300)
});

function onLoaded()
{
	
	$('td .select-box').attr('name', 'id[]');
}

function checkBoxesState()
{
	if($('.select-box:checked').length)
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