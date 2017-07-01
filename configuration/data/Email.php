<?php
/**
 * @module Email
 *
 * @brief supports the email table for the users
 */
 namespace afm
 {
	include_once('Data.php');

	// start w/ 2 as Data uses 1
	define('EMAIL_CHANGE', 2);
	define('EMAIL_ID_CHANGE', 4);
	
	class Email extends Data
	{
		private $m_userId;
		private $m_email;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->m_userId = 0;
			$this->m_email = null;
			
			$systemObj = &System::getInstance();
			$dbInstance = &$systemObj->getDatabase();

			$this->setTable($dbInstance->getTable(EMAIL_TABLE));
		}
		
		static public function withEmail($email)
		{
			$email = new Email();
			
			$email->loadByEmail($email);
			
			return $email;
		}
		
		public function setEmail($email)
		{
			$this->m_email = $email;
			
			$this->setChange(EMAIL_CHANGE);
		}
		
		public function getEmail()
		{
			return $this->m_email;
		}
		
		public function setUserId($userId)
		{
			$this->m_userId = $userId;

			$this->setChange(EMAIL_ID_CHANGE);
		}
		
		public function getUserId()
		{
			return $this->m_userId;
		}

        public function getUserName()
        {
            include_once('User.php');

            $user = new User();

            $user->load($this->m_userId);

            return $user->getName();
        }
	
		/**
		 * @brief load a email based on the email addr
		 */
		public function loadByEmail($email)
		{
			$success = false;
			
			$table = &$this->getTable();
			
			$resultSet = $table->loadRow("email='" . $email . "'");
			
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

		// internal methods
		protected function fromSQL($dbObject)
		{
			parent::fromSQL($dbObject);
	
			$this->setEmail($dbObject->email);
			$this->setUserId($dbObject->userId);
			
			$this->clearChanges();
		}

		protected function toArray()
		{
			$arrayRepresentation = parent::toArray();
						
			if ($this->isChanged(EMAIL_CHANGE) == true)
			{
				$arrayRepresentation['email'] = $this->getEmail();
			}
			
			if ($this->isChanged(EMAIL_ID_CHANGE) == true)
			{
				$arrayRepresentation['userId'] = $this->getUserId();
			}
			
			return $arrayRepresentation;
		}
	}
}
?>