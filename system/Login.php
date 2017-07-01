<?php
/**
 * @module Login
 *
 * @brief login implementation for all login widgets to implement
 */
namespace afm
{
	include_once('ILogin.php');
	include_once('Extension.php');
	
	class Login extends Extension implements ILogin
	{
		private $m_userNameField;
		private $m_passwordField;
		private $m_logoutField;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->m_userNameField = null;
			$this->m_passwordField = null;
			$this->m_logoutField = null;
			
			$this->setType(LOGIN_TYPE);
		}
		
		/**
		 * @fn getUserNameField
		 *
		 * @copydoc ILogin::getUserNameField
		 */
		public function getUserNameField()
		{
			return $this->m_userNameField;
		}
		
		/**
		 * @fn getPasswordField
		 *
		 * @copydoc ILogin::getPasswordField
		 */
		public function getPasswordField()
		{
			return $this->m_passwordField;
		}
		
		/**
		 * @fn getLogoutField
		 *
		 * @copydoc ILogin::getLogoutField
		 */
		public function getLogoutField()
		{
			return $this->m_logoutField;
		}

		protected function setUserNameField($userNameField)
		{
			$this->m_userNameField = $userNameField;
		}
		
		protected function setPasswordField($userPasswordField)
		{
			$this->m_passwordField = $userPasswordField;
		}
		
		protected function setLogoutField($logoutField)
		{
			$this->m_logoutField = $logoutField;
		}
	}	
}	
?>