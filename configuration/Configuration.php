<?php
/**
 * @module Configuration
 *
 * @brief general system information
 */
namespace afm
{
	$systemObject = & System::getInstance();

	$baseDir = $systemObject->getBaseSystemDir();

	include_once($baseDir . 'system/Toolbox.php');
	include_once('XmlDefines.php');
	include_once('IConfiguration.php');
	include_once($baseDir . 'page/XmlPage.php');
	
	class Configuration implements IConfiguration
	{
		private $m_configPage = null;
		private $m_configFile = null;

		public function __construct()
		{
			$this->m_configPage = new XmlPage(CONFIGURATION_SECTION);
		}
		
		static public function getSystemConfigFileName()
		{
			$thisDir = dirname(__FILE__) . '/';
			
			return $thisDir . '.htignore';
		}

		public function set($name, $value)
		{
			$newItem = $this->m_configPage->addNode($name);
			
			$newItem->setData($value);
		}
		
		public function get($name, $default=null)
		{
			$value = $default;
			
			$node = $this->m_configPage->getNode($name);
			
			if ($node != null)
			{
				$value = $node->getData();
			}

			return $value;			
		}
		
		public function setFileName($fileName)
		{
			$this->m_configFile = $fileName;
		}
		
		public function getFileName()
		{
			return $this->m_configFile;
		}
		
		public function loadFile($fileName = null)
		{
			if ($fileName != null)
			{
				$this->m_configFile = $fileName;
			}

			$this->m_configPage->load($this->m_configFile);			
		}
		
		public function saveFile($fileName = null)
		{
			if ($fileName != null)
			{
				$this->m_configFile = $fileName;
			}

			$file = fopen($this->m_configFile, 'w');
			
			if ($file != null)
			{
				fwrite($file, $this->m_configPage->render());
				fclose($file);
			}
		}
		
		public function render()
		{
			return $this->m_configPage->render();
		}
	}	
}	
?>