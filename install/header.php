<?php
/**
 * @module install
 *
 * @brief designed to install the system on the target
 *			platform
 */

function generateHeader(& $page)
{
	$header = $page->addHeader('primary_header');
	$header->addClass("header");
	
	$headerLabel = $page->addLabel('primary_header_label', "AFM pageBuilder", $header);	
	$headerLabel->addClass("header");
}
?>
