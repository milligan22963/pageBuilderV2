<?php
/*
 * ScriptManager - used to manage scripts such that other parts of the system don't have to know
 *					or care about locations etc.
 */

namespace afm
{
	define('SCRIPT_ROOT_ELEMENT', "scripts");
	define('PATHS_ELEMENT', "paths");
	define('FILES_ELEMENT', "files");
	define('PATH_ELEMENT', "path");
	define('FILE_ELEMENT', "file");
	define('JS_ELEMENT', "js");
	define('CSS_ELEMENT', "css");
	define('REQUIRES_ELEMENT', "requires");
	define('SM_TYPE_ATTR', "type");
	define('PATH_ATTR', "path");
	define('ID_ATTR', "id");
	define('PATH_ID_ATTR', "pathId");
	define('CODEFILE_ATTR', "codefile");
	
	class ScriptManager
	{
		static private $m_instance = null;
		
		private $m_jsPaths = array();
		private $m_jsFiles = array();
		private $m_jsDependencies = array();
		private $m_cssDependencies = array();

		private $m_cssPaths = array();
		private $m_cssFiles = array();
		
		/**
		 * @fn getInstance
		 *
		 * @brief static function to get the one instance to the system object
		 *
		 * @return the system instance in use
		 */
		static function &getInstance()
		{
			if (self::$m_instance == null)
			{
				self::$m_instance = new ScriptManager();
				
				self::$m_instance->initialize();
			}
			
			return self::$m_instance;
		}
		
		public function requireScript($jsId)
		{
			$success = false;

			if (array_key_exists($jsId, $this->m_jsFiles) == true)
			{
				$systemObj = System::getInstance();

				$page = $systemObj->getPageObject();

				if (array_key_exists($jsId, $this->m_jsDependencies) == true)
				{
					foreach ($this->m_jsDependencies[$jsId] as $dependency)
					{
						$this->requireScript($dependency);
					}
				}
				if (array_key_exists($jsId, $this->m_cssDependencies) == true)
				{
					foreach ($this->m_cssDependencies[$jsId] as $dependency)
					{
						$this->requireStyle($dependency);
					}
				}
				$page->addJSFile($this->m_jsFiles[$jsId]);
			
				$success = true;
			}

			return $success;
		}

		public function requireStyle($cssId)
		{
			$success = false;

			if (array_key_exists($cssId, $this->m_cssFiles) == true)
			{
				$systemObj = System::getInstance();

				$page = $systemObj->getPageObject();

				$page->addCSSFile($this->m_cssFiles[$cssId]);

				$success = true;
			}

			return $success;
		}
		
		/**
		 * @fn initialize
		 *
		 * @brief initializes the script manager
		 *        and loads the default script.xml file
		 */	 
		private function initialize()
		{	
			$thisDir = dirname(__FILE__);
					
			$this->loadScriptFile($thisDir . '/scripts.xml');
		}
		
		public function loadScriptFile($fileName)
		{
			include_once('System.php');
			
			$systemObject = System::getInstance();
			
			$baseDir = $systemObject->getBaseSystemDir();

			include_once($baseDir . 'page/XmlPage.php');
			
			$xmlPage = XmlPage::withDocument($fileName);
			
			$scriptsElement = $xmlPage->getNode(SCRIPT_ROOT_ELEMENT);
			
			$rootUrl = $systemObject->getSiteRootURL();
			
			$fail = false;
			
			if ($scriptsElement != null)
			{
//				error_log('Found scripts element');
				// load all of the paths
				$paths = $scriptsElement->getElement(PATHS_ELEMENT);
				if ($paths != null)
				{
					$jsPaths = $paths->getElement(JS_ELEMENT);
					if ($jsPaths != null)
					{
						foreach ($jsPaths->getElements(PATH_ELEMENT) as $jsPath)
						{
							$this->m_jsPaths[$jsPath->getAttribute(ID_ATTR)] = $rootUrl . $jsPath->getAttribute(PATH_ATTR) . '/';
//							error_log('JS Path: ' . $jsPath->getAttribute(ID_ATTR));
						}
					}

					$cssPaths = $paths->getElement(CSS_ELEMENT);
					if ($cssPaths != null)
					{
						foreach ($cssPaths->getElements(PATH_ELEMENT) as $cssPath)
						{
							$this->m_cssPaths[$cssPath->getAttribute(ID_ATTR)] = $rootUrl . $cssPath->getAttribute(PATH_ATTR) . '/';
//							error_log('CSS Path: ' . $cssPath->getAttribute(ID_ATTR));
						}
					}
					
				}
				else
				{
					$fail = true;
				}

				// load all of the files
				$files = $scriptsElement->getElement(FILES_ELEMENT);
				if ($files != null)
				{
					$jsFiles = $files->getElement(JS_ELEMENT);
					if ($jsFiles != null)
					{
						foreach ($jsFiles->getElements(FILE_ELEMENT) as $jsFile)
						{
//							error_log('JS File: ' . $jsFile->getAttribute(ID_ATTR));
							$pathId = $jsFile->getAttribute(PATH_ID_ATTR);
							if (array_key_exists($pathId, $this->m_jsPaths) == true)
							{
								$jsKey = $jsFile->getAttribute(ID_ATTR);
								$this->m_jsFiles[$jsKey] = $this->m_jsPaths[$pathId] . $jsFile->getAttribute(CODEFILE_ATTR);

								// load dependencies
								$requires = $jsFile->getElements(REQUIRES_ELEMENT);
								if ($requires != null)
								{
									foreach ($requires as $requirement)
									{
										$type = $requirement->getAttribute(SM_TYPE_ATTR);
										if ($type == JS_ELEMENT)
										{
											if (array_key_exists($jsKey, $this->m_jsDependencies) == false)
											{
												$this->m_jsDependencies[$jsKey] = array();
											}
											$this->m_jsDependencies[$jsKey][] = $requirement->getAttribute(ID_ATTR);
										}
										else if ($type == CSS_ELEMENT)
										{
											if (array_key_exists($jsKey, $this->m_cssDependencies) == false)
											{
												$this->m_cssDependencies[$jsKey] = array();
											}
											$this->m_cssDependencies[$jsKey][] = $requirement->getAttribute(ID_ATTR);											
										}
									}
								}
							}
							else
							{
								error_log('Cannot find path: ' . $pathId);
							}
						}
					}
					
					$cssFiles = $files->getElement(CSS_ELEMENT);
					if ($cssFiles != null)
					{
						foreach ($cssFiles->getElements(FILE_ELEMENT) as $cssFile)
						{
//							error_log('CSS File: ' . $cssFile->getAttribute(ID_ATTR));
							$pathId = $cssFile->getAttribute(PATH_ID_ATTR);
							if (array_key_exists($pathId, $this->m_cssPaths) == true)
							{
								$this->m_cssFiles[$cssFile->getAttribute(ID_ATTR)] = $this->m_cssPaths[$pathId] . $cssFile->getAttribute(CODEFILE_ATTR);
							}
							else
							{
								error_log('Cannot find path: ' . $pathId);
							}
						}
					}
				}
				else
				{
					$fail = true;
				}
			}
			else
			{
				$fail = true;
			}
			
			if ($fail == true)
			{
				error_log('Invalid script file: ' . $fileName);				
			}
		}
	}
}
