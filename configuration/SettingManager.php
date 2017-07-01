<?php
/**
 * @module SettingManager
 *
 * @brief manage all settings stored/created in the db
 *   lazy read i.e. upon request
 *   direct write
 */
namespace afm
{
	// define some standard default settings which we might find in our settings file
	define('SITE_TITLE', "title");
	define('SITE_TAGLINE', "tagline");
	define('SITE_ADMIN_NAME', "admin_name");
	define('SITE_ADMIN_EMAIL', "admin_email");
	define('SITE_HASH_SALT', "hash_salt");
	define('SITE_USER_NAME_SALT', "username_salt");
	define('SITE_REGISTRATION_SALT', "registration_salt");
	define('SITE_SESSION_LENGTH', "session_length");
	define('SITE_THEME', "theme");

	define('PATH_ADMIN', "admin_path");
	define('PATH_ASSET', "asset_path");
	define('PATH_CONTENT', "content_path");
	define('PATH_EXTENSION', "extension_path");
	define('PATH_THEME', "theme_path");

	define('OPTION_ALLOW_NEW_USERS', "allow_new_users");
	define('OPTION_ADMIN_CREATE_USERS', "admin_create_users");
	define('OPTION_ALLOW_USER_UPLOADS', "user_uploads");
	define('OPTION_ALLOW_AUTO_ACTIVATE', "auto_activate_registration");
	define('OPTION_ALLOW_USER_ACTIVATE', "user_activate");
	define('OPTION_SITE_TIME_ZONE', "time_zone");

	class SettingManager
	{
		static private $m_instance = null;
		
		private $m_settings = null;
		
		/**
		 * @fn getInstance
		 *
		 * @brief static function to get the one instance to the settings manager object
		 *
		 * @return the settings manager instance in use
		 */
		static function &getInstance()
		{
			if (self::$m_instance == null)
			{
				self::$m_instance = new SettingManager();
				
				self::$m_instance->initialize();				
			}
			
			return self::$m_instance;			
		}
		
		/**
		 * @fn initialize
		 *
		 * @brief initializes all of the system pieces
		 *        we require
		 */	 
		private function initialize()
		{
			$this->m_settings = array();
		}
		
		private function loadSetting($name, $default)
		{
			include_once('data/Settings.php');
			
			$setting = new Setting();
			
			if ($setting->getSetting($name) == true)
			{
				$this->m_settings[$name] = $setting->getValue();
			}
			else
			{
				$this->m_settings[$name] = $default;
			}
		}
		
		public function getAllSettings($readOnly = false, $adminEditable = true)
		{
			$setting = new Setting();

			return $setting->getAllSettings($readOnly, $adminEditable);
		}

		public function addSetting($settingName, $value, $persist = false)
		{
			$this->m_settings[$settingName] = $value;
			
			if ($persist == true)
			{
				$setting = Setting::withNameAndValue($settingName, $value);
				$setting->setActive(true);
				$setting->save();
			}
		}
		
		public function getSetting($settingName, $default = null)
		{
			if (array_key_exists($settingName, $this->m_settings) == false)
			{
				$this->loadSetting($settingName, $default);
			}
			
			return $this->m_settings[$settingName];
		}

		public function setSetting($settingName, $value)
		{
			$setting = new Setting();
			
			// see if it is in the db, if so replace it otherwise skip over it
			// with the assumption it isn't persistent.  If it should be then
			// the user should "addSetting" instead
			if ($setting->getSetting($settingName) == true)
			{
				$setting->setValue($value);
				$setting->save();
			}
		}

		public function getAllGroups($adminEditable = true)
		{
			include_once('data/SettingGroup.php');
			
			$group = new SettingGroup();

			return $group->getAllGroups($adminEditable);
		}

		
		private function __construct()
		{
		}
	}	
}	
?>