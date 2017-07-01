<?php 
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
?>
