<?php
/**
 * @module system
 *
 * @brief general system information
 */
namespace afm
{
	// defines used across the system
	define('SYSTEM_OBJ', 'system');
	define('FUNCTION_PARAMETER', "func");

	define('PAGE_DOMAIN', "page");
	define('PAGE_ADMIN_DOMAIN', "admin");
	define('PAGE_USER_DOMAIN', "user");
	define('PAGE_DEBUG_DOMAIN', "debug");
	define('PAGE_MAINTENANCE_DOMAIN', "maintenance");

	/**
	 * calls to the site/system with this defined in the post/get params indicate an ajax request
	 */
	define('HTML_REQUEST', "html");
	define('AJAX_REQUEST', "xml");
	define('JSON_REQUEST' , "json");
	
	class System
	{
		static private $m_instance = null;
		
		private $m_database = null;
		
		private $m_rootServerName = null;
		
		private $m_baseDir = null;
		
		private $m_associatedPageObject = null;
		
		private $m_pageDomain = PAGE_USER_DOMAIN;

		/**
		 * @brief static function to get the one instance to the system object
		 *
		 * @return the system instance in use
		 */
		static function &getInstance()
		{
			if (self::$m_instance == null)
			{
				self::$m_instance = new System();
				
				self::$m_instance->initialize();
				error_reporting(E_ALL); 
			}
			
			return self::$m_instance;			
		}
		
		/**
		 *
		 */
		public function &getDatabase()
		{
			include_once($this->getBaseSystemDir() . 'configuration/TableNames.php');
			
			return $this->m_database;
		}
		
		public function &getUserSession()
		{
			include_once('UserSession.php');
			
			return UserSession::getInstance();			
		}
		
		public function &getSettingsManager()
		{
			$baseDir = $this->getBaseSystemDir();
						
			include_once($baseDir . 'configuration/SettingManager.php');
			
			return SettingManager::getInstance();
		}
		
		public function &getExtensionManager()
		{
			include_once('ExtensionManager.php');
			
			return ExtensionManager::getInstance();
		}
		
		public function &getThemeManager()
		{
			include_once('ThemeManager.php');
			
			return ThemeManager::getInstance();			
		}
		
		public function &getScriptManager()
		{
			include_once('ScriptManager.php');
			
			return ScriptManager::getInstance();
		}
		
		public function setPageDomain($pageDomain)
		{
			$settingsManager = & $this->getSettingsManager();
			
			$settingsManager->addSetting(PAGE_DOMAIN, $pageDomain, false);
			
			$this->m_pageDomain = $pageDomain;
		}

		public function getPageDomain()
		{
			return $this->m_pageDomain;
		}
		
		public function setPageObject(& $pageObject)
		{
			$this->m_associatedPageObject = $pageObject;
		}

		public function & getPageObject()
		{
			return $this->m_associatedPageObject;
		}

		public function loadSite($populate = true)
		{
			include_once('ThemeManager.php');

			// load the theme
			$themeManager = ThemeManager::getInstance();
			
			// process it and any extensions
			$themeManager->load();
			
			if ($populate == true)
			{
				$themeManager->populate();
			}
		}
		
		public function populate()
		{
			include_once('ThemeManager.php');

			// load the theme
			$themeManager = ThemeManager::getInstance();

			$themeManager->populate();			
		}
		 
		/**
		 * @brief Called to return the root URL for the site allowing lower level scripts the ability to reference other
		 * 		areas without using relativity
		 *		will return something like http://www.mysite.com/subsite/ or just http://www.mysite.com depending on where the "root" is 
		 *
		 * @return the full URL of the root of the site
		 */
		public function getSiteRootURL()
		{
			$rootSiteURL = $this->m_rootServerName;
			
			if ($this->m_pageDomain == PAGE_ADMIN_DOMAIN)
			{
				$settingsManager = & $this->getSettingsManager();
			
				$adminArea = $settingsManager->getSetting(PATH_ADMIN);

				$sourceArray = explode('/', $adminArea);
				
				if (count($sourceArray) > 0)
				{
					$subSiteArray = explode('/', cleanseData($_SERVER['REQUEST_URI']));
					foreach ($subSiteArray as $dir)
					{
						// stop once we match the localArea
						if (strcmp($dir, $sourceArray[0]) != 0)
						{
							$rootSiteURL .= $dir . '/';
						}
						else
						{
							break;
						}
					}
//					error_log('site url: ' . $rootSiteURL);
				}
			}
			else
			{
				$rootSiteURL .= cleanseData(dirname($_SERVER['REQUEST_URI'])) . "/";				
			}
			
//			error_log('Site root url: ' . $rootSiteURL);
			return $rootSiteURL;
		}

		public function getParameterArray($keyValue)
		{
			$paramArray = null;

			if (isset($_GET[$keyValue]))
			{
				$paramArray = $_GET;
			}
			else if (isset($_POST[$keyValue]))
			{
				$paramArray = $_POST;
			}
			else if ($this->getContentType() == JSON_REQUEST)
			{
				$jsonData = file_get_contents("php://input");

				error_log($jsonData);
				$jsonArray = array();
				foreach (json_decode($jsonData, false) as $jsonObj)
				{
					$jsonArray[$jsonObj->name] = $jsonObj->value;
				}

				if (isset($jsonArray[$keyValue]) == true)
				{
					$paramArray = $jsonArray;
				}
			}
//			else
//			{
//				error_log('Content Type: ' . $_SERVER['CONTENT_TYPE']);
//			}
			return $paramArray;
		} 
		
		/**
		 * @brief Called to return the url to the current script
		 *         this allows lower level scripts to get their relative location
		 *
		 * @return the main page location the script name such as www.mysite.com/site/index.php
		 */ 
		public function getScriptURL($trimOptions = true)
		{
			$pageLocation = $this->m_rootServerName;
		  	$pageLocation .= cleanseData($_SERVER['REQUEST_URI']);

		  	if ($trimOptions == true)
		  	{
				if (strpos($pageLocation, '?') !== FALSE)
				{
					$pageLocation = strchr($pageLocation, '?', true);

				}
		  	}

		  	return $pageLocation;
		}

		public function getContentType()
		{
			$contentType = HTML_REQUEST;

			if (array_key_exists('CONTENT_TYPE', $_SERVER))
			{
				$contentType = cleanseData($_SERVER['CONTENT_TYPE']);
				
				if (stristr($contentType, JSON_REQUEST) !== FALSE)
				{
					include_once($this->m_baseDir . 'page/JSONPage.php');

					$contentType = JSON_REQUEST;
				}
				else if (stristr($contentType, AJAX_REQUEST) !== FALSE)
				{
					include_once($this->m_baseDir . 'page/XmlPage.php');

					$contentType = AJAX_REQUEST;
				}
				else
				{
					$contentType = HTML_REQUEST;
				}
			}
			return $contentType;
		}
			
		/**
		 * @brief the base of the root file system for the site
		 *
		 * @return the base root area such as /home/user/Documents/site/
		 */
		public function getBaseSystemDir()
		{
			if ($this->m_baseDir == null)
			{
				$this->m_baseDir = dirname(dirname(__FILE__)) . "/";
			}
			
			return $this->m_baseDir;
		}
		
		public function includeLocalFile($file)
		{
			include_once($file);
		}

		private function establishRootServer()
		{
		    $rootServer = "http";
		    if (!empty($_SERVER['HTTPS']))
		    {
		      $rootServer .= "s";
		    }
		
		    $rootServer .= "://" . cleanseData($_SERVER['HTTP_HOST']);
		    /* Check for non 80 ports */
		    if (!empty($_SERVER['SERVER_PORT']))
		    {
		      if ($_SERVER['SERVER_PORT'] != "80")
		      {
		        $rootServer .= ":" . intval($_SERVER['SERVER_PORT']);
		      }
		    }

		    $this->m_rootServerName = $rootServer;    
		}
		
		// [HTTP_ACCEPT_LANGUAGE] => en-US,en;q=0.8\n
		private function configureLocale()
		{
			$languageArray = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

			putenv('LC_ALL=' . $languageArray[0]);
			
			setlocale(LC_ALL, $languageArray[0]);
		}
		
		/**
		 * @fn initialize
		 *
		 * @brief initializes all of the system pieces
		 *        we require
		 */	 
		private function initialize()
		{
			include_once('Toolbox.php'); // local file

			if (!defined('INSTALL_IN_PROGRESS'))
			{
				$systemBaseDir = $this->getBaseSystemDir();

				include_once($systemBaseDir . 'database/Database.php');
				include_once($systemBaseDir . 'database/postgres/PostGres.php');
				include_once($systemBaseDir . 'configuration/Configuration.php');
				include_once($systemBaseDir . 'database/mysql/MySQL.php');
				
				// load config
				$xmlFile = Configuration::getSystemConfigFileName();
							
				//error_log("Loading: " . $xmlFile);
				
				$xmlConfig = new Configuration();
				
				$xmlConfig->loadFile($xmlFile);
				
				// create db instance
				switch ($xmlConfig->get(DB_TYPE))
				{
					case POSTGRES:
					{
						$this->m_database = new PGSQLDatabase();
					}
					break;
					case MYSQL:
					{
						$this->m_database = new MySQLDatabase();
					}
					break;
					default:
					{
						error_log('Bad DB Type: ' . $xmlConfig->get(DB_TYPE));
					}
					break;
				}
				
				if ($this->m_database != null)
				{
					$this->m_database->setPrefix($xmlConfig->get(DB_PREFIX));
					
					if ($this->m_database->initialize($xmlConfig->get(DB_NAME), $xmlConfig->get(DB_USER), $xmlConfig->get(DB_PASSWORD)) == true)
					{
						$tableFiles = getFileList($systemBaseDir . "configuration/data", "*", "xml", true);
						
						// load the database tables
						foreach ($tableFiles as $file)
						{
							$this->m_database->loadTable($file, false);
						}
						
						// load system type settings we need
						$this->loadSettings();
						
	//					error_log("Database is loaded");
					}
					else
					{
						error_log("Cannot initialize db: " . $xmlConfig->get(DB_NAME));
					}
				}
				else
				{
					error_log('Cannot create db.');
				}
			}
			// have fun
			
			// setup locale
			$this->configureLocale();
			
			// determine root server namee
			$this->establishRootServer();
		}
		
		private function loadSettings()
		{
			$baseDir = $this->getBaseSystemDir();
			
			include_once($baseDir . 'configuration/SettingManager.php');

			$timeZone = SettingManager::getInstance()->getSetting(OPTION_SITE_TIME_ZONE);			
			
			if ($timeZone != null)
			{
				date_default_timezone_set($timeZone);
			}
			else
			{
				date_default_timezone_set('UTC'); // default to UTC
				error_log('No timezone?');
			}
		}
				
		private function __construct()
		{
			
		}
	}	
}	
?>