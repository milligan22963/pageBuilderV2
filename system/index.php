<?php
/**
 * @module index
 *
 * @brief main entry point to the system
 */


// load our system
include_once('Toolbox.php');

include_once('ExtensionManager.php');
include_once('../page/HtmlPage.php');

$pageObject = new afm\HtmlPage();

$pageObject->setTitle("Toolbox");

$addedDiv = $pageObject->addDiv("mydiv");
$addedDiv->addAttribute("class", "sample");

$index = 1;
$files = afm\getFileList("../configuration/data", "*", "xml", true);
foreach ($files as $file)
{
	$pageObject->addLabel($file . $index, $file, $addedDiv);
	$index++;
}
$extensionManager = afm\ExtensionManager::getInstance();

$pageRendering = $pageObject->render();

echo $pageRendering;

?>
