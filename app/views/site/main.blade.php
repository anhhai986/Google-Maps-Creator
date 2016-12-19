@extends('site.layouts.master')

@section('page_title')
{{ \GMC\Core\Settings::get('app_title', Config::get('system.title')) }}
@endsection

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <div class="panel panel-default">
                <div class="panel-body">
					<h3 class="text-center">Multi User Store Finder</h3>
					<p class="text-center lead">Create beautiful Google Maps with lots of features to embed into any website.</p>
					<a href="{{ url('/login') }}" class="btn btn-primary btn-block btn-lg" style="border-bottom-width:5px; font-size:1.3em;"><i class="fa fa-location-arrow"></i> &nbsp; Click here to login </a>
					<br>
					<ul class="lead">
						<li>Easy to install and use</li>
						<li>Multi user with registration</li>
						<li>Unlimited users and Google Maps</li>
						<li>Search and get directions</li>
						<li>Multi language</li>
						<li>Responsive design</li>
						<li>Choose from multiple themes</li>
						<li>Many marker images included</li>
						<li>Multiple map styles included</li>
						<li>Labels to filter results</li>
						<li>Import CSV files</li>
						<li>Works with SQLite and MySQL</li>
					</ul>
                </div>
            </div>
        </div>
    </div>
</div>

@stop