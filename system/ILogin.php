<?php
/**
 * @module ILogin
 *
 * @brief login interface for all login widgets to implement
 */
namespace afm
{
	/**
	 *  @note - the login interface allows the system to have any type of login
	 *			provided it supports a user name and password.  The idea is that these
	 *			fields would be passed into whatever javascript and returned for validation
	 */
	interface ILogin
	{
		/**
		 * @fn getUserNameField
		 *
		 * @brief returns the field used to capture the user name
		 *
		 * @return the field name for the user name
		 */
		public function getUserNameField();
		
		/**
		 * @fn getPasswordField
		 *
		 * @brief returns the field used to capture the user password
		 *
		 * @return the field name for the user password
		 */
		public function getPasswordField();

		/**
		 * @fn getLogoutField
		 *
		 * @brief returns the field used to indicate that logging out
		 *
		 * @return the field name for the logout field
		 */
		public function getLogoutField();
	}	
}	
?>