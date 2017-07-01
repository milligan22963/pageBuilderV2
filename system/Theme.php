<?php
/**
 * @module Theme
 *
 * @brief the base of all themes
 */

namespace afm
{
	include_once('ITheme.php');
	
	define('THEME_OVERFLOW_AREA', '*');
	
	/**
		some ideas
		  register methods to be called as needed i.e.
		  admin page, register show layout and other type functions
		  main page, register which cells are available and what to call?  Or povide a way to call extensions based on cell?
	 */
	 
	class Theme implements ITheme
	{
		private $m_themeSettings;
		private $m_themeSections = array();
		private $m_mainContentArea = null;
		
		private $m_name;
		private $m_version;
		private $m_requires;
		private $m_codeFile;
		private $m_codeClass;
		private $m_description;
		private $m_author;
		private $m_website;
		private $m_themePath;
		private $m_themeURL;
		private $m_id;
		private $m_isActive;
		private $m_isPreview;
		private $m_themeDirectory;

		public function __construct()
		{
			$this->m_id = null;
			$this->m_themeSettings = null;
			$this->m_name = null;
			$this->m_version = null;
			$this->m_requires = null;
			$this->m_codeFile = null;
			$this->m_codeClass = null;
			$this->m_description = null;
			$this->m_author = null;
			$this->m_website = null;
			$this->m_themeURL = null;
			$this->m_isActive = false;
			$this->m_isPreview = false;
		}
		
		static public function withPath($path)
		{
			$theme = new Theme();

			$theme->load($path);

			// now I know my code and class type etc.
			include_once($theme->getThemePath() . $theme->getCodeFile());

			$class = $theme->getCodeClass();
			$targetTheme = new $class;

			$targetTheme->cloneTheme($theme);

			return $targetTheme;
		}

		/**
		 * @fn initialize
		 *
		 * @copydoc ITheme::initialize
		 */
		public function initialize($themeURL)
		{
			$this->m_themeURL = $themeURL;

			if ($this->getActive() == true)
			{
				$this->configureAsActive();
			}
		}

		/**
		 * @copydoc ITheme::setActive
		 */
		public function setActive($isActive)
		{
			$this->m_isActive = $isActive;
		}

		/**
		 * @copydoc ITheme::getActive
		 */
		public function getActive()
		{
			return $this->m_isActive;
		}

		public function setPreview($isPreview)
		{
			$this->m_isPreview = $isPreview;
		}

		public function getPreview()
		{
			return $this->m_isPreview;
		}

		/**
		 * @copydoc ITheme::preview
		 */
		 public function preview(& $parentElement)
		 {
			 $results = array();

			 // by default just show our image
			 if ($parentElement != null)
			 {
				 $imageId = $this->getId() . "_img";
				 $image = ImageElement::withParent($parentElement, $imageId);

				 $image->setImageSource($this->getThemeURL() . $this->getImage());

				 $results[$this->getName()] = $image;
			 }

			 return $results;
		 }

		/**
		 * @copydoc ITheme::load
		 */
		public function load($path)
		{
			include_once('System.php');
			
			$systemObject = System::getInstance();
			
			$baseDir = $systemObject->getBaseSystemDir();

			include_once($baseDir . 'page/XmlPage.php');
			
			$this->m_themePath = $path;

			$this->setThemeDirectory(basename($path));

			// load the theme info
			$themeInfo = $this->m_themePath . 'info.xml';
						
			$xmlPage = XmlPage::withDocument($themeInfo);
			
			$themeElement = $xmlPage->getElement(THEME_ROOT_ELEMENT);
			
			if ($themeElement != null)
			{
				$this->setId($themeElement->getAttribute(THEME_ID_ATTR));
				$this->setName($themeElement->getAttribute(THEME_NAME_ATTR));
				$this->setVersion($themeElement->getAttribute(THEME_VERSION_ATTR));
				$this->setRequires($themeElement->getAttribute(THEME_REQUIRES_ATTR));
				
				$description = $themeElement->getElement(THEME_DESCRIPTION_ELEMENT);
				if ($description != null)
				{
					$this->setDescription($description->getData());
				}
				$codeElement = $themeElement->getElement(THEME_CODE_ELEMENT);
				if ($codeElement != null)
				{
					$this->setCodeFile($codeElement->getAttribute(THEME_NAME_ATTR));
					$this->setCodeClass($codeElement->getAttribute(THEME_CLASS_ATTR));
					
					$this->setThemeSettings($codeElement->getElement(THEME_SETTINGS_ELEMENT));
				}
				else
				{
					$this->setCodeFile(null);
					$this->setCodeEntryPoint(null);
				}
				
				$imageElement = $themeElement->getElement(THEME_IMAGE_ELEMENT);
				if ($imageElement != null)
				{
					$this->setImage($imageElement->getAttribute(THEME_SOURCE_ATTR));
				}

				$authorElement = $themeElement->getElement(THEME_AUTHOR_ELEMENT);
				if ($authorElement != null)
				{
					$this->setAuthor($authorElement->getAttribute(THEME_NAME_ATTR));
					$this->setWebsite($authorElement->getAttribute(THEME_WEBSITE_ATTR));
				}
			}
		}

		/**
		 * @fn populate
		 *
		 * @copydoc ITheme::populate
		 */
		public function populate(& $parentElement)
		{
			$results = array();

			if (count($this->m_themeSections) > 0)
			{
				include_once('ExtensionManager.php');
				
				$extensionManager = & ExtensionManager::getInstance();
				
				// for each registered section of the theme
				// fill in the content
				$overflowThemeArea = null;
				foreach ($this->m_themeSections as $themeSectionId=>$themeCallback)
				{
//					error_log('Calling: ' . $themeCallback);
					if ($themeSectionId != THEME_OVERFLOW_AREA)
					{
						// create each section
						$sectionElement = & $this->$themeCallback($parentElement);
						
						if ($sectionElement != null)
						{
							// Now that the content area exists
							// call the extension manager to populate any extensions there
							$results = array_merge($extensionManager->processExtension($themeSectionId, $sectionElement, $this->getPreview()), $results);

							// we may want to do this in both cases
							// right now I want the preview to adjust/add class and attributes
							if ($this->getPreview() == true)
							{
								$extensionManager->addThemeSection($themeSectionId, $sectionElement);
							}
						}
					}
					else
					{
						// theme overflow only occurs when a user has unplaced extensions
						$overflowThemeArea = $themeCallback;
					}
				}
				
				// if there is an overflow area, once everything has been loaded
				// we will check for overflow and fill that area with anything not currently placed
				if (($overflowThemeArea != null) && ($extensionManager->hasOverflow($this->getPreview()) == true))
				{
//					error_log('Process overflow');
					$sectionElement = & $this->$overflowThemeArea($parentElement);
					
					// Now that the content area exists
					// call the extension manager to populate any extensions there that don't have a home
					// we only do this in real mode, not preview
					$results = array_merge($extensionManager->processOverflowExtensions($sectionElement, $this->getPreview()), $results);					
					if ($this->getPreview() == true)
					{
						$extensionManager->addThemeSection(THEME_OVERFLOW_AREA, $sectionElement);
					}
				}
			}

			return $results;
		}

		/**
		 * @fn getMainContentArea
		 *
		 * @copydoc ITheme::getMainContentArea
		 */
		public function &getMainContentArea()
		{
			return $this->m_mainContentArea;
		}

		/**
		 * @fn activate
		 *
		 * @copydoc ITheme::initialize
		 */
		public function activate()
		{
			$this->installTables();
		}
		
		/**
		 * @fn deactivate
		 *
		 * @copydoc ITheme::deactivate
		 */
		public function deactivate()
		{
			$this->removeTables();
		}

		/**
		 * @fn reset
		 *
		 * @copydoc ITheme::reset
		 */
		public function reset()
		{
			$this->resetTables();
		}

		/**
		 * @copydoc ITheme::getThemeDirectory
		 */
		public function getThemeDirectory()
		{
			return $this->m_themeDirectory;
		}

		protected function setThemeDirectory($directory)
		{
			$this->m_themeDirectory = $directory;
		}

		public function getName()
		{
			return $this->m_name;
		}
		
		public function setName($name)
		{
			$this->m_name = $name;
		}
		
		public function setId($id)
		{
			$this->m_id = $id;
		}

		public function getId()
		{
			return $this->m_id;
		}

		public function getVersion()
		{
			return $this->m_version;
		}
		
		public function setVersion($version)
		{
			$this->m_version = $version;
		}
		
		public function getRequires()
		{
			$this->m_requires;
		}
		
		public function setRequires($requires)
		{
			$this->m_requires = $requires;
		}
		
		public function getDescription()
		{
			return $this->m_description;
		}
		
		public function setDescription($description)
		{
			$this->m_description = $description;
		}
		
		public function getCodeFile()
		{
			return $this->m_codeFile;
		}
		
		public function setCodeFile($codeFile)
		{
			$this->m_codeFile = $codeFile;
		}
		
		public function getCodeClass()
		{
			return $this->m_codeClass;
		}
		
		public function setCodeClass($codeClass)
		{
			$this->m_codeClass = $codeClass;
		}
		
		public function getAuthor()
		{
			return $this->m_author;
		}
		
		public function setAuthor($author)
		{
			$this->m_author = $author;
		}
		
		public function getWebsite()
		{
			return $this->m_website;
		}
		
		public function setImage($image)
		{
			$this->m_themeImage = $image;
		}

		public function getImage()
		{
			return $this->m_themeImage;
		}

		public function setWebsite($website)
		{
			$this->m_website = $website;
		}
		
		public function getThemePath()
		{
			return $this->m_themePath;
		}

		public function getThemeURL()
		{
			return $this->m_themeURL;
		}

		protected function setThemeURL($themeURL)
		{
			$this->m_themeURL = $themeURL;
		}

		public function getThemeSettings()
		{
			return $this->m_themeSettings;
		}
		
		public function setThemeSettings($themeSettings)
		{
			$this->m_themeSettings = $themeSettings;
		}
				
		// internal functions for derived classes
		protected function requireStyleSheet($styleSheet)
		{
			$systemObject = System::getInstance();

			$pageObject = & $systemObject->getPageObject();

			$pageObject->addCSSFile($this->m_themeURL . $styleSheet);	
		}
		
		protected function requireScript($jsFile)
		{
			$systemObject = System::getInstance();

			$pageObject = & $systemObject->getPageObject();

			$pageObject->addJSFile($this->m_themeURL . $jsFile);
		}
		
		protected function installTables()
		{
			
		}
		
		protected function removeTables()
		{
			
		}
		
		protected function resetTables()
		{
			
		}
		
		protected function setMainContentArea(& $contentArea)
		{
			$this->m_mainContentArea = $contentArea;
		}
		
		protected function cloneTheme($theme)
		{
			$this->setCodeClass($theme->getCodeClass());
			$this->setCodeFile($theme->getCodeFile());
			$this->setDescription($theme->getDescription());
			$this->setAuthor($theme->getAuthor());
			$this->setImage($theme->getImage());
			$this->setName($theme->getName());
			$this->setWebsite($theme->getWebsite());
			$this->setRequires($theme->getRequires());
			$this->setVersion($theme->getVersion());
			$this->setId($theme->getId());
			$this->setThemeDirectory($theme->getThemeDirectory());
			$this->setThemeURL($theme->getThemeURL());
		}

		protected function configureAsActive()
		{
			// allows the dervied themes to configure themselves
		}

		/**
		 * @fn registerSection
		 *
		 * @brief registers a callback to be called when a section is ready for population
		 *
		 * @param[in] $sectionId the section id the theme recognizes such as 1 or 1.1
		 * @param[in] $callback - the method to call when this section is loaded
		 *
		 * @note - the callback should return an element for extensions to populate
		 */
		protected function registerSection($sectionId, $callback)
		{
			$this->m_themeSections[$sectionId] = $callback;
		}
	}
}
?>
