<?php
/**
 * @module user
 *
 * @brief creartes the user table for the system
 */
 namespace afm
 {
	include_once('Data.php');
	include_once('UserTypes.php');
		
	// start w/ 2 as Data uses 1
	define('USER_NAME_CHANGE', 2);
	define('USER_TYPE_CHANGE', 4);
	define('USER_PASSWORD_CHANGE', 8);
	define('USER_ACTIVITY_CHANGE', 16);
	
	class User extends Data
	{
		private $m_name;
		private $m_type;
		private $m_password;
		private $m_lastActivity;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->m_name = "none";
			$this->m_type = 0;
			$this->m_password = null;
			$this->m_lastActivity = date("Y-m-d H:i:s");
			
			$systemObj = &System::getInstance();
			$dbInstance = &$systemObj->getDatabase();

			$this->setTable($dbInstance->getTable(USER_TABLE));
		}
		
		static public function withName($name)
		{
			$user = new User();
			
			$user->loadUserByUserName($name);
			
			return $user;
		}
		
		public function setName($name)
		{
			$this->m_name = $name;
			
			$this->setChange(USER_NAME_CHANGE);
		}
		
		public function getName()
		{
			return $this->m_name;
		}
		
		public function setPassword($password)
		{
			$this->m_password = $password;

			$this->setChange(USER_PASSWORD_CHANGE);
		}
		
		public function getPassword()
		{
			return $this->m_password;
		}
		
		public function setType($type)
		{
			$userType = UserType::withId($type);
			
//			error_log('User Type: ' . $userType->getName());
			$this->m_type = $userType->getName();

			$this->setChange(USER_TYPE_CHANGE);
		}
		
		public function getType()
		{
			return $this->m_type;
		}

		public function getTypeId()
		{
			// convert the type to the id representation
			$userType = UserType::withName($this->m_type);

			return $userType->getId();
		}
				
		public function setLastActivity($lastActivity)
		{
			$this->m_lastActivity = $lastActivity;

			$this->setChange(USER_ACTIVITY_CHANGE);
		}
		
		public function getLastActivity()
		{
			return $this->m_lastActivity;
		}

		/**
		 * @brief load a user based on their name (case insensitive)
		 */
		public function loadUserByUserName($userName)
		{
			$success = false;
			
			$table = &$this->getTable();
			
			$resultSet = $table->loadRow("upper(name) like upper('" . $userName . "')");
			
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
		
		public function generateCookieIdentifier()
		{
			$configBaseDir = dirname(dirname(__FILE__)) . '/';
			
			include_once($configBaseDir . 'SettingManager.php');

			$settingsManager = SettingManager::getInstance();

			return \hash('sha1', $this->getName() . $settingsManager->getSetting(SITE_USER_NAME_SALT), false);
		}
		
		public function validatePassword($userName, $textPassword)
		{
			$valid = false;

			//error_log("Validating password for: " . $userName);
			//error_log("Validating password: " . $textPassword);

			// verify that the user supplied password matches the stored hashed password
			$valid = \password_verify($textPassword, trim($this->getPassword()));
			
			if ($valid != true)
			{
				error_log('Bad match');
			}
			return $valid;
		}

		// internal methods
		protected function fromSQL($dbObject)
		{
			parent::fromSQL($dbObject);
	
			$this->setName($dbObject->name);
			$this->setType($dbObject->type);
			$this->setPassword($dbObject->password);
			$this->setLastActivity($dbObject->last_activity);			
			
			$this->clearChanges();
		}

		protected function toArray()
		{
			$arrayRepresentation = parent::toArray();
						
			if ($this->isChanged(USER_NAME_CHANGE) == true)
			{
				$arrayRepresentation['name'] = $this->getName();
			}
			
			if ($this->isChanged(USER_PASSWORD_CHANGE) == true)
			{
				$arrayRepresentation['password'] = $this->getPassword();
			}
			
			if ($this->isChanged(USER_TYPE_CHANGE) == true)
			{
				$arrayRepresentation['type'] = $this->getTypeId();
			}
			
			if ($this->isChanged(USER_ACTIVITY_CHANGE) == true)
			{
				$arrayRepresentation['last_activity'] = $this->getLastActivity();
			}
			
			return $arrayRepresentation;
		}
	}
}
?>
