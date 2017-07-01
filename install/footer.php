<?php
/**
 * @module footer
 *
 * @brief designed to create a footer element
 */

function generateFooter(& $page)
{
	$footer = $page->addFooter('primary_footer');
	$footer->addClass("footer");
	
	$footerLabel = $page->addLabel('primary_footer_label', "AFM Software 2017", $footer);	
	$footerLabel->addClass("footer");
}
?>
