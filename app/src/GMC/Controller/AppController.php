<?php
namespace GMC\Controller;

use Request, Validator, Redirect;

/**
 * App controller
 *
 * General controller for app related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */
class AppController extends BaseController {

    /**
     * Save app settings.
	 *
	 * @param 	string $app_title 			POST Language code, optional
	 * @param 	string $mail_from_name 		POST User (full) name
	 * @param 	string $mail_from_address 	POST Email address, unique
	 * @param 	boolean $allow_signup 		POST Password
	 *
	 * @return	JSON: field error, general_error or general_success
     */
	public function postSettings()
	{
		$input = array(
			'app_title'          => Request::get('app_title'),
			'mail_from_name'     => Request::get('mail_from_name'),
			'mail_from_address'  => Request::get('mail_from_address'),
			'allow_signup'       => Request::get('allow_signup', '0')
		);

		$rules = array(
			'app_title'          => array('required'),
			'mail_from_name'     => array('required'),
			'mail_from_address'  => array('required', 'email')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/dashboard/settings')->withInput()->withErrors($validation);
		}

		// Save settings
		\GMC\Core\Settings::set('app_title', $input['app_title']);
		\GMC\Core\Settings::set('mail_from_name', $input['mail_from_name']);
		\GMC\Core\Settings::set('mail_from_address', $input['mail_from_address']);
		\GMC\Core\Settings::set('allow_signup', $input['allow_signup']);

		return Redirect::to('/dashboard/settings')->with('message', trans('global.save_success'));
	}
}