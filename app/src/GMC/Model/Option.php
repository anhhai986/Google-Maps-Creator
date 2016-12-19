<?php
namespace GMC\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Eloquent;

Class Option extends Eloquent
{
	use SoftDeletingTrait;

    protected $table='options';

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
        return $this->belongsToMany('GMC\Model\Item', 'item_options', 'item_id');
    }

    public function category()
    {
        return $this->belongsTo('GMC\Model\Category', 'category_id');
    }
}