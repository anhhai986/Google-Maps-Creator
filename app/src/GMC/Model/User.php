<?php
namespace GMC\Model;

use Eloquent;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use SoftDeletingTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * Soft delete
	 *
	 * @var boolean
	 */
	protected $dates = ['deleted_at'];

	public function getAttribute($key)
	{
		$value = parent::getAttribute($key);
		if ($key == 'settings' && $value) {
			$value = json_decode($value);
		}
		return $value;
	}

	public function setAttribute($key, $value)
	{
		if ($key == 'settings' && $value) {
			$value = json_encode($value);
		}
		parent::setAttribute($key, $value);
	}

	public function toArray()
	{
		$attributes = parent::toArray();
		if(isset($attributes['settings']))
        {
			$attributes['settings'] = json_decode($attributes['settings']);
		}
		return $attributes;
	}

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function getRememberToken()
	{
		return $this->remember_token;
	}

	public function setRememberToken($value)
	{
		$this->remember_token = $value;
	}

	public function getRememberTokenName()
	{
		return 'remember_token';
	}

    public function parent_user()
    {
        return $this->belongs_to('GMC\Model\User','parent_id');
    }

    public function child_users()
    {
        return $this->has_many('GMC\Model\User','parent_id');
    }

    public function settings()
    {
        return $this->hasMany('GMC\Model\Setting');
    }

    public function categories()
    {
        return $this->hasMany('GMC\Model\Category');
    }
}