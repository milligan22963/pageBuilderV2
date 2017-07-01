<?php
/**
 * @module verifydb
 *
 * @brief verifies the database connection for the provided username and password
 */

// used by other scripts for forms etc
define('DB_USER_NAME', "db_userName");
define('DB_USER_PASSWORD', "db_password");
define('SITE_DB_NAME', "site_db_name");

$baseDir = dirname(dirname(__FILE__)) . "/";
$installDir = dirname(__FILE__) . "/";

include_once($baseDir . 'system/System.php');

$systemObject = afm\System::getInstance();

include_once($baseDir . 'system/Toolbox.php');
include_once($baseDir . 'configuration/Configuration.php');
include_once($baseDir . 'database/postgres/PostGres.php');

// if the file was not included, then process otherwise ignore
if (afm\isPageIncluded(__FILE__) == false)
{
	if ($_SERVER['REQUEST_METHOD'] == "POST")
	{
		if ((isset($_POST[DB_USER_NAME]) && (isset($_POST[DB_USER_PASSWORD]))) && (isset($_POST[SITE_DB_NAME])))
		{
			$userName = afm\cleanseData($_POST[DB_USER_NAME]);
			$password = afm\cleanseData($_POST[DB_USER_PASSWORD]);
			$dbName = afm\cleanseData($_POST[SITE_DB_NAME]);
			$dbPrefix = afm\cleanseData($_POST[DB_PREFIX]);
			
			$database = null;
			
			$goodParams = true;
			
			// validate with the given type
			switch ($_POST[DB_TYPE])
			{
				case POSTGRES:
				{
					$database = new afm\PGSQLDatabase();
				}
				break;
				case MYSQL:
				{
					echo 'Using mysql';
				}
				break;
				default:
				{
					$goodParams = false;
				}
				break;
			}
			
			if ($database != null)
			{
				$database->setPrefix($dbPrefix);
				
				$newDatabase = false;
				$validConfig = false;
				
				if ($database->initialize($dbName, $userName, $password) == true)
				{
					// database exists so we need to reset it?
					$validConfig = true;
				}
				else
				{
					if ($database->initialize('postgres', $userName, $password) == true)
					{
						// create $dbname for this user
						if ($database->createDatabase($dbName) == true)
						{
							$validConfig = true;
							$newDatabase = true;
						}
					}
					else
					{
						echo "Unable to connect to database";
					}
				}
				
				if ($validConfig == true)
				{
					$xmlFile = afm\Configuration::getSystemConfigFileName();
					
					$xmlConfig = new afm\Configuration();
					
					$xmlConfig->setFileName($xmlFile);
					
					$xmlConfig->set(DB_USER, $userName);
					$xmlConfig->set(DB_PASSWORD, $password);
					$xmlConfig->set(DB_TYPE, $_POST[DB_TYPE]);
					$xmlConfig->set(DB_NAME, $dbName);
					$xmlConfig->set(DB_PREFIX, $dbPrefix);
				
					$xmlConfig->saveFile();

//					if ($newDatabase == true)
//					{
						error_log('Creating tables');
						$tableFiles = afm\getFileList($baseDir . "configuration/data", "*", "xml", true);
						
						$deferred = array();
						
						// load the database tables
						foreach ($tableFiles as $file)
						{
							error_log('Creating table: ' . $file);
							$table = &$database->loadTable($file, true);
							if ($table == null)
							{
								$deferred[] = $file;
							}
						}
						// some tables have dependencies so defer them until we are ready
						foreach ($deferred as $file)
						{
							error_log('Creating deferred table: ' . $file);
							$table = &$database->loadTable($file, true);
						}
//					}
				}
				displayResults();
			}
		}	
		else
		{
			echo "Improper values: " . print_r($_POST, true);
		}
	}
	else
	{
		echo "Ignoring non-post methods";
	}
}

function displayResults()
{
	$baseDir = dirname(dirname(__FILE__)) . "/";
	$installDir = dirname(__FILE__) . "/";
	
	include_once($baseDir . 'page/UserPage.php');
	include_once($baseDir . 'system/System.php');
	include_once($installDir . 'header.php');
	include_once($installDir . 'footer.php');
	
	$systemObject = afm\System::getInstance();

	$systemObject->setPageDomain(PAGE_USER_DOMAIN);

	$pageObject = new afm\UserPage();
	
	$pageObject->setTitle("Installation");
	$pageObject->addCSSFile("css/install.css");
	$pageObject->addJSFile("js/install.js");
	
	// need a database type selection along w/ username and password
	// need to collect some other data, if db already exists, ensure we can wack it
	generateHeader($pageObject);
	
	$contentDiv = $pageObject->addDiv("content_div");
	
	$contentDiv->setData('Site is now ready for use.');
	
	// close the page off w/ a footer
	generateFooter($pageObject);

//	$content = $pageObject->render();
//	error_log('Content: ' . $content);
	echo $pageObject->render();
}
?>
