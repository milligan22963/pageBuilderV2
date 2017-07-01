<?php
/**
 * @module index
 *
 * @brief main entry point to the system
 */


// load our system
include_once('System.php');
include_once('../page/HtmlPage.php');

$pageObject = new afm\HtmlPage();

$pageObject->setTitle("Scripts");

$addedDiv = $pageObject->addDiv("mydiv");
$addedDiv->addAttribute("class", "sample");

$index = 1;

$systemObj = afm\System::getInstance();

$systemObj->setPageDomain(PAGE_DOMAIN_USER);

$pageObject->requireJS('TOOLS');
$pageObject->requireCSS('SITE');

$pageRendering = $pageObject->render();

//error_log('Page: ' . $pageRendering);

echo $pageRendering;

?>
