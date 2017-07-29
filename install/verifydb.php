<?php
/**
 * @module verifydb
 *
 * @brief verifies the database connection for the provided username and password
 */

if (!defined('INSTALL_IN_PROGRESS'))
{
	define('INSTALL_IN_PROGRESS', "install_in_progress");
}
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
include_once($baseDir . 'database/mysql/MySQL.php');

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
			
			error_log('Param array: ' . print_r($_POST, true));
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
					$database = new afm\MySQLDatabase();
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
				
				$validConfig = false;

				$systemTableName = $database->getSystemTableName();
				
				if ($database->initialize($dbName, $userName, $password) == true)
				{
					// database exists so we need to reset it?
					if ($database->createDatabase($dbName, true) == true)
					{
						$validConfig = true;
					}
				}
				else
				{
					if ($database->initialize($systemTableName, $userName, $password) == true)
					{
						// create $dbname for this user
						if ($database->createDatabase($dbName, false) == true)
						{
							// now connect to it since it now exists
							$validConfig = $database->initialize($dbName, $userName, $password);
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

					error_log('Creating tables');

					// need to clear out . files
					$tableFiles = afm\getFileList($baseDir . "configuration/data", "*", "xml", true);
					
					$deferred = array();
					
					// load the database tables
					foreach ($tableFiles as $file)
					{
						error_log('Creating table: ' . $file);
						$table = &$database->loadTable($file, true);
						if ($table == null)
						{
							error_log('Adding: ' . $file . ' to the deferred group');
							$deferred[] = $file;
						}
					}
					// some tables have dependencies so defer them until we are ready
					foreach ($deferred as $file)
					{
						error_log('Creating deferred table: ' . $file);
						$table = &$database->loadTable($file, true);
					}
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
	$pageObject->addCSSFile("css/install.css", SITE_FILE);
	$pageObject->addJSFile("js/install.js", SITE_FILE);
	
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
