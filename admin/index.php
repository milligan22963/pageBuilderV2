<?php
/**
 * @module index
 *
 * @brief main entry point to the system
 */

$adminBase = dirname(__FILE__);
$siteBase = dirname($adminBase) . '/';

// load our system
include_once($siteBase . 'system/System.php');
include_once($siteBase . 'system/UserSession.php');

//error_log('Variables: ' . print_r($_SERVER, true));

$systemObj = & afm\System::getInstance();

$systemObj->setPageDomain(PAGE_ADMIN_DOMAIN);

// process/update user login details
afm\processUserLogin();

include_once($systemObj->getBaseSystemDir() . 'page/AdminPage.php');
include_once('admin.php');

// load all of the administrator options that are available
$adminOptions = afm\loadAdminOptions();

$adminPage = null;
$processingUnit = null;

$userSession = & $systemObj->getUserSession();

// determine the disposition of the user request
$userOption = null;
$paramArray = null;
$jsFile = null;

//error_log('Starting access');

// we allow any access, at this level as users may be activating registration
// creating their account,
// etc
$paramArray = $systemObj->getParameterArray(ADMIN_OPTION);
if ($paramArray != null)
{
	$userOption = $paramArray[ADMIN_OPTION];
//	error_log('Param array: ' . print_r($paramArray, true));
}

// if there is a user option then process it
if ($userOption != null)
{	
	if (array_key_exists($userOption, $adminOptions))
	{
		$includeFile = $adminOptions[$userOption]->getCode();
		include_once("${includeFile}");

//		error_log('File: '  . $includeFile);

		$targetClass = $adminOptions[$userOption]->getClass();

//		error_log('Target Class: ' . $targetClass);
		
		$jsFile = $adminOptions[$userOption]->getJSFile();
		
		$processingUnit = new $targetClass;
		
		// we need to pull in the appropriate admin operation
		$processingUnit->initialize($paramArray);
		
		// admin pages are controlled a little more tightly so
		// we base things only on known registered commands
		// using the admin.xml file.
		$adminPage = $processingUnit->process();		
	}
}

if ($adminPage == null)
{
	$adminPage = new afm\AdminPage();
	
	afm\showAdminPage($adminOptions);
	
	// processing unit is only instantiated if the user is an admin and is logged in
	if ($processingUnit != null)
	{
		$adminPage->requireJS('TOOLS');
		if ($jsFile != null)
		{
			$adminPage->addJSFile($jsFile, SITE_FILE);
		}
		
		$themeManager = & $systemObj->getThemeManager();
		
		$processingUnit->populate($themeManager->getMainContentArea());
	}
}

echo $adminPage->render();

?>
