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

define('DEFAULT_THEME_HEADER_ID', "0");
define('DEFAULT_THEME_MAIN_ASIDE_ID', "1");
define('DEFAULT_THEME_MAIN_CONTENT_ID', "2");
define('DEFAULT_THEME_FOOTER_ID', "3");
define('DEFAULT_THEME_MENU_ID', "4");
define('DEFAULT_THEME_OVERFLOW_ID', "5");

class DefaultTheme extends afm\Theme
{
	private $m_themePath = null;
	private $m_rootDir;
	private $m_settingsManager;
	private $m_mainContentSection;
	private $m_previewContentSection;
	private $m_sectionIds;
	
	public function __construct()
	{
		$this->m_sectionIds = array();

		$this->m_sectionIds[DEFAULT_THEME_HEADER_ID] = "header_section";
		$this->m_sectionIds[DEFAULT_THEME_MAIN_ASIDE_ID] = "main_aside";
		$this->m_sectionIds[DEFAULT_THEME_MAIN_CONTENT_ID] = "main_content";
		$this->m_sectionIds[DEFAULT_THEME_FOOTER_ID] = "footer_section";
		$this->m_sectionIds[DEFAULT_THEME_MENU_ID] = "main_menu";
		$this->m_sectionIds[DEFAULT_THEME_OVERFLOW_ID] = "main_overflow";
	}
	
	/**
	 * @copydoc ITheme::preview
	 */
	public function preview(& $parentElement)
	{
		$results = array();

		// override the base class implementation
		// instead of the standard image, show a small snapshot
		if ($parentElement != null)
		{
			$this->requireStyleSheet("css/preview.css");

			foreach ($this->m_sectionIds as $name=>$value)
			{
				$this->m_sectionIds[$name] .= "_preview";
			}

			$this->setPreview(true);

			$this->m_previewContentSection = new afm\SectionElement("main_content_box_preview");

			$results = $this->populate($parentElement);

			$this->setPreview(false);
		}

		return $results;
	}
	
	protected function configureAsActive()
	{
		$this->requireStyleSheet('css/theme.css');
		
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
	
	protected function & drawHeader($parentElement)
	{
		$headerElement = afm\HeaderElement::withParent($parentElement, $this->m_sectionIds[DEFAULT_THEME_HEADER_ID]);
		
		if ($this->getPreview() == false)
		{
			afm\LabelElement::withParent($headerElement, "header_label", $this->m_settingsManager->getSetting(SITE_TITLE));
		}
		return $headerElement;
	}
	
	protected function & drawAside($parentElement)
	{
		$mainContentArea = $this->getMainThemeContentArea();

		$parentElement->addChildElement($mainContentArea);

		$aside = afm\AsideElement::withParent($mainContentArea, $this->m_sectionIds[DEFAULT_THEME_MAIN_ASIDE_ID]);

		return $aside;
	}
	
	protected function & drawContent($parentElement)
	{
		$mainContentArea = $this->getMainThemeContentArea();

		$content = afm\SectionElement::withParent($mainContentArea, $this->m_sectionIds[DEFAULT_THEME_MAIN_CONTENT_ID]);

		if ($this->getPreview() == false)
		{
			$this->setMainContentArea($content);
		}
		
		return $content;
	}
	
	protected function & drawFooter($parentElement)
	{
		$footerElement = afm\FooterElement::withParent($parentElement, $this->m_sectionIds[DEFAULT_THEME_FOOTER_ID]);

		if ($this->getPreview() == false)
		{
			afm\LabelElement::withParent($footerElement, "footer_label", $this->m_settingsManager->getSetting(SITE_TAGLINE));
		}
		return $footerElement;
	}
	
	protected function & drawMenuArea($parentElement)
	{
		if ($this->getPreview() == false)
		{
			$mainContentArea = $this->getMainThemeContentArea();

			$menuArea = afm\AsideElement::withParent($mainContentArea, $this->m_sectionIds[DEFAULT_THEME_MENU_ID]);
		}
		return $menuArea;		
	}
	
	protected function & drawOverflow($parentElement)
	{
		$overflow = null;

		$mainContentArea = $this->getMainThemeContentArea();
		if ($mainContentArea != null)
		{			
			$overflow = afm\AsideElement::withParent($mainContentArea, $this->m_sectionIds[DEFAULT_THEME_OVERFLOW_ID]);
		}

		return $overflow;
	}

	protected function getMainThemeContentArea()
	{
		$mainContentArea = null;

		if ($this->getPreview() == false)
		{
			$mainContentArea = $this->m_mainContentSection;
		}
		else
		{
			$mainContentArea = $this->m_previewContentSection;
		}

		return $mainContentArea;
	}
}

?>
