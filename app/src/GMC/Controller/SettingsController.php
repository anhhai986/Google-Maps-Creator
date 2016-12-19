<?php
namespace GMC\Controller;

/**
 * Settings controller
 *
 * General controller for Settings related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */

class SettingsController extends BaseController {

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
     * Settings view
     */
	public function showSettings()
	{
		$message = \Session::get('message', false);
		$aMailConfig = \Config::get('mail.from');

		$allow_signup = \GMC\Core\Settings::get('allow_signup', \Config::get('system.allow_signup'));
		$allow_signup = ($allow_signup == '1' || $allow_signup) ? true : false;

		return \View::make('app.settings.main')
			->with('oCats', $this->cats)
			->with('message', $message)
			->with('aMailConfig', $aMailConfig)
			->with('allow_signup', $allow_signup);
	}
}