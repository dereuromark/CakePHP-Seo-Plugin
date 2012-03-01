<?php
/**
* Helper class to preform some basic tasks. 
*
* @author Nick Baker
* @since 2.0
* @license MIT
*/
class SeoUtil extends Object {
	
	/**
	 * Seo configurations stored in
	 * APP/config/seo.php
	 * @var array
	 */
	public static $configs = array();
	
	/**
	 * Return version number
	 * @return string version number
	 * @access public
	 */
	static function version() {
		return "6.0.0";
	}
	
	/**
	 * Return description
	 * @return string description
	 * @access public
	 */
	static function description() {
		return "CakePHP Search Engine Optimization Plugin";
	}
	
	/**
	 * Return author
	 * @return string author
	 * @access public
	 */
	static function author() {
		return "Nick Baker, Alan Blount";
	}
	
	/**
	 * Load the SeoAppError class
	 */
	static function loadSeoError() {
		App::uses('SeoAppError', 'Seo.Error');
		//return require_once(dirname(__FILE__) . DS . '..' . DS . 'seo_app_error.php');
		return true;
	}
	
	/**
	 * Utility method to call Seo.SeoBlacklist::isBanned($ip);
	 */
	static function isBanned($ip = null) {
		App::import('Model','Seo.SeoBlacklist');
		return SeoBlacklist::isBanned($ip);
	}
	
	/**
	 * Testing getting a configuration option.
	 * @param key to search for
	 * @return mixed result of configuration key.
	 * @access public
	 */
	static function getConfig($key) {
		if (isset(self::$configs[$key])) {
			return self::$configs[$key];
		}
		//try configure setting
		if (self::$configs[$key] = Configure::read("Seo.$key")) {
			return self::$configs[$key];
		}
		//try load configuration file and try again.
		Configure::load('seo');
		self::$configs = Configure::read('Seo');
		if (self::$configs[$key] = Configure::read("Seo.$key")) {
			return self::$configs[$key];
		}
		
		return null;
	}

}