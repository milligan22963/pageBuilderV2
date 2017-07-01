<?php
/**
 * @module Login
 *
 * @brief the login extension to allow a simple login procedure
 */

$systemObj = afm\System::getInstance();

$systemObj->includeLocalFile('Menu.php');

class MenuWidget extends afm\Menu
{
	private $m_menuBox;
	private $m_headerCount = 1;
	
	public function __construct()
	{
		parent::__construct();

		$this->m_menuBox = null;
	}
	
	public function addMenuHeader($header)
	{
		$headerObj = null;

		if ($this->m_menuBox != null)
		{
			$id = "menu_header_" . $this->m_headerCount++;

			$headerObj = afm\DivElement::withParent($this->m_menuBox, $id);
			$idLabel = $id . "_label";
			$label = afm\LabelElement::withParent($headerObj, $idLabel, $header);			
		}

		return $headerObj;
	}

	/**
	 * @fn populate
	 *
	 * @copydoc IExtension::populate
	 */
	public function populate(& $parentElement)
	{
		$systemObj = afm\System::getInstance();

		$pageObject = & $systemObj->getPageObject();
		
		$this->requireStyleSheet('css/menu.css');

		$this->m_menuBox = new afm\SectionElement("menu_content_box");

		$parentElement->addChildElement($this->m_menuBox);
		
		$pageObject->addLabel("menu_title", "Menu", $this->m_menuBox);

		// we add in the main home entry, other extensions will add after processing
   		$this->addEntry('home', 'Home', 'user_menu', $systemObj->getSiteRootURL() . 'index.php');
	}
	
	/**
	 * @fn addEntry
	 *
	 * @copydoc IMenu::addEntry
	 */
	public function addEntry($menuId, $menuTitle, $class, $link)
	{
		parent::addEntry($menuId, $menuTitle, $class, $link);
		
		// add it right now
		$systemObj = afm\System::getInstance();

		$pageObject = & $systemObj->getPageObject();

		$menuDiv = $pageObject->addDiv($menuId . 'div', $this->m_menuBox);
		$menuDiv->addClass("menu");
		$menuLink = $pageObject->addAnchor($menuId, $link, $menuTitle, $menuDiv);
		$menuLink->addClass($class);
	}	
}
?>
