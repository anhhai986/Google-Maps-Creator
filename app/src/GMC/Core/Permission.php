<?php
namespace GMC\Core;

class Permission {

    /**
     * Check permissions
     */
    public static function check($permission, $override = false) {

		switch($permission)
		{
			case 'Export Users CSV': 
				return (Auth::user()->role == 1) ? true : false; 
				break;
		}

    }
}