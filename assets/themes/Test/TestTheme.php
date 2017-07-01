<?php
/**
 * @module DefaultTheme
 *
 * @brief the main entry point for this theme
 */

$systemObj = & afm\System::getInstance();

$themeRootPath = $systemObj->getBaseSystemDir() . '/system/';

include_once($themeRootPath . 'Theme.php');

/**
	some ideas
	  register methods to be called as needed i.e.
	  admin page, register show layout and other type functions
	  main page, register which cells are available and what to call?  Or povide a way to call extensions based on cell?
	  
	  the settings stanza can have information regarding database tables etc.
	  
	  // we need to have a way to show unassigned active extensions
	  problem arises when a user switches themes with different areas.
	  
	  need an interface to generateLayout to get the default layout to show a user and allow
	  drag/drop of extensions on it.
	  
	  need to know if an extension can have more than one instance on a page i.e. we may have an extensions that
	  supports tags and content and we have two different views on the page
	  
	  This theme will show how to have different color schemes based on its own tables and configuration
	  
	  this theme will also need admin configuration options
	  
	  for demo purposes this theme will have a header, two columns, and a footer, one column will be an aside, one will be content
	  /------------------------------------------/
	  /                      1                   /
	  /------------------------------------------/
	  /         /                            /   /
	  /         /                            /   /
	  /         /                            /   /
	  /    2.1  /         2.2                / O / (Oveflow area)
	  /         /                            /   /
	  /         /                            /   /
	  /         /                            /   /
	  /         /                            /   /
	  /------------------------------------------/
	  /                     3                    /
	  /------------------------------------------/
	  
	  the admin version of this theme will have an extra section
	  for the menu
	  /------------------------------------------/
	  /                      1                   /
	  /------------------------------------------/
	  /         /                          /     /
	  /         /                          /     /
	  /         /                          /     /
	  /    2.1  /         2.2              / 2.3 /
	  /         /                          /     /
	  /         /                          /     /
	  /         /                          /     /
	  /         /                          /     /
	  /------------------------------------------/
	  /                     3                    /
	  /------------------------------------------/
 */
 
class TestTheme extends afm\Theme
{
	private $m_themePath = null;
	private $m_rootDir;
	private $m_settingsManager;
	private $m_mainContentSection;
	
	public function __construct()
	{
		
	}
	
	protected function configureAsActive()
	{
		// allows the dervied themes to configure themselves
		$this->requireStyleSheet('css/testtheme.css');
		
		$this->registerSection('1', 'drawHeader');
		$this->registerSection('2.1', 'drawAside'); // called second, assumption
		$this->registerSection('2.2', 'drawContent');
		
		$systemObj = & afm\System::getInstance();
		$settingsManager = & $systemObj->getSettingsManager();
		
		$pageDomain = $settingsManager->getSetting(PAGE_DOMAIN);
		
		if ($pageDomain == PAGE_ADMIN_DOMAIN)
		{
			$this->registerSection('2.3', 'drawMenuArea');			
		}
		$this->registerSection('3', 'drawFooter');
		$this->registerSection(THEME_OVERFLOW_AREA, 'drawOverflow');

		$this->m_rootDir = $systemObj->getBaseSystemDir();
		include_once($this->m_rootDir . 'configuration/SettingManager.php');

		$this->m_settingsManager = $settingsManager;
		
		$this->m_mainContentSection = new afm\SectionElement("main_content_box");
	}

	protected function & drawHeader()
	{
		$systemObj = & afm\System::getInstance();

		$pageObject = &$systemObj->getPageObject();
		
		$headerElement = $pageObject->addHeader("header_section");
		
		$pageObject->addLabel("header_label", $this->m_settingsManager->getSetting(SITE_TITLE), $headerElement);
		
		return $headerElement;
	}
	
	protected function & drawAside()
	{
		$systemObj = & afm\System::getInstance();

		$pageObject = &$systemObj->getPageObject();
		
		$pageObject->addChildElement($this->m_mainContentSection);
		
		$aside = $pageObject->addAside("main_aside", $this->m_mainContentSection);
				
		return $aside;
	}
	
	protected function & drawContent()
	{
		$systemObj = & afm\System::getInstance();

		$pageObject = &$systemObj->getPageObject();
		
		$content = $pageObject->addSection("main_content", $this->m_mainContentSection);

		$this->setMainContentArea($content);
		
		return $content;
	}
	
	protected function & drawFooter()
	{
		$systemObj = & afm\System::getInstance();

		$pageObject = &$systemObj->getPageObject();
		
		$footerElement = $pageObject->addFooter("footer_section");
				
		$pageObject->addLabel("footer_label", $this->m_settingsManager->getSetting(SITE_TAGLINE), $footerElement);
		
		return $footerElement;
	}
	
	protected function & drawMenuArea()
	{
		$systemObj = & afm\System::getInstance();

		$pageObject = &$systemObj->getPageObject();
		
		$menuArea = $pageObject->addAside("main_menu", $this->m_mainContentSection);

		return $menuArea;		
	}
	
	protected function & drawOverflow()
	{
		if ($this->m_mainContentSection != null)
		{
		$systemObj = & afm\System::getInstance();

		$pageObject = &$systemObj->getPageObject();
			
			$content = $pageObject->addSection("main_overflow", $this->m_mainContentSection);
		}
	}
}

?>
