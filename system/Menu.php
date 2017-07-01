<?php
/**
 * @module Menu
 *
 * @brief base menu implementation for all menu widgets to implement
 */
namespace afm
{
	include_once('MenuItem.php');
	include_once('IMenu.php');
	include_once('Extension.php');
	
	/**
	 * @note the menus will for now just be added at server side so removing them is pointless
	 *       however in the future, I may have a table w/ menu items in it which could be added
	 *       and removed based on the user's choice
	 *
	 *		when that is a reality then the removeEntry will be useful as it will remove it from the database
	 *		the database allows the menu to be created wherever it is needed and speeds up page loads
	 *      as it will be a read from the DB opposed to server side adding.
	 *
	 *		it may also allow a little more order when a number of widgets all want to add menu items...
	 */
	class Menu extends Extension implements IMenu
	{
		private $m_menuOptions;
		
		public function __construct()
		{
			parent::__construct();
			
			$this->m_menuOptions = array();

			$this->setType(MENU_TYPE);
		}

		public function addMenuHeader($header)
		{
			// implementations will need to handle this
		}

		/**
		 * @copydoc IMenu::addEntry
		 */
		public function addEntry($menuId, $menuTitle, $class, $link)
		{
			$menuItem = MenuItem::withAll($menuId, $menuTitle, $class, $link);
			
			$this->m_menuOptions[$menuId] = $menuItem;			
		}
		
		/**
		 * @copydoc IMenu::removeEntry
		 */
		public function removeEntry($menuId)
		{
			unset($this->m_menuOptions[$menuId]);		
		}
	}	
}	
?>