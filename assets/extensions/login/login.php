<?php
/**
 * @module Login
 *
 * @brief the login extension to allow a simple login procedure
 */

$systemObj = afm\System::getInstance();

include_once($systemObj->getBaseSystemDir() . 'system/Login.php');

class LoginWidget extends afm\Login 
{
	public function __construct()
	{
		parent::__construct();
		
		$this->setUserNameField('userName');
		$this->setPasswordField('userPassword');
		$this->setLogoutField('logoutUser');
	}
	
	/**
	 * @copydoc IExtension::populate
	 */
	public function populate(& $parentElement)
	{
		$childElement = null;

		$systemObj = afm\System::getInstance();

		include_once($systemObj->getBaseSystemDir() . 'system/UserSession.php');

		$pageObject = & $systemObj->getPageObject();
		
		$this->requireStyleSheet('css/login.css');
//		$this->requireScript('js/login.js');
		
		$userSession = &afm\UserSession::getInstance();
		
		if ($userSession->isLoggedIn() == false)
		{
			//
			// loginform
			//
			$childElement = $pageObject->addForm('login', $parentElement);
			$childElement->setEncodingType(afm\FormElement::$sm_formUrlEncoded);
			
			// add our action and utilize the script itself so we can work at both the root and admin level
			$childElement->setAction($systemObj->getScriptURL());
			
			// login the user if they have chosen that
			$userNameDiv = $pageObject->addDiv("username_div", $childElement);
			
			$userNameField = $childElement->addLabeledInput($this->getUserNameField(), "User Name: ", TEXT, $userNameDiv);
			$userNameField->setRequired(true);
			
			$passwordDiv = $pageObject->addDiv("password_div", $childElement);
			
			$passwordField = $childElement->addLabeledInput($this->getPasswordField(), "Password: ", PASSWORD_TYPE, $passwordDiv);
			$passwordField->setRequired(true);
	
			$submitButton = $childElement->addSubmitButton("login_submit");
			
			$submitButton->setValue('Login');
			
		}
		else
		{
			//
			// logout form
			//
			$childElement = $pageObject->addForm('logout', $parentElement);
			$childElement->setEncodingType(afm\FormElement::$sm_formUrlEncoded);
			
			// add our action and utilize the script itself so we can work at both the root and admin level
			$childElement->setAction($systemObj->getScriptURL());
			
			// login the user if they have chosen that
			$userNameDiv = $pageObject->addDiv("logout_div", $childElement);
			
			$label = $pageObject->addLabel('user_label', 'Welcome ', $userNameDiv);
			$userNameElement = afm\ItalicElement::withParent($userNameDiv, 'user_name_value', $userSession->getUserName());

			$logoutField = $childElement->addHiddenInput($this->getLogoutField());
			$logoutField->setValue($this->getLogoutField());
			$submitButton = $childElement->addSubmitButton("logout_submit", $userNameDiv);
			
			$submitButton->setValue('Logout');			
		}

		return $childElement;
	}
}
?>
