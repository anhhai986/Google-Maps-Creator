<?php
namespace GMC\Controller;

use Auth, Request, Validator, Redirect;

/**
 * Option controller
 *
 * General controller for option related functions.
 *
 * @package		Controllers
 * @category	App
 * @version		0.01
 */
class OptionController extends BaseController {

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
     * Show options view
     */

	public function showOptions()
	{
		$pp = \Request::get('pp', 10);

		// Check cat id
		$category_id = \Request::get('category_id', 0);

		$oCat = \GMC\Model\Category::find($category_id);
		if(count($oCat) == 0) die('Not found');

		// Permission check
		$oCheck = \GMC\Model\User::find($this->parent_user_id)->categories()->find($category_id);
		if(count($oCheck) == 0) die('No permissions');

		if(isset($_GET['restore']))
		{
			\GMC\Model\Option::withTrashed()->where('user_id', $this->parent_user_id)->where('category_id', $category_id)->restore();
		}

		if(isset($_GET['trash']))
		{
			$oTrash = \GMC\Model\Option::onlyTrashed()->where('user_id', $this->parent_user_id)->where('category_id', $category_id)->forceDelete();
		}

		$oOptions = \GMC\Model\Option::leftJoin('categories AS c', 'options.category_id', '=', 'c.id')->where('options.user_id', '=', $this->parent_user_id)->where('category_id', $category_id)->orderBy('c.name', 'DESC')->orderBy('options.name', 'ASC')->paginate($pp, array('c.name as category_name', 'options.id', 'options.name', 'options.active', 'options.created_at', 'options.updated_at'));

		$page_title = trans('global.options');

		$error = \Session::get('error', false);
		$message = \Session::get('message', false);

		return \View::make('app.option.options')
			->with('parent_user_id', $this->parent_user_id)
			->with('oCats', $this->cats)
			->with('oOptions', $oOptions)
			->with('oCheck', $oCheck)
			->with('pp', $pp)
			->with('category_id', $category_id)
			->with('page_title', $page_title)
			->with('error', $error)
			->with('message', $message);
	}

    /**
     * Show option view
     */

	public function showOption()
	{
		// Check cat id
		$category_id = \Request::get('category_id', 0);

		$oCat = \GMC\Model\Category::find($category_id);
		if(count($oCat) == 0) die('Not found');

		// Permission check
		$oCheck = \GMC\Model\User::find($this->parent_user_id)->categories()->find($category_id);
		if(count($oCheck) == 0) die('No permissions');

		// Get option id
		$option_id = \Request::get('id', 0);

		if($option_id > 0)
		{
			// Get option + permission check
			$oOption = \GMC\Model\Option::where('user_id', '=', $this->parent_user_id)->find($option_id);
			if(count($oOption) == 0) die('Not found');
		
			$oOption->active = ($oOption->active == '1') ? true : false;
		}
		else
		{
			$oOption = new \stdClass();
			$oOption->id = 0;
		}

		if(\Input::old() || $option_id == 0)
		{
			$oOption->category_id = $category_id;
			$oOption->name = Input::old('name', '');
			$oOption->active = (bool)Input::old('active', true);
		}

		$page_title = $oOption->name;

		$error = \Session::get('error', false);
		$message = \Session::get('message', false);

		return \View::make('app.option.option')
			->with('parent_user_id', $this->parent_user_id)
			->with('oCats', $this->cats)
			->with('oCheck', $oCheck)
			->with('oOption', $oOption)
			->with('category_id', $category_id)
			->with('page_title', $page_title)
			->with('error', $error)
			->with('message', $message);

	}

    /**
     * Save option.
	 *
	 * @param 	string $name 			POST Category option
	 * @param 	boolean $active 		POST Is option active / visible
	 *
	 * @return	JSON: field error, general_error or general_success
     */
	public function postSave()
	{
		$input = array(
			'id'           => Request::get('id', 0),
			'category_id'  => Request::get('category_id'),
			'name'         => Request::get('name'),
			'active'       => Request::get('active', 0)
		);

		$rules = array(
			'name'         => array('required')
		);

		$validation = Validator::make($input, $rules);

		if($validation->fails())
		{
			return Redirect::to('/option?id=' . $input['id'] . '&category_id=' . $input['category_id'])->withInput()->withErrors($validation);
		}

		if($input['id'] == 0)
		{
			$oOption = new \GMC\Model\Option;
		}
		else
		{
			$oOption = \GMC\Model\Option::find($input['id']);
		}

		$oOption->user_id = $this->parent_user_id;
		$oOption->name = $input['name'];
		$oOption->active = $input['active'];
		$oOption->save();

		return Redirect::to('/dashboard/options?category_id=' . $input['category_id'])->with('message', trans('global.save_success'));
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
				if(isset($_POST['delete']))
				{
					$action = 'deleted';
					$affected = \GMC\Model\Option::where('id', '=', $id)->where('user_id', '=',  $this->parent_user_id)->delete();
				}
				if(isset($_POST['switch']))
				{
					$action = 'switched';
					$current = \GMC\Model\Option::where('id', '=', $id)->first();
					$switch = ($current->active == 1) ? 0 : 1;
					$affected = \GMC\Model\Option::where('id', '=', $id)->where('user_id', '=',  $this->parent_user_id)->update(array('active' => $switch));
				}
			}
		}

		return Redirect::to('/dashboard/options?category_id=' . Request::get('category_id') . '&' . $action);
	}

    /**
     * Download CSV.
	 *
	 * @return	CSV download
     */
	public function getDownloadCsv()
	{
        $oGet = \GMC\Model\Option::where('user_id', '=', $this->parent_user_id)
                ->get(array('name', 'active', 'updated_at', 'created_at'))
                ->toArray();

        $outstream = fopen("php://output",'r+') or die("Can't open php://output");

         foreach ($oGet as $row) 
         {
            fputcsv($outstream, $row);
         }

        fclose($outstream);

        return Response::make('', 200, array(
            'Content-Description'       => 'File Transfer',
            'Content-Type'              => 'text/csv',
            'Content-Disposition'       => 'attachment; filename="'. date_format(new DateTime(), 'Y-m-d') . '_' . trans('global.option') . '_Export.csv"',
            'Content-Transfer-Encoding' => 'binary',
            'Expires'                   => 0,
            'Cache-Control'             => 'must-revalidate, post-check=0, pre-check=0',
            'Pragma'                    => 'public'
        ));
	}
}