<?php
/**
 * @module install
 *
 * @brief designed to install the system on the target
 *			platform
 */
 
 // include relative - chicken and egg we cant get the system object until we include
 // its file and need the base dir to get the system object.
 // regular pages will be cleaner.  This code is intended to be temporary on a target site
$baseDir = dirname(dirname(__FILE__)) . "/";
$installDir = dirname(__FILE__) . "/";

include_once($baseDir . 'system/System.php');
$systemObject = afm\System::getInstance();

include_once($baseDir . 'page/HtmlPage.php');
include_once($baseDir . 'database/IDatabase.php');
include_once($installDir . 'header.php');
include_once($installDir . 'footer.php');
include_once($installDir . 'verifydb.php');

$pageObject = new afm\HtmlPage();

$pageObject->setTitle("Installation");
$pageObject->addCSSFile("css/install.css");
$pageObject->addJSFile("js/install.js");

// need a database type selection along w/ username and password
// need to collect some other data, if db already exists, ensure we can wack it
generateHeader($pageObject);

// Create a form to allow the user to select a database type, right now postgres and mysql
$databaseForm = $pageObject->addForm("database");
$databaseForm->setEncodingType(afm\FormElement::$sm_formUrlEncoded);

// Contain it in a fieldset
$fieldSet = $databaseForm->addFieldSet("db_field_set");
$fieldSet->addLegend("Database Configuration");

// Create the select
$labeledInput = $fieldSet->addLabeledInput(DB_TYPE, "Select Type:", SELECT);

// add our options
$labeledInput->addOption("PostGRES", POSTGRES, true);
$labeledInput->addOption("MySQL", MYSQL, false);

$dbNameDiv = $pageObject->addDiv("dbname_div", $fieldSet);

$dbNameField = $fieldSet->addLabeledInput(SITE_DB_NAME, "Database Name: ", TEXT, $dbNameDiv);
$dbNameField->setRequired(true);

$dbPrefixDiv = $pageObject->addDiv("dbprefix_div", $fieldSet);
$dbPrefix = $fieldSet->addLabeledInput(DB_PREFIX, 'Database Prefix: ', TEXT, $dbPrefixDiv);
$dbPrefix->setRequired(true);

// We also need a user name and password
$userNameDiv = $pageObject->addDiv("username_div", $fieldSet);

$userNameField = $fieldSet->addLabeledInput(DB_USER_NAME, "User Name: ", TEXT, $userNameDiv);
$userNameField->setRequired(true);

$passwordDiv = $pageObject->addDiv("password_div", $fieldSet);

$passwordField = $fieldSet->addLabeledInput(DB_USER_PASSWORD, "Password: ", PASSWORD_TYPE, $passwordDiv);
$passwordField->setRequired(true);

$fieldSet->addSubmitButton("db_submit");

// add our action
$databaseForm->setAction($systemObject->getScriptURL(true) . "verifydb.php");

// close the page off w/ a footer
generateFooter($pageObject);

echo $pageObject->render();

?>
