<?php
namespace GMC\Controller;

use Auth, User, Request;

/**
 * Import controller
 *
 * General controller for import related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */
class ImportController extends BaseController {

    public function __construct()
    {
		if(Auth::check())
		{
			$this->parent_user_id = (Auth::user()->parent_id == 0) ? Auth::user()->id : Auth::user()->parent_id;
		}
		else
		{
			$this->parent_user_id = 0;
		}

		$this->cats = \GMC\Model\Category::where('user_id', '=', $this->parent_user_id)->orderBy('name', 'ASC')->get();
    }

    /**
     * Show import view
     */

	public function showImport()
	{
		return \View::make('app.edit.import')
			->with('parent_user_id', $this->parent_user_id)
			->with('oCats', $this->cats);
	}

    /**
     * Import row.
	 *
	 * @param 	string $index 			POST index count
	 * @param 	array of fields  		POST []
	 *
	 * @return	JSON
     */
	public function postRow()
	{
        // Check if user is logged in
		if(Auth::check())
		{
			$parent_user_id = (Auth::user()->parent_id == 0) ? Auth::user()->id : Auth::user()->parent_id;
		}
		else
		{
			die('Not logged in');
		}

        $category_id = Request::get('category_id');
        $append = Request::get('append');
        $index = Request::get('index');
        $count = Request::get('count');
        $value = Request::get('value');

        // Check if user owns category
        $oCat = \GMC\Model\User::find($parent_user_id)->categories()->find($category_id);
        if(count($oCat) == 0) die('No permissions');

        // Remove existing data (replace)
        if($append == '0' && $index == 0)
        {
            $affectedRows = \GMC\Model\Item::where('user_id', '=', $parent_user_id)->where('category_id', '=', $category_id)->forceDelete();
        }

        $name = (isset($value[0]) && $value[0] != '') ? $value[0] : '';
        $address = (isset($value[1]) && $value[1] != '') ? $value[1] : '';
        $phone = (isset($value[2]) && $value[2] != '') ? $value[2] : '';
        $email = (isset($value[3]) && $value[3] != '') ? $value[3] : '';
        $website = (isset($value[4]) && $value[4] != '') ? $value[4] : '';
        $description = (isset($value[5]) && $value[5] != '') ? $value[5] : '';

        if($address != '')
        {
            // Geocode address
            $geocode = \GMC\Core\GeoHelpers::geocode($address);

            if(isset($geocode['error']))
            {
                return 'error geocoding';
            }

			$oItem = new \GMC\Model\Item;
            $oItem->user_id = $parent_user_id;
            $oItem->category_id = $category_id;
            $oItem->name = $name;
            $oItem->address = $address;
            $oItem->phone = $phone;
            $oItem->email = $email;
            $oItem->website = $website;
            $oItem->description = $description;
            $oItem->lat = $geocode['latitude'];
            $oItem->lng = $geocode['longitude'];
            $oItem->active = 1;
            $oItem->save();
        }

        if($count == $index)
        {
            return 'ready';
        }
	}
}