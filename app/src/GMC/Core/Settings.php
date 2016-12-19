<?php
namespace GMC\Core;

class Settings {

    /**
     * Get setting
     */
    public static function get($name, $default = NULL, $user_id = 0) {
		$oSetting = \GMC\Model\Setting::where('name', $name)->where('user_id', $user_id)->first();
		if(! empty($oSetting))
		{
			return $oSetting->value;
		}
		elseif($default != NULL)
		{
			return $default;
		}
		else
		{
			return NULL;
		}
    }

    /**
     * Set setting
     */
    public static function set($name, $value, $user_id = 0) {

		$oSetting = \GMC\Model\Setting::where('name', $name)->where('user_id', $user_id);

		if($oSetting->exists()) 
		{
			if($value == NULL)
			{
				$oSetting->delete();
			}
			else
			{
				$oSetting->update(array(
					'value' =>$value
				));
			}
		}
		elseif($value != NULL)
		{
			$oSetting = new \GMC\Model\Setting;

			$oSetting->name = $name;
			$oSetting->value = $value;
			$oSetting->user_id = $user_id;
			$oSetting->save();
		}
		return true;
    }

}