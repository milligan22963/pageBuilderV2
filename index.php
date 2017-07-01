<?php
/**
 * @module index
 *
 * @brief main entry point to the system
 *
 * It is assumed this is at the top of the file system
 */

// load our system
include_once('system/System.php');
include_once('system/UserSession.php');
include_once('page/UserPage.php');

$systemObj = & afm\System::getInstance();

$systemObj->setPageDomain(PAGE_USER_DOMAIN);

// process/update user login details
afm\processUserLogin();

$userOption = null;
$extensionOption = null;

$paramArray = $systemObj->getParameterArray(USER_OPTION);
if ($paramArray != null)
{
	$userOption = $paramArray[USER_OPTION];
    $extensionOption = $paramArray[EXTENSION_OPTION];
}

$pageObject = null;

// if there is a user option then process it
if ($userOption != null)
{
    $extensionManager = & $systemObj->getExtensionManager();

    // process user option
    $pageObject = &$extensionManager->processRequest($extensionOption, $userOption, $paramArray);
}

if ($pageObject == null)
{
    $pageObject = new afm\UserPage();

    // do the defaults before processing the theme specific
    $settingsManager = & $systemObj->getSettingsManager();
    $pageObject->setTitle($settingsManager->getSetting(SITE_TITLE));
    $pageObject->requireCSS('SITE');

    $systemObj->loadSite();

    $extensionManager = & $systemObj->getExtensionManager();
    $menuWidget = & $extensionManager->getExtensionByType(MENU_TYPE);
    $command = $systemObj->getScriptURL();
}

//error_log('Render: ' . $pageObject->render());
echo $pageObject->render();

?>
