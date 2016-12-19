<?php
namespace GMC\Core;

use File, Crypt;

class CategoryHelpers {

    /**
     * Get embed code
     */
    public static function getEmbed($category_id) {
		$return = '';
		$return .= "<div style='margin:0 0 10px 0'>" . trans('global.embed_text') . "</div>";
		$return .= "<textarea class='embed-area'>" . str_replace('"', '&quot;', str_replace('<', '&lt;', str_replace('>', '&gt;', '<iframe style="width:100%;height:400px;" src="' . \GMC\Core\CategoryHelpers::getLink($category_id) . '" scrolling="no" frameborder="no"></iframe>'))) . "</textarea>";
		return $return;
    }

    /**
     * Get languages
     */
    public static function getLanguages() {
		$languages = File::directories(app_path() . '/lang/');
		$aLanguages = array();

		foreach($languages as $language)
		{
			$language_abbr = str_replace('\\', '/', str_replace(app_path() . '/lang', '', (string)$language));
			$language_abbr = trim($language_abbr, '/');
			$language_file = include $language . '/map.php';
			$full_language = $language_file['language'];
			$aLanguages[$language_abbr] = $full_language;
		}
		return $aLanguages;
    }

    /**
     * Get language for category
     */
    public static function getLanguage($language = '', $format_value = false, $category_id = 0) {
		$default_language = 'en';

		if($category_id > 0 && $language == '')
		{
			$oCategory = \GMC\Model\Category::find($category_id);
			$language = (isset($oCategory->settings->language)) ? $oCategory->settings->language : $default_language;
		}

		if($language == '')
		{
			$language = $default_language;
		}

		return ($format_value) ? $language : $language;
    }

    /**
     * Get link for map
     */
    public static function getLink($category_id) {
		return url('/map?m=' . Crypt::encrypt('id=' . $category_id));
    }

    /**
     * Get link for map
     */
    public static function parseLink($str) {
		try {
			$str = Crypt::decrypt($str);
		}
		catch(\Illuminate\Encryption\DecryptException $e)
		{
			die('Map not found');
		}

		parse_str($str, $arr);
		return $arr;
    }

    /**
     * Get marker for category
     */
    public static function getMarker($marker = '', $format_value = false, $category_id = 0) {
		$path = '/assets/img/markers';
		$default_marker = '/-custom/MapMarker_Marker_Outside_Pink.png';

		if($category_id > 0 && $marker == '')
		{
			$oCategory = \GMC\Model\Category::find($category_id);
			$marker = ($oCategory->marker == '') ? $default_marker : $oCategory->marker;
		}

		if($marker == '')
		{
			$marker = $default_marker;
		}

		return ($format_value) ? $marker : url($path . $marker);
    }

    /**
     * Get markers
     */
    public static function getMarkers($required = true) {
		$markers = File::allFiles(public_path() . '/assets/img/markers/');
		$aMarkers = array();

		if(! $required)
		{
			$aMarkers[''] = '';
		}

		foreach($markers as $marker)
		{
			$file = str_replace('\\', '/', str_replace(public_path() . '/assets/img/markers', '', $marker));
			$text = trim($file, '/');
			$text = str_replace('/', ' \ ', $text);
			$text = str_replace('-', ' ', $text);
			$text = str_replace('_', ' ', $text);
			$text = str_replace('.png', '', $text);
			$text = str_replace('and', '&', $text);
			$text = str_replace('MapMarker', '', $text);
			$text = ucwords($text);
			$aMarkers[$file] = $text;
		}
		return $aMarkers;
    }

    /**
     * Get theme
     */
    public static function getTheme($theme = '', $format_value = false, $category_id = 0) {
		$path = '/assets/vendor/bootswatch/';
		$default_theme = 'lumen';

		if($category_id > 0 && $theme == '')
		{
			$oCategory = \GMC\Model\Category::find($category_id);
			$theme = (isset($oCategory->settings->theme)) ? $oCategory->settings->theme : $default_theme;
		}

		if($theme == '')
		{
			$theme = $default_theme;
		}

  	$theme = str_replace('.css', '', $theme);

		return ($format_value) ? $theme : url($path . $theme . '/bootstrap.min.css');
    }

    /**
     * Get themes
     */
    public static function getThemes() {
		$themes = File::directories(public_path() . '/assets/vendor/bootswatch/');
		$aThemes = array();

		foreach($themes as $theme)
		{
			if (\File::exists($theme . '/thumbnail.png'))
			{
				$file = basename($theme);
				$file = trim($file, '/');
				$text = str_replace('/', ' \ ', $file);
				$text = str_replace('-', ' ', $text);
				$text = str_replace('_', ' ', $text);
				$text = ucwords($text);
				$aThemes[$file] = $text;
			}
		}

		return $aThemes;
    }
}