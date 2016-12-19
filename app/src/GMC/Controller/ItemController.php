<?php
namespace GMC\Controller;

use Auth, Request, Redirect, Validator, User, Config, Input, Response, DateTime;

/**
 * Item controller
 *
 * General controller for item related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */
class ItemController extends BaseController {

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
     * Show items view
     */

	public function showItems()
	{
		// Check cat id
		$category_id = Request::get('category_id', 0);

		$oCat = \GMC\Model\Category::find($category_id);
		if(count($oCat) == 0) die('Not found');

		// Permission check
		$oCheck = \GMC\Model\User::find($this->parent_user_id)->categories()->find($category_id);
		if(count($oCheck) == 0) die('No permissions');

		$oOption = \GMC\Model\Option::where('user_id', '=', $this->parent_user_id)->where('category_id', '=', $category_id)->where('active', '=', 1)->orderBy('name', 'ASC NOCASE')->get();

		$tags = '';
		if(count($oOption) > 0)
		{
			foreach($oOption as $option)
			{
				$tags .= '{"id": ' . $option->id . ', "text": "' . $option->name . '"},';
			}
		}
		$tags = rtrim($tags, ',');

		$error = \Session::get('error', false);
		$message = \Session::get('message', false);

		return \View::make('app.item.items')
			->with('parent_user_id', $this->parent_user_id)
			->with('category_id', $category_id)
			->with('oCheck', $oCheck)
			->with('oCats', $this->cats)
			->with('oCat', $oCat)
			->with('tags', $tags)
			->with('error', $error)
			->with('message', $message);
	}

    /**
     * Show item view
     */

	public function showItem()
	{
		// Check cat id
		$category_id = Request::get('category_id', 0);

		$oCat = \GMC\Model\Category::find($category_id);
		if(count($oCat) == 0) die('Not found');

		// Permission check
		$oCheck = \GMC\Model\User::find($this->parent_user_id)->categories()->find($category_id);
		if(count($oCheck) == 0) die('No permissions');

		$oOption = \GMC\Model\Option::where('user_id', '=', $this->parent_user_id)->where('category_id', '=', $category_id)->where('active', '=', 1)->orderBy('name', 'ASC NOCASE')->get();

		$tags = '';
		if(count($oOption) > 0)
		{
			foreach($oOption as $option)
			{
				$tags .= '{"id": ' . $option->id . ', "text": "' . $option->name . '"},';
			}
		}
		$tags = rtrim($tags, ',');

		// Get item
		$item_id = Request::get('id', 0);

		if($item_id > 0)
		{
			$oItem = \GMC\Model\Item::find($item_id);
			if(count($oItem) == 0) die('Not found');
		
			// Permission check
			$oCheck = \GMC\Model\User::find($this->parent_user_id)->categories()->find($oItem->category_id)->items()->find($item_id);
			if(count($oCheck) == 0) die('No permissions');
		
			$options = implode(',', $oItem->optionsList(false, $category_id));
		
			$oItem->category_id = $category_id;
			$oItem->options = $options;
			$oItem->active = ($oItem->active == '1') ? true : false;
		}
		else
		{
			$oItem = new \stdClass();
			$oItem->id = 0;
		}

		if(Input::old() || $item_id == 0)
		{
			$oItem->category_id = Input::old('category_id', Input::get('category_id'));
			$oItem->name = Input::old('name', '');
			$oItem->options = Input::old('options', '');
			$oItem->address = Input::old('address', '');
			$oItem->image = Input::old('image', '');
			$oItem->marker = Input::old('marker', '');
			$oItem->phone = Input::old('phone', '');
			$oItem->email = Input::old('email', '');
			$oItem->website = Input::old('website', '');
			$oItem->description = Input::old('description', '');
			$oItem->description = Input::old('description', '');
			$oItem->active = (bool)Input::old('active', true);
		}

		$crumb_title = ($item_id > 0) ? trans('global.edit_item') : trans('global.new_item');
		$page_title = ($item_id > 0) ? $oItem->name : trans('global.new_item');

		$error = \Session::get('error', false);
		$message = \Session::get('message', false);
		$geoError = \Session::get('geoError', false);

		return \View::make('app.item.item')
			->with('parent_user_id', $this->parent_user_id)
			->with('category_id', $category_id)
			->with('oCats', $this->cats)
			->with('oCat', $oCat)
			->with('oItem', $oItem)
			->with('tags', $tags)
			->with('crumb_title', $crumb_title)
			->with('page_title', $page_title)
			->with('error', $error)
			->with('geoError', $geoError)
			->with('message', $message);
	}

    /**
     * Add a new item or update existing.
	 *
	 * @param 	integer $id 			POST id, 0 = insert
	 * @param 	string $name 			POST Item name
	 * @param 	boolean $active 		POST Is item active / visible
	 *
	 * @return	JSON: field error, general_error or general_success
     */

	public function postSave()
	{
		$description = (Request::get('description') == '<p><br></p>') ? NULL : Request::get('description');
		$input = array(
			'id'                 => Request::get('id', 0),
			'category_id'        => Request::get('category_id'),
			'name'               => Request::get('name'),
			'options'            => Request::get('options'),
			'address'            => Request::get('address'),
			'phone'              => Request::get('phone'),
			'email'              => Request::get('email'),
			'website'            => Request::get('website'),
			'description'        => $description,
			'marker'             => Request::get('marker'),
			'active'             => Request::get('active', 0)
		);

		$rules = array(
			'name'               => array('required'),
			'category_id'        => array('required'),
			'address'            => array('required'),
			'email'              => array('email')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/dashboard/item?category_id=' . $input['category_id'] . '&id=' . $input['id'])->withInput()->withErrors($validation);
		}

		// Geocode address
		$geocode = \GMC\Core\GeoHelpers::geocode($input['address']);

		if(isset($geocode['error']))
		{
			return Redirect::to('/dashboard/item?category_id=' . $input['category_id'] . '&id=' . $input['id'])->withInput()->with('geoError', $geocode['msg']);
		}

		// Permission check
		$oCheck = \GMC\Model\User::find($this->parent_user_id)->categories()->find($input['category_id']);
		if(count($oCheck) == 0) die('No permissions');

		if($input['id'] == 0)
		{
			$oItem = new \GMC\Model\Item;
		}
		else
		{
			$oItem = \GMC\Model\Item::find($input['id']);
            // Detach options
            $oItem->options()->detach();
		}

		$oItem->user_id = $this->parent_user_id;
		$oItem->category_id = $input['category_id'];
		$oItem->name = $input['name'];
		$oItem->address = $input['address'];
		$oItem->phone = $input['phone'];
		$oItem->email = $input['email'];
		$oItem->website = $input['website'];
		$oItem->lat = $geocode['latitude'];
		$oItem->lng = $geocode['longitude'];
		$oItem->description = $input['description'];
		$oItem->marker = $input['marker'];
		$oItem->active = $input['active'];
		$oItem->save();

        if($input['options'] != '')
        {
            // Insert new options
            $aOptions = explode(',', $input['options']);
            foreach($aOptions as $option)
            {
                $bAdd = true;

                if(is_numeric($option))
                {
                    // Check if option exists by id
                    $oOption = \GMC\Model\Option::find($option);
                    if(count($oOption) > 0)
                    {
                        $oItem->options()->attach($oOption->id);
                        $bAdd = false;
                    }
                }
				else
				{
                    // Check if option exists by name
                    $oOption = \GMC\Model\Option::where('name', '=', $option)->where('category_id', '=', $input['category_id'])->first();
                    if(count($oOption) > 0)
                    {
                        $oItem->options()->attach($oOption->id);
                        $bAdd = false;
                    }
				}

                if($bAdd)
                {
                    $oOption = new \GMC\Model\Option;
                    $oOption->user_id = $this->parent_user_id;
                    $oOption->category_id = $input['category_id'];
                    $oOption->name = $option;
                    $oOption->active = 1;
                    $oOption->undeletable = 0;
                    $oOption->save();

                    // Attach option
                    $oItem->options()->attach($oOption->id);
                }
            }
        }

		return Redirect::to('/dashboard/items?category_id=' . $input['category_id'])->with('message', trans('global.save_success'));
	}

    /**
     * Delete item(s)
	 *
	 * @param 	array $id 			POST Array containing IDs
	 *
	 * @return	Redirect
     */

	public function postBatchDelete()
	{
		if(Auth::check())
		{
			foreach(Request::get('id', array()) as $id)
			{
				$affected = \GMC\Model\Item::where('id', '=', $id)->where('user_id', '=',  $this->parent_user_id)->delete();
			}
		}

		return Redirect::to('/dashboard/items?category_id=' . Request::get('category_id') . '&deleted');
	}

    /**
     * Download CSV.
	 *
	 * @return	CSV download
     */

	public function getDownloadCsv()
	{
        $category_id = Request::get('category_id', 0);
        $oGet = \GMC\Model\Item::where('user_id', '=', $this->parent_user_id)
                ->where('category_id', '=', $category_id)
                ->get(array('name', 'address', 'phone', 'email', 'website', 'description'))
                ->toArray();

        $outstream = fopen("php://output",'r+') or die("Can't open php://output");

		foreach ($oGet as $row) 
		{
			fputcsv($outstream, $row, ';');
		}

        fclose($outstream);

        return Response::make('', 200, array(
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => 'text/csv',
            'Content-Disposition'       => 'attachment; filename="'. date_format(new DateTime(), 'Y-m-d') . '_' . trans('global.item') . '_Export.csv"',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'                    => 'public'
        ));
	}

    /**
     * Upload file.
	 *
	 * @return	File info
     */

	public function postUpload()
	{
		$destinationPath = app_path() . '/storage/uploads/' . md5($this->parent_user_id . Config::get('app.key'));

		$file = Input::file('file');
		$category_id = Request::get('category_id', 0);

		if($file == '')
		{
			return Redirect::to('/dashboard/import?category_id=' . $category_id)->withInput()->with('error', trans('global.no_file_selected'));
		}

		$filename = $file->getClientOriginalName();
		$extension = $file->getClientOriginalExtension();

		if($extension != 'csv')
		{
			return Redirect::to('/dashboard/import?category_id=' . $category_id)->withInput()->with('error', trans('global.only_csv_allowed'));
		}

		$uploadSuccess = Input::file('file')->move($destinationPath, $filename);

		if($uploadSuccess)
		{
			return Redirect::to('/dashboard/import?category_id=' . $category_id); //->withInput()->with('message', trans('global.upload_success'));
		} else {
			return Redirect::to('/dashboard/import?category_id=' . $category_id)->withInput()->with('error', trans('global.upload_error'));
		}
	}
}