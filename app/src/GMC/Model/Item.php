<?php
namespace GMC\Model;

use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Eloquent, DB;

Class Item extends Eloquent
{
	use SoftDeletingTrait;

    protected $table='items';
	protected $touches = array('category');

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

    public function options()
    {
        return $this->belongsToMany('GMC\Model\Option','item_options', 'item_id');
    }

    public function optionsList($named = false, $category_id = 0)
    {
        $aReturn = array();
		$sql_where = ($category_id > 0) ? "o.category_id = " . $category_id . " AND" : "";
        $return = DB::select("SELECT io.option_id, o.name FROM item_options io LEFT JOIN options o ON io.option_id = o.id WHERE " . $sql_where . " o.active = 1 AND io.item_id = " . $this->id);
        foreach($return as $ret)
        {
            $aReturn[] = ($named) ? $ret->name : $ret->option_id;
        }
        return $aReturn;
    }

    public function category()
    {
        return $this->belongsTo('GMC\Model\Category', 'category_id');
    }

    public function map_style()
    {
        return $this->hasOne('GMC\Model\MapStyle', 'map_style_id');
    }
}