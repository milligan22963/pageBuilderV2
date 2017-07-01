<?php
/**
 * @module UserSession
 *
 * @brief tools for the user sessions
 */
namespace afm
{	
	$systemObject = & System::getInstance();
	
	$baseDir = $systemObject->getBaseSystemDir();
	
	include_once($baseDir . 'configuration/data/User.php');
	include_once($baseDir . 'configuration/data/Email.php');
	include_once($baseDir . 'configuration/data/UserTypes.php');
	include_once('Log.php');
		
	class UserSession
	{
		private $m_currentUser;
		private $m_loggedIn;
		private static $m_singleInstance = null;
		
		private function __construct()
		{
			$this->m_currentUser = new User();
			$this->m_loggedIn = false;
			session_start();
		}
		
		public static function &getInstance()
		{
			if (self::$m_singleInstance == null)
			{
				self::$m_singleInstance = new UserSession();
			}
			
			return self::$m_singleInstance;
		}
		
		function getUserId()
		{
			$returnVal = 0; // db starts with 1
	
			if ($this->m_loggedIn == true)
			{
				$returnVal = $this->m_currentUser->getId();
			}
			return $returnVal;
		}
		
		function getUserName()
		{
			$returnString = null;
	
			if ($this->m_loggedIn == true)
			{
				$returnString = $this->m_currentUser->getName();
			}
			
			return $returnString;
		}
		
		function getUserType()
		{
			$returnString = USER_TYPE_OTHER;
			
			if ($this->m_loggedIn == true)
			{
				$returnString = $this->m_currentUser->getType();
			}
	
			return $returnString;
		}
		
		function loginUser($userName, $userPassword)
		{
			$this->m_loggedIn = false;
			if ($this->m_currentUser->loadUserByUserName($userName) == true)
			{
				if ($this->m_currentUser->getActive() == true)
				{
					if ($this->m_currentUser->validatePassword($userName, $userPassword) == true)
					{
						$systemObject = & System::getInstance();
						
						$baseDir = $systemObject->getBaseSystemDir();
						
						include_once($baseDir . '/configuration/SettingManager.php');
						
						$sessionLength = SettingManager::getInstance()->getSetting(SITE_SESSION_LENGTH, 900);
						
						$_SESSION['userName'] = $userName;
						$_SESSION['userPassword'] = $userPassword; // DWM do we want/need this?  We could assume if the userName is correct that its good.
						$_SESSION['loginTime'] = time();
						setcookie('userName', $this->m_currentUser->generateCookieIdentifier(), $_SESSION['loginTime'] + $sessionLength, "/");
						//					session_write_close();
							
						$this->m_loggedIn = true;
					}
				}
			}
			return $this->m_loggedIn;
		}
		
		function isActive()
		{
			return $this->m_currentUser->getUserActive();
		}
		
		function isLoggedIn()
		{
			return $this->m_loggedIn;		
		}
		
		function logoutUser()
		{
			$logSystem = LogToFile::getInstance();
			if (array_key_exists('userName', $_SESSION))
			{
				$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging out user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
			}
			else
			{
				$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging out user anonymous (' . __LINE__ . ')' . PHP_EOL);
			}
			$this->m_loggedIn = false;
			$this->m_currentUser = new User();
			unset($_SESSION['userName']);
			unset($_SESSION['userPassword']);
			unset($_SESSION['loginTime']);		
			unset($_COOKIE['userName']);
	//		session_write_close();
		}
		
		function updateActivity($currentTime)
		{
			if ($this->m_loggedIn == true)
			{
				$this->m_currentUser->setLastActivity($currentTime);
				$this->m_currentUser->save();
			}
		}
	}
	
	function processUserLogin()
	{
		$success = false;
		
		/*
		 * Get a user session instance which will start the session
		 */
		$loginInstance = &UserSession::getInstance();
	
		$logSystem = LogToFile::getInstance();
		
		if (isset($_POST['logoutUser']))
		{
			$loginInstance->logoutUser();
			$success = true;
		}
		else
		{
			/*
			 * Are they current logging in?  if so get the variables posted
			 */
			if (isset($_POST['userName']) && isset($_POST['userPassword']))
			{
				error_log('Logging in: ' . $_POST['userName']);
				$userName = cleanseData($_POST['userName']);
				$userPassword = cleanseData($_POST['userPassword']);

				$loginInstance->loginUser($userName, $userPassword);

				$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $userName . '(' . __LINE__ . ')' . PHP_EOL);

				//		$loginInstance->loginUser($_GET['userName'], $_GET['userPassword']);
				$loginInstance->updateActivity(time());
				$success = true;
			}
			elseif (isset($_SESSION['userName']) && isset($_SESSION['userPassword']))
			{
				$userName = cleanseData($_SESSION['userName']);
				$userPassword = cleanseData($_SESSION['userPassword']);

				$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $userName . '(' . __LINE__ . ')' . PHP_EOL);
				/*
				 * See if the session has expired
				 */
				if (isset($_SESSION['loginTime']))
				{
					include_once(dirname(dirname(__FILE__)) . '/configuration/SettingManager.php');
					
					$sessionLength = SettingManager::getInstance()->getSetting(SITE_SESSION_LENGTH, 900);
					$currentTime = time();
					$lastLogin = $_SESSION['loginTime'];
					
					error_log('Current Time: ' . $currentTime);
					error_log('Last Time: ' . $lastLogin);
					
					if ($currentTime - $lastLogin <= $sessionLength)
					{
						/*
						 * They are still good - update there last login to the current time
						 * 
						 * DWM if they steal the userName hash and sessionId then they can impersonate me
						 * what to do to prevent/deter this?  Hash userName with date/time and update on each access? i.e. rotating key/pair
						 */
						
						if (isset($_COOKIE['userName']))
						{
							$user = new User();
							$user->loadUserByUserName($userName);
							if ($_COOKIE['userName'] == $user->generateCookieIdentifier())
							{
	//							error_log('Cookie match');
								$loginInstance->loginUser($userName, $userPassword);
								$loginInstance->updateActivity(time());
								$success = true;
								$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $_SESSION['userName'] . '(' . __LINE__ . ')' . PHP_EOL);
							}
							else
							{
								error_log('No cookie match for you.');
							}
						}
						else
						{
							//if cookies are not set then revalidate with session data
							$loginInstance->loginUser($userName, $userPassword);
							$loginInstance->updateActivity($currentTime);
							$success = true;
							$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $userName . '(' . __LINE__ . ')' . PHP_EOL);
						}
					}
					else
					{
						$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $userName . '(' . __LINE__ . ')' . PHP_EOL);
					}
				}
				else
				{
					$logSystem->logInformation(LOG_SYSTEM_TRACE, 'Logging in user with userName:' . $userName . '(' . __LINE__ . ')' . PHP_EOL);
				}
			}
		}
		
		if ($success == false)
		{
			$loginInstance->logoutUser();
		}
	}
}
?>