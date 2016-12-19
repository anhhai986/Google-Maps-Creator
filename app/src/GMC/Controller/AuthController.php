<?php
namespace GMC\Controller;

use Request, Hash, Validator, DB, Redirect, Response, Session, Auth, Config, Password, Lang, URL, Mail;

/**
 * Authorization controller based on Laravel auth
 *
 * Used for authorization actions like signup, login and update account.
 * Works with Laravel security - http://laravel.com/docs/security.
 *
 * @package		Controllers
 * @category	Authorization
 * @version		0.01
 */

class AuthController extends BaseController {

    /**
     * Create new instance, add CSRF (Cross-site request forgery) filter on POST.
     */

    public function __construct()
    {
		$this->beforeFilter('csrf', array('on' => 'post'));
    }

    /**
     * Login view
     */

	public function showLogin()
	{
		if (\Auth::check())
		{
			return \Redirect::to('/dashboard');
		}

		// Allow sign up?
		$allow_signup = (bool)\GMC\Core\Settings::get('allow_signup', \Config::get('system.allow_signup'));
	
		return \View::make('app.auth.login')
			->with('allow_signup', $allow_signup);
	}

    /**
     * Signup view
     */

	public function showSignup()
	{
		if (\Auth::check())
		{
			return \Redirect::to('/dashboard');
		}
	
		// Allow sign up?
		$allow_signup = (bool)\GMC\Core\Settings::get('allow_signup', \Config::get('system.allow_signup'));
	
		if (! $allow_signup)
		{
			return \Response::view('app.errors.404', array(), 404);
		}
	
		return \View::make('app.auth.signup');
	}

    /**
     * Reminder view
     */

	public function showReminder()
	{
		return \View::make('app.auth.reminder');
	}

    /**
     * Reset view
     */

	public function showReset($token)
	{
		// Does token exist?
		$oReminder = \DB::table('password_reminders')->where('token', $token)->first();
	
		$valid_token = false;
	
		if(count($oReminder) > 0)
		{
			$valid_token = true;
	
			// Is token valid?
			$iMinutesAgo = \Carbon\Carbon::createFromTimeStamp(strtotime($oReminder->created_at))->diffInMinutes();
			if($iMinutesAgo > (60 * 24)) 
			{
				$valid_token = false;
				\DB::delete("DELETE FROM password_reminders WHERE token = '" . addslashes($token) . "'");
			}
		}
	
		return \View::make('app.auth.reset')
			->with('valid_token', $valid_token)
			->with('token', $token);
	}

    /**
     * Activate view
     */

	public function showActivate($token)
	{
		// Allow sign up?
		$allow_signup = (bool)\GMC\Core\Settings::get('allow_signup', \Config::get('system.allow_signup'));
		if(! $allow_signup) die();
	
		// Does token exist?
		$oReminder = \DB::table('account_activations')->where('token', $token)->first();
	
		$valid_token = false;
	
		if(count($oReminder) > 0)
		{
			$valid_token = true;
	
			\DB::table('users')
				->where('email', $oReminder->email)
				->update(array('active' => '1'));
	
			\DB::delete("DELETE FROM account_activations WHERE token = '" . addslashes($token) . "'");
		}
	
		return \View::make('app.auth.activate')
			->with('valid_token', $valid_token)
			->with('token', $token);
	}

    /**
     * Logout
     */

	public function doLogout()
	{
		\Auth::logout();
		return \Redirect::to('/login');
	}

    /**
     * Signup a new user.
	 *
	 * @param 	string $language 			POST Language code, optional
	 * @param 	string $name 				POST User (full) name
	 * @param 	string $email 				POST Email address, unique
	 * @param 	string $password 			POST Password
	 * @param 	string $repeat_password 	POST Confirm password
	 * @param 	boolean $disclaimer 		POST Agree on disclaimer, 1 or 0
	 *
	 * @return	Redirect with $error, $message and/or $errors
     */
	public function postSignup()
	{
		$allow_signup = \GMC\Core\Settings::get('allow_signup', Config::get('system.allow_signup'));
		if(! $allow_signup) die();

		$input = array(
			'language'         => Request::get('language', Config::get('app.locale')),
			'name'             => Request::get('name'),
			'email'            => Request::get('email'),
			'password'         => Request::get('password'),
			'confirm_password' => Request::get('confirm_password'),
			'disclaimer'       => (bool) Request::get('disclaimer', false)
		);

		$rules = array(
			'email'            => array('required', 'email', 'unique:users,email'),
			'password'         => array('required', 'min:5'),
			'confirm_password' => array('required', 'min:5', 'same:password'),
			'disclaimer'       => 'accepted'
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/signup')->withInput()->withErrors($validation);
		}

		// Post validated, create user
		$user = new \GMC\Model\User;

		$user->name = $input['name'];
		$user->email = $input['email'];
		$user->password = Hash::make($input['password']);

		$user->save();

		// Create and insert activation code
		$token = str_random(40);

		DB::table('account_activations')->insert(
			array(
				'email'      => $input['email'],
				'token'      => $token,
				'created_at' => \Carbon\Carbon::now()
			)
		);

		// Mail activation mail
		$link = URL::to('/activate/' . rawurlencode($token));

		$data = array(
					'mailto' => $input['email'],
					'name'   => $input['name'],
					'link'   => $link
					);

		Mail::send('emails.app.' . $input['language'] . '.activate', $data, function($message) use ($data)
		{
			$aMailConfig = Config::get('mail.from');
			$message->from(\GMC\Core\Settings::get('mail_from_address', $aMailConfig['address']), \GMC\Core\Settings::get('mail_from_name', $aMailConfig['name']));

			$message->to($data['mailto'], $data['name'])->subject('[' . \GMC\Core\Settings::get('app_title', Config::get('system.title')) . '] ' . Lang::get('global.activate_account_subject'));
		});

		return Redirect::to('/signup')->with('message', trans('global.thanks_for_signing_up'));
	}

    /**
     * Download all users in a CSV if user has right permissions.
	 *
	 * @return	CSV download
     */
	public function getUsersCsv()
	{
		if(\GMC\Core\Permission::check('Export Users CSV'))
		{
			$oUser = \GMC\Model\User::all()->toArray();

			$outstream = fopen("php://output",'r+') or die("Can't open php://output");

			 foreach ($oUser as $row) 
			 {
				fputcsv($outstream, $row);
			 }

			fclose($outstream);

			return Response::make('', 200, array(
				'Content-Description'       => 'File Transfer',
				'Content-Type'              => 'text/csv',
				'Content-Disposition'       => 'attachment; filename="'. date_format(new DateTime(), 'Y-m-d') . '_User_Export.csv"',
				'Content-Transfer-Encoding' => 'binary',
				'Expires'                   => 0,
				'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
				'Pragma'                    => 'public'
			));
		}
	}

    /**
     * Send password reset for email address. If the email address is found an email with a reset
	 * link is sent. For email template see /app/views/emails/app/[language]/reminder.blade.php.
	 *
	 * @param 	string $email 	POST Email address
	 *
	 * @return	Redirect with $message and/or $errors
     */
	public function postRemind()
	{
		$input = array(
			'language' => Request::get('language', Config::get('app.locale')),
			'email'    => Request::get('email')
		);

		$rules = array(
			'email'    => array('required', 'email', 'exists:users,email')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/reminder')->withInput()->withErrors($validation);
		}

		// Get user
		$oUser = \GMC\Model\User::where('email', '=', $input['email'])->where('active', '1')->first();

		if(count($oUser) > 0)
		{
			// Success mail
			$data = array(
						'mailto' => $input['email'],
						'name' => $oUser->name
						);

			// Override template
			Config::set('auth.reminder.email', 'emails.app.' . $input['language'] . '.reminder');

			Password::remind(array('email' => $input['email']), function($message, $user) use ($data)
			{
				$aMailConfig = Config::get('mail.from');
				$message->from(\GMC\Core\Settings::get('mail_from_address', $aMailConfig['address']), \GMC\Core\Settings::get('mail_from_name', $aMailConfig['name']));

				$message->to($data['mailto'], $data['name'])->subject('[' . \GMC\Core\Settings::get('app_title', Config::get('system.title')) . '] ' . Lang::get('global.reset_pass_subject'));
			});

			return Redirect::to('/reminder')->with('message', trans('global.password_reset_msg'));
		}
	}

    /**
     * Reset password. Once a user has followed the link sent with the password reset email,
	 * this function will actually update the password.
	 *
	 * @param 	string $token 		POST Verification code generated for reset
	 * @param 	string $pass1 		POST New password
	 * @param 	string $pass1		POST Confirm new password
	 *
	 * @return	Redirect possibly with $error or $errors
     */
	public function postReset()
	{
		$input = array(
			'token'    => Request::get('token'),
			'pass1'    => Request::get('pass1'),
			'pass2'    => Request::get('pass2')
		);

		$rules = array(
			'pass1' => array('required', 'min:5'),
			'pass2' => array('required', 'min:5', 'same:pass1')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/reset/' . Request::get('token'))->withInput()->withErrors($validation);
		}

		// Post validated, check token, find user and reset password
		$oReminder = DB::table('password_reminders')->where('token', $input['token'])->first();

		if(count($oReminder) > 0)
		{
			$oUser = DB::table('users')->where('email', $oReminder->email)->where('active', '1')->first();

			if(count($oUser) > 0)
			{
				// Delete token
				DB::delete("DELETE FROM password_reminders WHERE token = '" . addslashes($input['token']) . "'");

				DB::table('users')
					->where('email', $oReminder->email)
					->update(array('password' => Hash::make($input['pass1'])));

				return Redirect::to('/login?reset');
			}
		}
		return Redirect::to('/reset/' . Request::get('token'))->with('error', trans('global.unkown_error'));
	}

    /**
     * Validate user credentials and login on success. Also last login
	 * date will be updated and logins (login count) will be incremented.
	 *
	 * @param 	string $email 			POST E-mail address
	 * @param 	string $password 		POST Password
	 * @param 	string $remember		POST Remember login in cookie
	 *
	 * @return	Redirect with $error or $errors
     */
	public function postLogin()
	{
		$input = array(
			'email'    => Request::get('email'),
			'password' => Request::get('password'),
			'remember' => (bool) Request::get('remember', false)
		);

		$rules = array(
			'email'    => array('required', 'email', 'exists:users,email'),
			'password' => array('required', 'min:5')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/login')->withInput()->withErrors($validation);
		}

		// Post validated, Set login credentials
		$credentials = array(
			'email'    => $input['email'],
			'password' => $input['password'],
			'active' => 1
		);

		if(! Auth::attempt($credentials, $input['remember']))
		{
			return Redirect::to('/login')->withInput()->with('error', trans('global.login_failed'));
		}

        // login OK, update table
        $oUser = \GMC\Model\User::find(Auth::user()->id);

        $iLogins = $oUser->logins + 1;

        // Save last login in session
        Session::put('last_login', $oUser->last_login);
        Session::put('logins', $iLogins);

        $oUser->logins = $iLogins;
        $oUser->last_login = \Carbon\Carbon::now();
        $oUser->save();

		return Redirect::to('/dashboard');
	}

    /**
     * Login as a different user, when role = 1 (admin).
	 *
	 * @param 	string $id 			GET User ID
	 *
	 * @return	Redirect
     */
	public function getLoginAs($id)
	{
		if(Auth::user()->role == 1 && is_numeric($id))
		{
			Auth::loginUsingId($id);
			return Redirect::to('/dashboard');
		}
	}

    /**
     * Delete user(s)
	 *
	 * @param 	array $id 			POST Array containing User IDs
	 *
	 * @return	Redirect
     */
	public function postBatchUserDelete()
	{
		$aId = Request::get('id');

		if(Auth::user()->role == 1)
		{
			\GMC\Model\User::destroy($aId);
		}

		return Redirect::to('/dashboard/users?deleted');
	}

    /**
     * Update account settings. Current password is required for security reasons.
	 *
	 * @param 	int $id 			POST User ID
	 * @param 	string $name 		POST User (full) name
	 * @param 	string $email 		POST Email address
	 * @param 	string $pass1 		POST New password, optional
	 * @param 	string $pass2		POST Confirm new password, optional
	 * @param 	string $password	POST Current password for verification
	 * @param 	string $timezone 	POST Timezone, defaults to UTC
	 *
	 * @return	Redirect with $error, $message and/or $errors
     */
	public function postUpdateAccount()
	{
		$input = array(
			'id'       => Request::get('id'),
			'name'     => Request::get('name'),
			'email'    => Request::get('email'),
			'pass1'    => Request::get('pass1'),
			'pass2'    => Request::get('pass2'),
			'password' => Request::get('password'),
			'timezone' => Request::get('timezone')
		);

		// Get user
		$oUser = \GMC\Model\User::find($input['id']);

		if(count($oUser) == 0)
		{
			return Redirect::to('/dashboard/user/settings')->withInput()->with('error', 'User not found');
		}

		// Is admin?
		if(Auth::user()->id != $oUser->id && Auth::user()->role != 1)
		{
			return Redirect::to('/dashboard/user/settings')->withInput()->with('error', 'No permissions');
		}

		// Check if current password is correct
		if(! Hash::check($input['password'], $oUser->password))
		{
			return Redirect::to('/dashboard/user/settings')->withInput()->with('error', trans('global.incorrect_password'));
		}

		// Form validation
		$rules = array(
			'email'    => array('required', 'email'),
			'pass1'    => array('min:5', 'same:pass2'),
			'pass2'    => array('min:5', 'same:pass1'),
			'password' => array('required', 'min:5'),
			'name'     => array('required')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/dashboard/user/settings')->withInput()->withErrors($validation);
		}

		// Check if email is valid
		if($input['email'] != $oUser->email) {
			$oCheck = \GMC\Model\User::where('email', '=', $input['email'])->get();
			if(count($oCheck) > 0) {
			    return Redirect::to('/dashboard/user/settings')->withInput()->with('error', trans('global.email_exists'));
			} else {
				$oUser->email = $input['email'];
			}
		}

		// Check if password is changed
		if($input['pass1'] != '') {
			$oUser->password = Hash::make($input['pass1']);
		}

		// User columns
		$oUser->name = $input['name'];
		$oUser->timezone = $input['timezone'];
		$oUser->save();

		return Redirect::to('/dashboard/user/settings')->with('message', trans('global.save_success'));
	}

    /**
     * Create new user or update existing.
	 *
	 * @param 	int $id 			POST User ID
	 * @param 	int $role 			POST Role (1 = admin, 2 = moderator, 3 = user)
	 * @param 	string $name 		POST User (full) name
	 * @param 	string $email 		POST Email address
	 * @param 	string $password 	POST Password
	 * @param 	string $password	POST Current password for verification
	 * @param 	string $timezone 	POST Timezone, defaults to UTC
	 * @param 	boolean $active 	POST Timezone, defaults to UTC
	 *
	 * @return	Redirect possibly with $error or $errors
     */
	public function postAccount()
	{
		// Is admin?
		if(Auth::user()->role != 1)
		{
            return Redirect::to('/dashboard/users/user')->withInput()->with('error', 'No permissions');
		}

		$input = array(
			'id'    => Request::get('id'),
			'role' => Request::get('role', 0),
			'email' => Request::get('email'),
			'pass1' => Request::get('pass1'),
			'pass2' => Request::get('pass2'),
			'name' => Request::get('name'),
			'timezone' => Request::get('timezone'),
			'active' => (bool)Request::get('active', false)
		);

		$pass_required = ($input['id'] == 0) ? 'required' : '';

		$rules = array(
			'email'    => array('required', 'email'),
			'pass1'    => array($pass_required, 'min:5', 'same:pass2'),
			'pass2'    => array($pass_required, 'min:5', 'same:pass1'),
			'name'    => array('required')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/dashboard/users/user?id=' . $input['id'])->withInput()->withErrors($validation);
		}


		// New or update
		if($input['id'] == 0)
		{
			// New
			$oUser = new \GMC\Model\User;

			$oUser->email = $input['email'];
		}
		else
		{
			$oUser = \GMC\Model\User::find($input['id']);

			if(count($oUser) == 0)
			{
                return Redirect::to('/dashboard/users/user?id=' . $input['id'])->withInput()->with('error', 'User not found');
			}
		}

		// Check if email is valid
		if($input['email'] != $oUser->email || $input['id'] == 0)
		{
			$oCheck = \GMC\Model\User::where('email', '=', $input['email'])->get();
			if(count($oCheck) > 0)
			{
                return Redirect::to('/dashboard/users/user?id=' . $input['id'])->withInput()->with('error', Lang::get('global.email_exists'));
			}
			else
			{
				$oUser->email = $input['email'];
			}
		}

		// Check if password is changed
		if($input['pass1'] != '')
		{
			$oUser->password = Hash::make($input['pass1']);
		}

		// User columns
		$oUser->name = $input['name'];
		$oUser->timezone = $input['timezone'];$timezone;
		$oUser->active = (int)$input['active'];
		if($input['role'] > 0) $oUser->role = $input['role'];

		$oUser->save();

		return Redirect::to('/dashboard/users?saved');
	}
}