<?php
/**
 * @module admin
 *
 * @brief admin page
 */

namespace afm
{
	define('ADMIN_ELEMENT', "admin");
	define('MODULE_ELEMENT', "module");
	define('ADMIN_TITLE_ATTR', "title");
	define('CODE_ATTR', "code");
	define('JS_ATTR', "js");
	define('ENABLED_ATTR', "enabled");
	define('OPTION_ATTR', "option");
	define('CLASS_ATTR', "class");
	define('VISIBILITY_ATTR', "visibility");

	// only admins
	define('ADMIN_AUDIANCE', 'admin');

	// users and admins
	define('USER_AUDIANCE', 'user');

	// unregistered users along with users and admins
	define('ALL_AUDIANCE', 'all');
	
	class AdminOption
	{
		private $m_title;
		private $m_code;
		private $m_enabled;
		private $m_option;
		private $m_class;
		private $m_jsFile;
		private $m_visibibility;

		public function __construct($title, $code, $jsFile, $class, $option, $visibility, $enabled)
		{
			$this->m_title = $title;
			$this->m_code = $code;
			$this->m_enabled = $enabled;
			$this->m_class = $class;
			$this->m_option = $option;
			$this->m_jsFile = $jsFile;
			$this->m_visibility = $visibility;
		}
		
		public function getTitle()
		{
			return $this->m_title;
		}
		
		public function getCode()
		{
			return $this->m_code;
		}
		
		public function getClass()
		{
			return $this->m_class;
		}
		
		public function getJSFile()
		{
			return  $this->m_jsFile;
		}
		
		public function getEnabled()
		{
			return $this->m_enabled;
		}
		
		public function getOption()
		{
			return $this->m_option;
		}

		public function getVisibility()
		{
			return $this->m_visibility;
		}
	}
	
	/**
	 * Load option set for the admin page - all admin functions are extensible via the admin.xml file
	 */
	function loadAdminOptions()
	{
		$systemObj = & System::getInstance();
	
		include_once($systemObj->getBaseSystemDir() . 'page/XmlPage.php');
	
		$optionArray = array();
		
		$xmlPage = XmlPage::withDocument('admin.xml');
			
		/**
		 * Need to pull out the option file data including
		 *   title - the title for the menu
		 *   class - the class for the code
		 *   option - the option for the command
		 *   code - the code to load
		 *   enabled - true or flase
		 */
		
		/* The main node is admin followed by each of the entries */
		$adminNode = $xmlPage->getNode(ADMIN_ELEMENT);
		if ($adminNode != null)
		{
			foreach ($adminNode->getElements(MODULE_ELEMENT) as $module)
			{
				$enabled = $module->getAttribute(ENABLED_ATTR);
				
				if (strcasecmp($enabled, "true") == 0)
				{
					$title = $module->getAttribute(ADMIN_TITLE_ATTR);
					$code = $module->getAttribute(CODE_ATTR);
					$class = $module->getAttribute(CLASS_ATTR);
					$option = $module->getAttribute(OPTION_ATTR);
					$jsFile = $module->getAttribute(JS_ATTR);
					$visibility = $module->getAttribute(VISIBILITY_ATTR);
					$enabled = $module->getAttribute(ENABLED_ATTR);

					$adminOption = new AdminOption($title, $code, $jsFile, $class, $option, $visibility, $enabled);
					
					$optionArray[$option] = $adminOption;
				}
			}
		}
		else
		{
			error_log('No admin element');
		}
		
		return $optionArray;	
	}
	
	function showAdminPage(& $adminOptions)
	{
		$systemObj = & System::getInstance();
		
		$pageObject = & $systemObj->getPageObject();

		$settingsManager = & $systemObj->getSettingsManager();
		$extensionManager = & $systemObj->getExtensionManager();
		$userSession = & $systemObj->getUserSession();

		// do the defaults before processing the theme specific
		// DWM - will need to add in translation
		$pageObject->setTitle($settingsManager->getSetting(SITE_TITLE) . ' - Administration');
		$pageObject->requireCSS('SITE');
		$pageObject->addCSSFile('css/admin.css', SITE_FILE);
		
		$systemObj->loadSite();
		
		// setup our menus
		
		$menuWidget = & $extensionManager->getExtensionByType(MENU_TYPE);
		$loginWidget = & $extensionManager->getExtensionByType(LOGIN_TYPE);
		
		if ($menuWidget != null)
		{
			$command = $systemObj->getScriptURL();
						
			if ($userSession->isLoggedIn() == true)
			{
				// show admin only options in addition to all of the others
				if ($userSession->getUserType() == USER_TYPE_ADMIN)
				{
					foreach ($adminOptions as $adminOption)
					{
						if ($adminOption->getEnabled() == true)
						{
							$menuId = 'admin_' . $adminOption->getOption();
							
							$menuWidget->addEntry($menuId, $adminOption->getTitle(), 'admin_menu', $command . '?' . ADMIN_OPTION . '=' . $adminOption->getOption());
						}
					}
				}
			}
			else
			{
				foreach ($adminOptions as $adminOption)
				{
					if (($adminOption->getEnabled() == true) && ($adminOption->getVisibility() == ALL_AUDIANCE))
					{
						$menuId = 'admin_' . $adminOption->getOption();
						
						$menuWidget->addEntry($menuId, $adminOption->getTitle(), 'admin_menu', $command . '?' . ADMIN_OPTION . '=' . $adminOption->getOption());
					}					
				}
			}
			if ($loginWidget == null)
			{
				$menuWidget->addEntry('admin_login', 'Logout', 'admin_menu', $command);
			}
		}
		else if ($loginWidget == null)
		{
			// add our own links in...
			$div = $pageObject->addDiv('login_div');
			
			if ($userSession->isLoggedIn() == false)
			{
				$pageObject->addAnchor('login_link', $systemObj->getScriptURL(), 'Login', $div);
			}
			else
			{
				$pageObject->addAnchor('login_link', $systemObj->getScriptURL(), 'Logout', $div);		
			}
		}		
	}
}
?>
