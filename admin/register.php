<?php
/**
 * @module register
 *
 * @brief registeration page
 *
 * @note - this will allow a new user to be registered however
 *         they will still need to be assigned a role i.e. default is 'user'
 */

namespace afm
{
	if (defined('SYSTEM_OBJ') == false)
	{
		die('None shall pass.');
	}
	
	include_once('AdminOperation.php');

	// form fields
	define('NEW_USER_NAME_PARAMETER', "new_user_name");
	define('NEW_USER_NAME_ERROR', "new_user_name_error");
	define('NEW_USER_PASSWORD_PARAMETER', "new_user_password");
	define('EMAIL_ADDR_ERROR', "email_addr_error");
	define('NEW_USER_EMAIL_PARAMETER', "new_user_email");
	define('NEW_USER_EMAIL2_PARAMETER', "new_user_email2");
	
	// json command response
	define('CHECK_USER_NAME', "checkUserName");
	define('REGISTER_NEW_USER', "registerNewUser");
	define('ACTIVATE_REGISTRATION', "activateUser");

	class RegisterUser extends AdminOperation 
	{
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @copydoc IAdminOperation::process
		 */
		public function process()
		{
			$resultingPage = null;
			
			if ($this->isJSONCall() == true)
			{
				$systemObj = System::getInstance();
				
				include_once($systemObj->getBaseSystemDir() . 'page/JSONPage.php');

				// create jsonPage and return based on sub option such as 'check_user_name'
				$resultingPage = new JSONPage();

				$params = $this->getParameters();
				if (isset($params[FUNCTION_PARAMETER]) == true)
				{
					switch ($params[FUNCTION_PARAMETER])
					{
						case CHECK_USER_NAME:
						{
							$success = false;
							
							if (array_key_exists(NEW_USER_NAME_PARAMETER, $params) == true)
							{
								$user = new User();

								if ($user->loadUserByUserName(cleanseData($params[NEW_USER_NAME_PARAMETER])) == false)
								{
									$success = true;
								}
							}
							$resultingPage->addObject(JSON_SUCCESS, $success == true ? JSON_TRUE : JSON_FALSE);
						}
						break;
						case REGISTER_NEW_USER:
						{
							error_log('Registering a new user');
							// need an allow similar flag to disallow anything with "admin" in it (case insensitive)
							// we may want a random token when creating them to verify it was us i.e. no race conditions between two users
							// both wanting the same username.
							$settingsManager = & $systemObj->getSettingsManager();
							
							// see if anyone can register
							$autoRegister = $settingsManager->getSetting(OPTION_ALLOW_AUTO_ACTIVATE);

							// check to ensure the user doesn't exist
							if (array_key_exists(NEW_USER_NAME_PARAMETER, $params) == true)
							{
								$userName = cleanseData($params[NEW_USER_NAME_PARAMETER]);
								$password = convertValue(PASSWORD, cleanseData($params[NEW_USER_PASSWORD_PARAMETER]), null);
								$email = cleanseEmail($params[NEW_USER_EMAIL_PARAMETER]);
								$email2 = cleanseEmail($params[NEW_USER_EMAIL2_PARAMETER]);

								if (($userName !== FALSE) && ($password !== FALSE) && ($email !== FALSE) && ($email2 !== FALSE))
								{
									if (strcasecmp($email, $email2) == 0)
									{
										$user = new User();
										$userType = UserType::withName('user');
										if ($user->loadUserByUserName($userName) == false)
										{
											$user->setName($userName);
											$user->setPassword($password);
											$user->setType($userType->getId());

											// now we need to generate an email if they are not auto registered
											if ($autoRegister == true)
											{
												$user->setActive(true);
											}
											else
											{
												include_once($systemObj->getBaseSystemDir() . 'system/Mail.php');

												$siteTitle = $settingsManager->getSetting(SITE_TITLE);

												$registrationMail = new Mail();
												$registrationMail->setSender($settingsManager->getSetting(SITE_ADMIN_EMAIL));
												$registrationMail->setSubject($siteTitle .  "- Registration Activation");

												if ($settingsManager->getSetting(OPTION_ALLOW_USER_ACTIVATE) == true)
												{
													$registerationKey = md5($settingsManager->getSetting(SITE_REGISTRATION_SALT . $userName));

													// send the email to register
													$registrationMail->addRecipient($email);
													$activationLink = $systemObj->getScriptURL(true) . '&func=' . ACTIVATE_REGISTRATION . '&key=' . $registerationKey;
													$activationLink .= '&userName=' . $userName;

													$registrationMail->setMessage($siteTitle . " - You have been registered please click the following link to activate your account: " . PHP_EOL . $activationLink);													
												}
												else
												{
													// if not active and no email then the admin will need to activate them manually
													// send the email to the admin to register them
													$registrationMail->addRecipient($settingsManager->getSetting(SITE_ADMIN_EMAIL));
													$registrationMail->setMessage($siteTitle . "- A new user: " . $userName . " has been registered.  Please activate as soon as possible." . PHP_EOL);
												}

												$registrationMail->sendMessage();
											}

											$user->save();

											// should now be there since we just saved it
											if ($user->loadUserByUserName($userName) == true)
											{
												$emailRecord = new Email();

												$emailRecord->setEmail($email);
												$emailRecord->setUserId($user->getId());

												$emailRecord->save();
											}
										}
									}
								}
							}
						}
						break;
						case ACTIVATE_REGISTRATION:
						{

						}
						break;
						default:
						{
							error_log('Unknown command: ' . $params[FUNCTION_PARAMETER]);
							$resultingPage = null;
						}
					}
				}
			}
			return $resultingPage;
		}

		/**
		 * @copydoc IAdminOperation::populate
		 */
		public function populate(& $parent)
		{
			$systemObj = & System::getInstance();
			
			$userSession = & $systemObj->getUserSession();
			$settingsManager = & $systemObj->getSettingsManager();
			
			// see if anyone can register
			$registrationOption = $settingsManager->getSetting(OPTION_ALLOW_NEW_USERS);
			
			// if no then see if the admin can (of course we must check that the admin user is logged in)
			if ($registrationOption == false)
			{
				if ($userSession->isLoggedIn() == true)
				{
					if ($userSession->getType() == USER_TYPE_ADMIN)
					{
						$registrationOption = $settingsManager->getSetting(OPTION_ADMIN_CREATE_USERS);
					}
				}
			}
			
			$childElement = null;
			if ($registrationOption == true)
			{				
				$childElement = new FormElement('registration_form');
				$childElement->setEncodingType(FormElement::$sm_formUrlEncoded);
				$childElement->setAction("javascript:registerUser('" . $systemObj->getScriptURL(true) . "', '" . NEW_USER_NAME_PARAMETER . "', '" . NEW_USER_PASSWORD_PARAMETER . "', '" . NEW_USER_EMAIL_PARAMETER . "', '" . NEW_USER_EMAIL2_PARAMETER . "')");

				// Contain it in a fieldset
				$fieldSet = $childElement->addFieldSet("registration_user");
				$fieldSet->addLegend("User Details");

				$userNameDiv = new DivElement("username_div");
				$fieldSet->addChildElement($userNameDiv);

				$userNameField = $fieldSet->addLabeledInput(NEW_USER_NAME_PARAMETER, "User Name: ", TEXT, $userNameDiv);
				$userNameField->setRequired(true);
				$userNameField->addAttribute('onchange', "javascript:checkUserName('". $systemObj->getScriptURL(false) . "', '" . NEW_USER_NAME_PARAMETER . "', '" . NEW_USER_NAME_ERROR . "')");

				$errorLabel = new LabelElement(NEW_USER_NAME_ERROR);
				$errorLabel->setData('The user name you have entered is already taken.');
				$userNameDiv->addChildElement($errorLabel);

				$passwordDiv = new DivElement("password_div");
				$fieldSet->addChildElement($passwordDiv);

				$passwordField = $fieldSet->addLabeledInput(NEW_USER_PASSWORD_PARAMETER, "Password: ", PASSWORD_TYPE, $passwordDiv);
				$passwordField->setRequired(true);

				$emailDiv = new DivElement("email_div");
				$fieldSet->addChildElement($emailDiv);

				$emailField = $fieldSet->addLabeledInput(NEW_USER_EMAIL_PARAMETER, "Email: ", EMAIL_TYPE, $emailDiv);
				$emailField->addAttribute(PLACEHOLDER_ATTR, 'Enter email address');
				$emailField->setRequired(true);
				$emailField->addAttribute('onchange', "javascript:compareEmailAddresses('" . NEW_USER_EMAIL_PARAMETER . "', '" . NEW_USER_EMAIL2_PARAMETER . "', '" . EMAIL_ADDR_ERROR . "')");

				$emailField = $fieldSet->addLabeledInput(NEW_USER_EMAIL2_PARAMETER, "Repeat Email: ", EMAIL_TYPE, $emailDiv);
				$emailField->addAttribute(PLACEHOLDER_ATTR, 'Repeat email address');
				$emailField->setRequired(true);
				$emailField->addAttribute('onchange', "javascript:compareEmailAddresses('" . NEW_USER_EMAIL_PARAMETER . "', '" . NEW_USER_EMAIL2_PARAMETER . "', '" . EMAIL_ADDR_ERROR . "')");

				$errorLabel = new LabelElement(EMAIL_ADDR_ERROR);
				$errorLabel->setData('The email addresses entered do not match.');
				$emailDiv->addChildElement($errorLabel);

				$fieldSet->addSubmitButton("new_user_submit");
			}
			else
			{
				$childElement = new LabelElement('registration_label');
				$childElement->setData("Registration is not enabled.  Please contact the administrator.");
			}
			$parent->addChildElement($childElement);
		}
	}
}
?>
