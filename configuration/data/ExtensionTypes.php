<?php
/**
 * @module ExtensionTypes
 *
 * @brief manages extension types
 */
 namespace afm
 {
 	define('MENU_TYPE', "menu");
 	define('LOGIN_TYPE', "login");
 	define('BLOG_TYPE', "blog");
 	define('CUSTOM_TYPE', "custom");

	include_once('Data.php');
		
	class ExtensionType extends Data
	{
		private $m_type;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->m_type = CUSTOM_TYPE;
			
			$systemObj = &System::getInstance();
			$dbInstance = &$systemObj->getDatabase();

			$this->setTable($dbInstance->getTable(EXTENSION_TYPE_TABLE));
		}
		
		static public function withType($type)
		{
			$extensionType = new ExtensionType();
			
			$extensionType->loadExtensionTypeByType($type);
			
			return $extensionType;
		}
		
		static function withId($id)
		{
			$extensionType = new ExtensionType();
			
			$extensionType->load($id);
			
			return $extensionType;
		}
		
		public function setType($type)
		{
			$this->m_type = trim($type);			
		}
		
		public function getType()
		{
			return $this->m_type;
		}
		
		// internal methods
		protected function loadExtensionTypeByType($type)
		{
			$success = false;
			
			$table = &$this->getTable();
			
			$resultSet = $table->loadRow("type='" . $type . "'");
			
			if ($resultSet != null)
			{
                if ($row = $resultSet->fetch(\PDO::FETCH_LAZY))
                {
	                $this->fromSQL($row);
	                
	                $success = true;
	            }
				$resultSet = null; // done w/ it
			}
			return $success;
		}

		protected function fromSQL($dbObject)
		{
			parent::fromSQL($dbObject);
	
			$this->setType($dbObject->type);
		}

		protected function toArray()
		{
			$arrayRepresentation = parent::toArray();

			$arrayRepresentation['type'] = $this->getType();
			
			return $arrayRepresentation;
		}
	}
}
?>