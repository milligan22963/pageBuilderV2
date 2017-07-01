<?php
/**
 * @module ExtensionManager
 *
 * @brief extension support to load and display an extension where it needs to be
 */
namespace afm
{
	include_once('IExtension.php');

	// elements
	define('EXTENSION_ROOT_ELEMENT', "extension");
	define('EXTENSION_DESCRIPTION_ELEMENT', "description");
	define('EXTENSION_CODE_ELEMENT', "code");
	define('EXTENSION_PATH_ELEMENT', "path");
	define('EXTENSION_AUTHOR_ELEMENT', "author");
	define('EXTENSION_SETTINGS_ELEMENT', "settings");
	
	// attributes
	define('EXTENSION_NAME_ATTR', "name");
	define('EXTENSION_VERSION_ATTR', "version");
	define('EXTENSION_REQUIRES_ATTR', "requires");
	define('EXTENSION_CLASS_ATTR', "class");
	define('EXTENSION_TYPE_ATTR', "type");
	define('EXTENSION_WEBSITE_ATTR', "website");
		
	class ExtensionManager
	{
		static private $m_instance = null;
		
		private $m_previewExtensions;    // array of extensions that are used in the preview
		private $m_activeExtensions;	// array of extensions that have been instantiated
		private $m_availableExtensions; // all of the extensions that might be available
		private $m_activeExtensionCount; // number of active extensions in the db
		private $m_themeSections;
		
		/**
		 * @brief static function to get the one instance to the extension manager object
		 *
		 * @return the extension manager instance in use
		 */
		static function &getInstance()
		{
			if (self::$m_instance == null)
			{
				self::$m_instance = new ExtensionManager();
				
				self::$m_instance->initialize();
			}
			
			return self::$m_instance;
		}

		public function enableExtension($extensionPath, $sectionId)
		{

		}

		public function disableExtension($extensionPath)
		{

		}

		// by default this should only be preview mode
		// as we don't want non-active extensions showing up
		public function processOtherExtensions(& $parentElement)
		{
			$extensionResults = array();

			include_once('System.php');
		
			// load sections like sectionid
			$systemInstance = &System::getInstance();
			$dbIntance = & $systemInstance->getDatabase();
			
			$extensionTable = & $dbIntance->getTable(EXTENSION_TABLE);
			
			$results = $extensionTable->loadRow(" active='false'");

			if ($results != null)
			{
				$extensions = $results->fetchAll(\PDO::FETCH_OBJ);
				foreach ($extensions as $extension)
				{
					$loadedExtension = null;

					$path = trim($extension->path);
					$loadedExtension = $this->loadExtension($path);

					if ($loadedExtension != null)
					{
						$extensionResults[$loadedExtension->getName()] = $loadedExtension->preview($parentElement);
						$this->m_previewExtensions[$path] = $loadedExtension;
					}
				}
			}

			// now process any that are not in the database
			foreach ($this->m_availableExtensions as $path)
			{
				$results = $extensionTable->loadRow(" path='" . $path . "'");
				if ($results != null)
				{
					if ($results->fetch(\PDO::FETCH_OBJ) === FALSE)
					{
						// place in the proper location
						$loadedExtension = $this->loadExtension($path);
						$extensionResults[$loadedExtension->getName()] = $loadedExtension->preview($parentElement);
						$this->m_previewExtensions[$path] = $loadedExtension;
					}
				}
			}
			return $extensionResults;
		}

		public function processOverflowExtensions(& $parentElement, $isPreview)
		{
			$extensionResults = array();

			include_once('System.php');
		
			$targetArray = & $this->m_previewExtensions;
			$sourceArray = & $this->m_activeExtensions;

			if ($isPreview == false)
			{
				$sourceArray = & $this->m_previewExtensions;
				$targetArray = & $this->m_activeExtensions;					
			}

			// this one is in overflow so populate/preview it there
			// overflow should only be for those active in the database
			// but not populated by the current theme
			foreach ($this->m_availableExtensions as $path)
			{
				$loadedExtension = null;

				// this one isn't loaded yet
				if (array_key_exists($path, $targetArray) == false)
				{
					// is it in the source?
					if (array_key_exists($path, $sourceArray) == true)
					{
						$loadedExtension = $sourceArray[$path];
					}
					else
					{
						// is this one in the database?
						$systemInstance = &System::getInstance();
						$dbIntance = & $systemInstance->getDatabase();
						
						$extensionTable = & $dbIntance->getTable(EXTENSION_TABLE);
						
						$results = $extensionTable->loadRow(" path='" . $path . "'");
						if ($results != null)
						{
							if ($results->fetch(\PDO::FETCH_OBJ) !== FALSE)
							{
								$loadedExtension = $this->loadExtension($path);
							}
						}
					}

					if ($loadedExtension != null)
					{
						if ($isPreview == false)
						{
							$extensionResults[$loadedExtension->getName()] = $loadedExtension->populate($parentElement);
						}
						else
						{
							$extensionResults[$loadedExtension->getName()] = $loadedExtension->preview($parentElement);
						}
						$targetArray[$path] = $loadedExtension;
					}
				}
			}

			return $extensionResults;
		}

		public function processExtension($sectionId, & $parentElement, $isPreview)
		{
			$extensionResults = array();

			include_once('System.php');
		
			if ($sectionId != THEME_OVERFLOW_AREA)
			{
				// load sections like sectionid
				$systemInstance = &System::getInstance();
				$dbIntance = & $systemInstance->getDatabase();
				
				$extensionTable = & $dbIntance->getTable(EXTENSION_TABLE);
				
				$results = $extensionTable->loadRow(" active='true' and location like '" . $sectionId . "%' order by location asc");

				if ($results != null)
				{
					$extensions = $results->fetchAll(\PDO::FETCH_OBJ);
					foreach ($extensions as $extension)
					{
						$loadedExtension = null;

						$path = trim($extension->path);
						if (array_key_exists($path, $this->m_activeExtensions) == true)
						{
							$loadedExtension = $this->m_activeExtensions[$path];
						}
						else
						{
							$loadedExtension = $this->loadExtension($path);
						}

						if ($loadedExtension != null)
						{
							if ($isPreview == false)
							{
								$extensionResults[$loadedExtension->getName()] = $loadedExtension->populate($parentElement);
								$this->m_activeExtensions[$path] = $loadedExtension;
							}
							else
							{
	//							error_log('Processing section: ' . $sectionId);
								$extensionResults[$loadedExtension->getName()] = $loadedExtension->preview($parentElement);
								$this->m_previewExtensions[$path] = $loadedExtension;
							}
						}
					}
				}
			}

			return $extensionResults;
		}
		
		public function hasOverflow($isPreview = false)
		{
			// while the # loaded should never exceed the active account, this will cover it
			// 
			$extensionCount = count($this->m_activeExtensions);
			if ($isPreview == true)
			{
//				error_log('Overflow: ' . count($this->m_previewExtensions) . ' out of: ' . $this->m_activeExtensionCount);
				$extensionCount = count($this->m_previewExtensions);				
			}
			return ($extensionCount < $this->m_activeExtensionCount);
		}
		
		public function &getExtensionByType($extensionType)
		{
			$desiredExtension = null;
			
			foreach ($this->m_activeExtensions as $extension)
			{
				if ($extension->getType() == $extensionType)
				{
					$desiredExtension = $extension;
					break;
				}
			}
			return $desiredExtension;
		}

		public function addThemeSection($sectionId, $sectionElement)
		{
			$this->m_themeSections[$sectionId] = $sectionElement;
		}

		public function &getThemeSections()
		{
			return $this->m_themeSections;
		}
		
		// multi use extensions i.e. those showing up in more than one spot must have
		// their own entry in the extensions table
		private function loadExtension($extensionPath)
		{
			$extension = null;
			
			$systemInstance = &System::getInstance();

			$baseDir = $systemInstance->getBaseSystemDir();
			$settingsManager = & $systemInstance->getSettingsManager();

			include_once($baseDir . 'page/XmlPage.php');
			
			$fullPath = $this->m_extensionPath . '/' . $extensionPath . '/';

			$scriptPath = $systemInstance->getSiteRootURL() . $settingsManager->getSetting(PATH_EXTENSION) . '/' . $extensionPath . '/';
			
			// load information path for the extensions
			$infoFile = $fullPath . 'info.xml';
			$xmlPage = XmlPage::withDocument($infoFile);
			
			$extensionElement = $xmlPage->getElement(EXTENSION_ROOT_ELEMENT);
			
			if ($extensionElement != null)
			{
				$name = $extensionElement->getAttribute(EXTENSION_NAME_ATTR);
				$descriptionElement = $extensionElement->getElement(EXTENSION_DESCRIPTION_ELEMENT);
				$description = $descriptionElement->getData();
				$codeElement = $extensionElement->getElement(EXTENSION_CODE_ELEMENT);
				if ($codeElement != null)
				{
					$settings = $codeElement->getElement(EXTENSION_SETTINGS_ELEMENT);
	
					include_once($fullPath . $codeElement->getAttribute(EXTENSION_NAME_ATTR));
					
					$class = $codeElement->getAttribute(EXTENSION_CLASS_ATTR);

					$extension = new $class;
					
					$extension->initialize($scriptPath, $settings);
					
					$extension->setName($name);
					$extension->setDescription($description);
				}
			}
			
			return $extension;
		}
		
		/**
		 * @brief initializes all of the requirements
		 */	 
		private function initialize()
		{
			include_once('System.php');
			
			$systemInstance = & System::getInstance();
			
			$baseDir = $systemInstance->getBaseSystemDir();
			
			include_once($baseDir . 'page/XmlPage.php');
			include_once($baseDir . 'configuration/data/ExtensionData.php');
			
			$settingsManager = $systemInstance->getSettingsManager();

			$this->m_availableExtensions = array();
			$this->m_activeExtensions = array();
			
			$this->m_extensionPath = $baseDir . $settingsManager->getSetting(PATH_EXTENSION);

			// determine each of the available extensions  array_diff(scandir($directory), array('..', '.')
			foreach (array_diff(scandir($this->m_extensionPath), array('..', '.')) as $directory)
			{
				if (strstr($directory, ".") === FALSE)
				{
					// for now just having the directory is enough
					// we will load more details as needed/required
					$this->m_availableExtensions[] = $directory;
				}
			}

			// get number of active extensions			
			$extData = new ExtensionData();
			$this->m_activeExtensionCount = $extData->getExtensionCount(true);

			$this->m_extensionsLoaded = 0;
			
			$this->m_themeSections = array();
//			error_log('NumRows: ' . $this->m_activeExtensionCount);
		}
		
		
		private function __construct()
		{
			
		}
	}
}	
?>