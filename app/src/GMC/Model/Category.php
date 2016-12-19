<?php
namespace GMC\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Eloquent;

Class Category extends Eloquent
{
	use SoftDeletingTrait;

    protected $table='categories';

	/**
	 * Soft delete
	 *
	 * @var array
	 */
	protected $dates = ['deleted_at'];

	public function getAttribute($key)
	{
		$value = parent::getAttribute($key);
		if($key == 'settings' && $value)
        {
		    $value = json_decode($value);
		}
		return $value;
	}

	public function setAttribute($key, $value)
	{
		if($key == 'settings' && $value)
        {
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

    public function items()
    {
        return $this->hasMany('GMC\Model\Item', 'category_id');
    }

    public function options()
    {
        return $this->hasMany('GMC\Model\Option', 'category_id')->orderBy('name', 'ASC');
    }

    public function map_style()
    {
        return $this->hasOne('GMC\Model\MapStyle');
    }
}