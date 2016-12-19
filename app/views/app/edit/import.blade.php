@extends('app.layouts.backend')

@section('page_title')
{{ trans('global.import') }} - {{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('head')

<link href="{{ URL::asset('assets/vendor/handsontable/dist/handsontable.full.min.css') }}" rel="stylesheet">

@stop

@section('content')
<?php
// Check cat id
$category_id = Request::get('category_id', 0);

$oCat = \GMC\Model\Category::find($category_id);
if(count($oCat) == 0) die('Not found');

// Permission check
$oCheck = \GMC\Model\User::find($parent_user_id)->categories()->find($category_id);
if(count($oCheck) == 0) die('No permissions');

$last_crumb = '';

$aFiles = File::files(app_path() . '/storage/uploads/' . md5($parent_user_id . Config::get('app.key')));

if(count($aFiles) > 0)
{
	$file = $aFiles[0];
	if(isset($_GET['delete_data']))
	{
		File::delete($file);
		header('Location: ' . url('/dashboard/import?category_id=' . $category_id));
		die();
	}
	else
	{
		$pathinfo = pathinfo($file);
		$last_crumb = '<li>' . $pathinfo['basename'] . '</li>';
	}
}

?>
 	<div class="row">
		<div class="col-md-12">

			<h1 class="page-header">{{ trans('global.import') }}</h1>

			<ol class="breadcrumb">
			  <li><a href="{{ url('/dashboard') }}">{{ trans('global.categories') }}</a></li>
			  <li><a href="{{ url('/dashboard/items?category_id=' . $category_id) }}">{{ $oCheck->name }}</a></li>
			  <li>{{ trans('global.import') }}</li>
			  {{ $last_crumb }}
			</ol>
		</div>
	</div>

 	<div class="row">
		<div class="col-md-12" id="handsontable">
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

echo '<div class="list-actions">';
echo '<a href="' . url('/dashboard/items?category_id=' . $category_id) . '" class="btn btn-default"><i class="fa fa-arrow-left"></i> ' . trans('global.back') . '</a>';

if(count($aFiles) > 0)
{
	echo '<button type="button" class="btn btn-success" data-toggle="popover" data-container="body" data-placement="left" data-content="' . trans('global.append_or_replace') . '<br><br><a href=\'#\' class=\'import_data append btn btn-default\'>' . trans('global.append') . '</a> <a href=\'#\' class=\'import_data replace btn btn-default pull-right\'>' . trans('global.replace') . '</a>">' . trans('global.import_data') . '</button>';
	echo '<button type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="' . trans('global.delete_data_info') . '" onclick="if(confirm(\'' . str_replace('"', '&quot;', str_replace('"', '&quot;', trans('global.are_you_sure'))) . '\')){ document.location = \'' . url('/dashboard/import?category_id=' . $category_id . '&delete_data') . '\';}">' . trans('global.delete_data') . '</a>';
}

echo '</div>'; // .list-actions

if(count($aFiles) > 0)
{
?>
<div class="progress progress-striped active" id="progress-import">
  <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100">
  </div>
</div>
<?php

    $columns = 6;
	$row = 1;
	$sheet = '';
	$csv = \GMC\Core\Csv::read($file);

	if($csv !== FALSE) {
		foreach($csv as $data) {
			$num = count($data);
			$row++;
			$sheet .= '[';
			for($c=0; $c < $columns; $c++) {
				$cell = (isset($data[$c])) ? utf8_encode($data[$c]) : '';
				$cell = str_replace('"', '\"', $cell);
				$cell = str_replace(chr(13), ' ', $cell);
				$cell = str_replace(chr(11), ' ', $cell);

				$sheet .= '"' . $cell . '"';
				if($c + 1 < $columns) $sheet .= ',';
			}
			$sheet .= '],';
		}
	}

	echo '<script>';
	echo 'var data = [' . $sheet . ']';
	echo '</script>';

	echo '<div id="sheet"></div>';
}

if(count($aFiles) == 0)
{
	// Upload file
	echo trans('global.import_help');

	$aRequired = array(
		trans('global.name'),
		trans('global.address'),
		trans('global.phone'),
		trans('global.email'),
		trans('global.website'),
		trans('global.description')
	);

	$i = 0;
	echo '<table class="csv"><tr>';
	foreach($aRequired as $column)
	{
		$req = ($i < 2) ? ' *' : '';
		echo '<td>' . $column . $req . '</td>';
		$i++;
	}
	echo '</tr></table>';
	echo '* ' . trans('global.required');

	echo '<br><br>';

	echo Former::horizontal_open()
		->action(url('api/v1/item/upload'))
		->enctype('multipart/form-data')
		->method('POST');

	echo Former::hidden()
		->value($category_id)
		->name('category_id');

	echo Former::files('file')
		->name('file')
		->label(trans('global.select_csv'))
		->accept('csv')
		->required();

	echo Former::actions()
		->lg_primary_submit(trans('global.upload'))
		->lg_default_link(trans('global.cancel'), url('/dashboard/items?category_id=' . $category_id));

	echo Former::close();
}
?>
		</div>
	</div>

@stop

@section('custom_script')
<?php
if(count($aFiles) > 0)
{
?>
<script src="{{ URL::asset('assets/vendor/handsontable/dist/handsontable.full.min.js') }}"></script>
<script type="text/javascript">
$(function() {
	$('#sheet').handsontable({
	  data: data,
	  minSpareRows: 1,
	  colHeaders: ["{{ trans('global.name') }}", "{{ trans('global.address') }}", "{{ trans('global.phone') }}", "{{ trans('global.email') }}", "{{ trans('global.website') }}", "{{ trans('global.description') }}"],
	  contextMenu: true,
	  width: function() { return $('#handsontable').width(); },
	  height: function() { return parseInt($(window).height()) - 320; }
	});
});

$('body').on('click', '.import_data', import_data);

function import_data()
{
	var promises = [];
	var errors = 0;
    $('.popover').removeClass('in');
	$('.list-actions button').attr('disabled', true);

    var append = $(this).hasClass('append') ? 1 : 0;
	var sheet = $('#sheet').handsontable('getInstance');
	var data = sheet.getData();
	var count = data.length - 1;

	$.each(data, function(index, value) {
		var i = index;
		var request = $.ajax({
		  url: "{{ url('/api/v1/import/row') }}",
		  type: "POST",
		  data: {category_id : {{ $category_id }}, append : append, index : i, count : count, value : value},
		  dataType: "html"
		});
 
		promises.push(request);
 
		request.done(function(value) {
			import_row(i, value);
		});
 
		request.fail(function(jqXHR, textStatus) {
			import_row(i);
		});
	});

	function import_row(index, value)
	{
		var perc = Math.round((index / count) * 100);
		$('#progress-import .progress-bar').css('width', perc + '%');
		$('#progress-import .progress-bar').text(''+ index +'/' + count);
	}

	$.when.apply(null, promises).done(function(){
	    $('.list-actions button').attr('disabled', false);
		$('#progress-import .progress-bar').css('width', '100%');
		$('#progress-import .progress-bar').text("{{ trans('global.ready') }}");
	})

    return false;
}
</script>
<?php
}
?>
@stop