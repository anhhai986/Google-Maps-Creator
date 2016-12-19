<?php
namespace GMC\Model;

use Eloquent;

Class MapStyle extends Eloquent
{

    protected $table='map_styles';
	protected $softDelete  = false;
    public $timestamps = false;

    public function category()
    {
        return $this->hasMany('GMC\Model\Category', 'category_id');
    }
}