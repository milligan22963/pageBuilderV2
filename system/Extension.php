<?php
/**
 * @module Extension
 *
 * @brief extension implementation for all extensions to extend
 */
namespace afm
{
 	include_once('IExtension.php');
  	
	class Extension implements IExtension
	{
		private $m_extensionPath = null;
		private $m_extensionSettings;
		private $m_type;
		private $m_name;
		private $m_description;

		public function __construct()
		{
			
		}

		/**
		 * @copydoc IExtension::initialize
		 */
		public function initialize($relativeExtensionPath, $extensionSettings)
		{
			$this->m_extensionSettings = $extensionSettings;
			$this->m_extensionPath = $relativeExtensionPath;
		}

		/**
		 * @copydoc IExtension::setName
		 */
		public function setName($name)
		{
			$this->m_name = $name;
		}

		public function getName()
		{
			return $this->m_name;
		}

		/**
		 * @copydoc IExtension::setDescription
		 */
		public function setDescription($description)
		{
			$this->m_description = $description;
		}

		/**
		 * @fn getType
		 *
		 * @copydoc IExtension::getType
		 */
		public function getType()
		{
			return $this->m_type;
		}
		
		public function preProcess()
		{

		}

		public function postProcess()
		{

		}
		
		public function &processRequest($option, $paramArray)
		{
			$resultingPage = new JSONPage();

			$resultingPage->addObject(JSON_SUCCESS, JSON_FALSE);
	
			return $resultingPage;
		}

		/**
		 *  @copydoc IExtension::preview
		 */
		public function preview(& $parentElement)
		{
			$elementId = str_replace(' ', '_', $this->m_name) . '_preview';

			$label = LabelElement::withParent($parentElement, $elementId, $this->m_name);

			$label->addClass('extension_preview');
			$label->addAttribute('extpath', basename($this->m_extensionPath));

			return $label;
		}

		/**
		 * @fn populate
		 *
		 * @copydoc IExtension::populate
		 */
		public function populate(& $parentElement)
		{
			return null;
		}

		/**
		 * @fn activate
		 *
		 * @copydoc IExtension::activate
		 */
		public function activate()
		{
			$this->installTables();
		}
		
		/**
		 * @fn deactivate
		 *
		 * @copydoc IExtension::deactivate
		 */
		public function deactivate()
		{
			$this->removeTables();
		}
		
		/**
		 * @fn reset
		 *
		 * @copydoc IExtension::reset
		 */
		public function reset()
		{
			$this->resetTables();			 
		}
		 
		// internal functions for derived classes
		protected function requireStyleSheet($styleSheet)
		{
			$systemObject = System::getInstance();

			$currentPage = & $systemObject->getPageObject();

			$currentPage->addCSSFile($this->m_extensionPath . $styleSheet);
		}
		
		protected function requireScript($jsFile)
		{
			$systemObject = System::getInstance();

			// try loading via script manager
			// if not then add in local path and include directly
			$scriptManager = & $systemObject->getScriptManager();

			if ($scriptManager->requireScript($jsFile) == false)
			{
				$pageObject = & $systemObject->getPageObject();

				$pageObject->addJSFile($this->m_extensionPath . $jsFile);
			}
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
		
		protected function getExtensionPath()
		{
			return $this->m_extensionPath;
		}

		protected function setType($type)
		{
			$this->m_type = $type;
		}
	}	
}	
?>