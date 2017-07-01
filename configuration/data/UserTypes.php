<?php
/**
 * @module UserTypes
 *
 * @brief creates the usertype table for the system
 */
 namespace afm
 {
	// see user_types.xml for details
	define('USER_TYPE_OTHER', "other");
	define('USER_TYPE_ADMIN', "admin");
	define('USER_TYPE_USER', "user");
	define('USER_TYPE_SUPPORT', "support");
	define('USER_TYPE_DEBUG', "debug");

	include_once('Data.php');
		
	class UserType extends Data
	{
		private $m_name;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->m_name = USER_TYPE_OTHER;
			
			$systemObj = &System::getInstance();
			$dbInstance = &$systemObj->getDatabase();

			$this->setTable($dbInstance->getTable(USER_TYPE_TABLE));
		}
		
		static public function withName($name)
		{
			$userType = new UserType();
			
			$userType->loadUserTypeByName($name);
			
			return $userType;
		}
		
		static function withId($id)
		{
			$userType = new UserType();
			
			$userType->load($id);
			
			return $userType;
		}
		
		public function setName($name)
		{
			$this->m_name = trim($name);			
		}
		
		public function getName()
		{
			return $this->m_name;
		}
		
		// internal methods
		protected function loadUserTypeByName($name)
		{
			$success = false;
			
			$table = &$this->getTable();
			
			$resultSet = $table->loadRow("name='" . $name . "'");
			
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
	
			$this->setName($dbObject->name);
		}

		protected function toArray()
		{
			$arrayRepresentation = parent::toArray();

			$arrayRepresentation['name'] = $this->getName();
			
			return $arrayRepresentation;
		}
	}
}
?>
