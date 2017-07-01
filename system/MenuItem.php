<?php
/**
 * @module MenuItem
 *
 * @brief class to define a menu item for displaying
 */
namespace afm
{	
	class MenuItem
	{
		private $m_title;
		private $m_id;
		private $m_class;
		private $m_link;
		private $m_image;
		
		public function __construct($id)
		{
			$this->m_title = "menu";
			$this->m_id = $id;
			$this->m_class = "menu";
			$this->m_link = "";
			$this->m_image = null;
		}
		
		static public function withTitleAndLink($id, $title, $link)
		{
			$menuItem = new MenuItem($id);
			
			$menuItem->setTitle($title);
			$menutItem->setLink($link);
			
			return $menuItem;
		}

		static public function withAll($id, $title, $class, $link)
		{
			$menuItem = new MenuItem($id);
			
			$menuItem->setClass($class);
			$menuItem->setTitle($title);
			$menuItem->setLink($link);
			
			return $menuItem;
		}
		
		public function setImageLink($imageLink)
		{
			$this->m_image = $imageLink;
		}
		
		public function getImageLink()
		{
			return $this->m_image;
		}
		
		public function getTitle()
		{
			return $this->m_title;
		}
		
		public function setTitle($title)
		{
			$this->m_title = $title;
		}
		
		public function getClass()
		{
			return $this->m_class;
		}
		
		public function setClass($class)
		{
			$this->m_class = $class;
		}

		public function getId()
		{
			return $this->m_id;
		}
		
		public function getLink()
		{
			return $this->m_link;
		}
		
		public function setLink($link)
		{
			$this->m_link = $link;
		}
	}	
}	
?>