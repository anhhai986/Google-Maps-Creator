<?php
namespace GMC\Controller;

/**
 * User controller
 *
 * General controller for User related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */

class UserController extends BaseController {

    public function __construct()
    {
		if(\Auth::check())
		{
			$this->parent_user_id = (\Auth::user()->parent_id == 0) ? \Auth::user()->id : \Auth::user()->parent_id;
		}
		else
		{
			$this->parent_user_id = 0;
		}

		$this->cats = \GMC\Model\Category::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'ASC')->get();
    }

    /**
     * Users view
     */
	public function showUsers()
	{
		$pp = \Request::get('pp', 10);

		if(isset($_GET['restore']))
		{
			\GMC\Model\User::withTrashed()->restore();
		}

		$oTrash = NULL;

		if(isset($_GET['trash']))
		{
			$oTrash = \GMC\Model\User::onlyTrashed()->forceDelete();
		}

		$oDeleted = \GMC\Model\User::onlyTrashed()->get();

		$oUsers = \GMC\Model\User::orderBy('name', 'ASC')->paginate($pp);

		return \View::make('app.user.users')
			->with('oCats', $this->cats)
			->with('pp', $pp)
			->with('oTrash', $oTrash)
			->with('oDeleted', $oDeleted)
			->with('oUsers', $oUsers);
	}

    /**
     * User view
     */
	public function showUser()
	{
		// Get user
		$user_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : 0;

		if($user_id > 0)
		{
			$oUser = \GMC\Model\User::find($user_id);
			if(count($oUser) == 0) die('Not found');
		
			$oUser->active = ($oUser->active == '1') ? true : false;
		}
		else
		{
			$oUser = new \stdClass();
			$oUser->id = 0;
		}

		if(\Input::old() || $user_id == 0)
		{
			$oUser->name = \Input::old('name', '');
			$oUser->email = \Input::old('email', '');
			$oUser->timezone = \Input::old('timezone', 'UTC');
			$oUser->active = (bool)\Input::old('active', true);
			$oUser->role = \Input::old('role', 3);
		}

		$page_title = ($user_id > 0) ? trans('global.edit_user') : trans('global.new_user');

		return \View::make('app.user.user')
			->with('oCats', $this->cats)
			->with('user_id', $user_id)
			->with('oUser', $oUser)
			->with('oCats', $this->cats)
			->with('page_title', $page_title);
	}

    /**
     * User settings view
     */
	public function showUserSettings()
	{
		// Get user
		$user_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : \Auth::user()->id;

		$oUser = \GMC\Model\User::find($user_id);
		if(count($oUser) == 0) die('Not found');

		if(\Input::old('name', false))
		{
			$oUser->name = \Input::old('name');
			$oUser->email = \Input::old('email');
			$oUser->timezone = \Input::old('timezone');
		}

		$error = \Session::get('error', false);
		$message = \Session::get('message', false);

		return \View::make('app.user.settings')
			->with('parent_user_id', $this->parent_user_id)
			->with('oCats', $this->cats)
			->with('oUser', $oUser)
			->with('error', $error)
			->with('message', $message);
	}
}