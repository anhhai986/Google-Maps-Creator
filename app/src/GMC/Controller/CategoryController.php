<?php
namespace GMC\Controller;

use Auth, Request, Validator, Redirect;

/**
 * Category controller
 *
 * General controller for category related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */
class CategoryController extends BaseController {

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
     * Category view
     */
	public function showCategory()
	{
		// Get category
		$category_id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? $_GET['id'] : 0;

		if($category_id > 0)
		{
			$oCat = \GMC\Model\User::find($this->parent_user_id)->categories()->find($category_id);
			if(count($oCat) == 0) die('Not found');
		
			$oCat->active = ($oCat->active == '1') ? true : false;
			$theme = (isset($oCat->settings->theme)) ? $oCat->settings->theme : '';
			$language = (isset($oCat->settings->language)) ? $oCat->settings->language : '';
			$map_style_id = $oCat->map_style_id;
		}
		else
		{
			$oCat = new \stdClass();
			$oCat->id = 0;
			$theme = '';
			$language = '';
			$map_style_id = 1;
		}

		if(\Input::old() || $category_id == 0)
		{
			$oCat->name = \Input::old('name', '');
			$oCat->marker = \Input::old('marker', '');
			$oCat->active = (bool)\Input::old('active', true);
			$theme = \Input::old('theme', '');
			$language = \Input::old('language', '');
			$map_style_id = \Input::old('map_style_id', 1);
		}

		$page_title = ($category_id > 0) ? $oCat->name : trans('global.new_category');

		return \View::make('app.category.category')
			->with('parent_user_id', $this->parent_user_id)
			->with('oCats', $this->cats)
			->with('oCat', $oCat)
			->with('category_id', $category_id)
			->with('page_title', $page_title)
			->with('theme', $theme)
			->with('language', $language)
			->with('map_style_id', $map_style_id);
	}

    /**
     * Add a new category or update existing.
	 *
	 * @param 	integer $id 			POST id, 0 = insert
	 * @param 	string $name 			POST Category name
	 * @param 	boolean $active 		POST Is category active / visible
	 *
	 * @return	JSON: field error, general_error or general_success
     */
	public function postSave()
	{
		$input = array(
			'id'           => Request::get('id', 0),
			'map_style_id' => Request::get('map_style_id'),
			'name'         => Request::get('name'),
			'marker'       => Request::get('marker'),
			'theme'        => Request::get('theme'),
			'language'     => Request::get('language'),
			'active'       => Request::get('active', 0)
		);

		$rules = array(
			'name'         => array('required')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/dashboard')->withInput()->withErrors($validation);
		}

		if($input['id'] == 0)
		{
			$oCat = new \GMC\Model\Category;
		}
		else
		{
			$oCat = \GMC\Model\Category::find($input['id']);
		}

		$oCat->user_id = $this->parent_user_id;
		$oCat->map_style_id = $input['map_style_id'];
		$oCat->name = $input['name'];
		$oCat->marker = $input['marker'];
		$oCat->active = $input['active'];
		$oCat->settings = array(
			'language' => $input['language'],
			'theme' => $input['theme']
		);
		$oCat->save();

		return Redirect::to('/dashboard')->with('message', trans('global.save_success'));
	}

    /**
     * Delete cat(s)
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
				$affected = \GMC\Model\Category::where('id', '=', $id)->where('user_id', '=',  $this->parent_user_id)->delete();
			}
		}

		return Redirect::to('/dashboard?deleted');
	}

    /**
     * Download CSV.
	 *
	 * @return	CSV download
     */
	public function getDownloadCsv()
	{
        $oGet = \GMC\Model\Category::where('user_id', '=', $this->parent_user_id)->get(array('name', 'active', 'updated_at', 'created_at'))->toArray();

        $outstream = fopen("php://output",'r+') or die("Can't open php://output");

         foreach ($oGet as $row) 
         {
            fputcsv($outstream, $row);
         }

        fclose($outstream);

        return Response::make('', 200, array(
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => 'text/csv',
            'Content-Disposition'       => 'attachment; filename="'. date_format(new DateTime(), 'Y-m-d') . '_' . trans('global.category') . '_Export.csv"',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'                    => 'public'
        ));
	}
}