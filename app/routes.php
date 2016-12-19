<?php
/*
 |--------------------------------------------------------------------------
 | Installation check (database, permissions)
 |--------------------------------------------------------------------------
 */

\GMC\Controller\InstallationController::check();

// ----------------------------------------------------------------------------
// General

$aUrl = parse_url(URL::current());
Former::framework('TwitterBootstrap3');

// ----------------------------------------------------------------------------
// API

if(isset($aUrl['path']) && strpos($aUrl['path'], '/api/v1') !== false)
{
	Route::controller('/api/v1/auth',            'GMC\Controller\AuthController');
	Route::controller('/api/v1/app',             'GMC\Controller\AppController');
	Route::controller('/api/v1/category',        'GMC\Controller\CategoryController');
	Route::controller('/api/v1/item',            'GMC\Controller\ItemController');
	Route::controller('/api/v1/option',          'GMC\Controller\OptionController');
	Route::controller('/api/v1/import',          'GMC\Controller\ImportController');
	Route::controller('/api/v1/installation',    'GMC\Controller\InstallationController');
};

// ----------------------------------------------------------------------------
// Main site routes

Route::get('/', function()
{
	return View::make('site.main');
});

if(isset($aUrl['path']))
{
	// ----------------------------------------------------------------------------
	// Map route

	Route::get('/map', function()
	{
		$cat = \GMC\Core\CategoryHelpers::parseLink(Request::get('m'));
		$language = \GMC\Core\CategoryHelpers::getLanguage('', false, $cat['id']);
	
		App::setLocale($language);
	
		return View::make('map.main')->with('cat', $cat);
	});

	// ----------------------------------------------------------------------------
	// App routes

	Route::group(array('before' => 'auth'), function()
	{
		Route::get( '/dashboard',                             'GMC\Controller\DashboardController@showDashboard');
		Route::get( '/dashboard/user/settings',               'GMC\Controller\UserController@showUserSettings');
		Route::get( '/dashboard/items',                       'GMC\Controller\ItemController@showItems');
		Route::get( '/dashboard/item',                        'GMC\Controller\ItemController@showItem');
		Route::get( '/dashboard/options',                     'GMC\Controller\OptionController@showOptions');
		Route::get( '/dashboard/option',                      'GMC\Controller\OptionController@showOption');
		Route::get( '/dashboard/category',                    'GMC\Controller\CategoryController@showCategory');
		Route::get( '/dashboard/import',                      'GMC\Controller\ImportController@showImport');
	
		// Superadmin views
		Route::group(array('before' => 'superadmin'), function()
		{
			if(Config::get('system.user_management'))
			{
				Route::get( '/dashboard/users',                      'GMC\Controller\UserController@showUsers');
				Route::get( '/dashboard/users/user',                 'GMC\Controller\UserController@showUser');
				Route::get( '/dashboard/settings',                   'GMC\Controller\SettingsController@showSettings');
			}
		});
	});

	// ----------------------------------------------------------------------------
	// Auth

	Route::get( '/login',                                     'GMC\Controller\AuthController@showLogin');
	Route::get( '/signup',                                    'GMC\Controller\AuthController@showSignup');
	Route::get( '/reminder',                                  'GMC\Controller\AuthController@showReminder');
	Route::get( '/reset/{token}',                             'GMC\Controller\AuthController@showReset');
	Route::get( '/activate/{token}',                          'GMC\Controller\AuthController@showActivate');
	Route::get( '/logout',                                    'GMC\Controller\AuthController@doLogout');

	// ----------------------------------------------------------------------------
	// 404
	
	App::missing(function($exception)
	{
		return Response::view('app.errors.404', array(), 404);
	});
}