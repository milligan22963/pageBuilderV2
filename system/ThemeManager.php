<?php
/**
 * @module ThemeManager
 *
 * @brief theme support to load and display a theme on the given site
 */
namespace afm
{	
	class ThemeManager
	{
		static private $m_instance = null;

		private $m_rootThemePath; // path to all themes
		private $m_rootThemeURL;
		private $m_activeThemePath; // active theme path
		private $m_activeThemeURL;
		private $m_activeThemeInstance = null;  // the active theme
		
		/**
		 * @fn getInstance
		 *
		 * @brief static function to get the one instance to the theme manager object
		 *
		 * @return the theme manager instance in use
		 */
		static function &getInstance()
		{
			if (self::$m_instance == null)
			{
				self::$m_instance = new ThemeManager();
				
				self::$m_instance->initialize();
			}
			
			return self::$m_instance;			
		}
		
		public function load()
		{
			$this->m_activeThemeInstance->initialize($this->m_activeThemeURL);
		}
		
		public function &getActiveTheme()
		{
			return $this->m_activeThemeInstance;
		}

		public function &getMainContentArea()
		{
			if ($this->m_activeThemeInstance != null)
			{
				return $this->m_activeThemeInstance->getMainContentArea();
			}
			return null;
		}
		
		public function preview($parentElement)
		{
			return $this->m_activeThemeInstance->preview($parentElement);
		}

		public function populate()
		{
			$systemObject = System::getInstance();

			$pageObject = & $systemObject->getPageObject();

			$extensionManager = & $systemObject->getExtensionManager();

			$extensionManager->preProcess();

			$mainContainer = SectionElement::withParent($pageObject, 'page_content');
			$populateResults = $this->m_activeThemeInstance->populate($mainContainer);

			$extensionManager->postProcess();

			return $populateResults;
		}
	
		public function getAllThemes()
		{
			$systemObject = System::getInstance();
			
			$baseDir = $systemObject->getBaseSystemDir();
			
			include_once($baseDir . 'configuration/SettingManager.php');
			
			$availableThemes = array();

			// active theme is always first
			$availableThemes[] = $this->m_activeThemeInstance;

			$themeDirs = getDirectoryList($this->m_rootThemePath);

			$settingsManager = SettingManager::getInstance();
			$activeTheme = $settingsManager->getSetting(SITE_THEME, "default");
			$path = $this->m_rootThemePath . '/';

			// load the database tables
			foreach ($themeDirs as $directory)
			{
				if ($directory != $activeTheme)
				{
					$theme = Theme::withPath($path . $directory . '/');
					$theme->initialize($this->m_rootThemeURL . $directory . '/');
					$availableThemes[] = $theme;
				}
			}

			return $availableThemes;
		}

		/**
		 * @fn initialize
		 *
		 * @brief initializes all of the requirements
		 */	 
		private function initialize()
		{
			include_once('System.php');
			include_once('Theme.php');
			
			$systemObject = System::getInstance();
			
			$baseDir = $systemObject->getBaseSystemDir();

			include_once($baseDir . 'configuration/SettingManager.php');
			
			$settingsManager = SettingManager::getInstance();

			$themeRootPath = $settingsManager->getSetting(PATH_THEME);
			
			$this->m_rootThemePath = $baseDir . $themeRootPath;

			$activeTheme = $settingsManager->getSetting(SITE_THEME, "default");

			$this->m_activeThemePath = $this->m_rootThemePath . '/' . $activeTheme . '/';

			$this->m_rootThemeURL = $systemObject->getSiteRootURL() . $themeRootPath . '/';
			$this->m_activeThemeURL = $this->m_rootThemeURL . $activeTheme . '/';
			
			$this->m_activeThemeInstance = Theme::withPath($this->m_activeThemePath);
			$this->m_activeThemeInstance->setActive(true);

			// have fun
		}
		
		private function __construct()
		{
			
		}
	}	
}	
?>