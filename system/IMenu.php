<?php
/**
 * @module IMenu
 *
 * @brief menu interface for all menu widgets to implement
 */
namespace afm
{
	include_once('MenuItem.php');
	
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
	interface IMenu
	{
		/**
		 *
		 */
		public function addMenuHeader($header);

		/**
		 * @brief adds a menu entry to the menu object
		 *
		 * @param[in] $menuTitle - the title for the menu
		 * @param[in] $menuId - the unique id for the menu
		 * @param[in] $class - any classes to style the menu
		 * @param[in] $link - the target/link for the menu item
		 */
		public function addEntry($menuId, $menuTitle, $class, $link);
		
		/**
		 * @brief removes a menu item
		 *
		 * @param[in] $menuId the unique menu id to remove
		 */
		public function removeEntry($menuId);		
	}	
}	
?>