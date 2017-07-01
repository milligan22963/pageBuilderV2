<?php
/**
 * @module SettingGroup
 *
 * @brief manages settings groups
 */
 namespace afm
 {
	include_once('Data.php');
		
	class SettingGroup extends Data
	{
		private $m_tag;
		private $m_name;
		private $m_description;
		private $m_adminEditable;

		public function __construct()
		{
			parent::__construct();

			$this->m_name = 'none';
			$this->m_description = 'none';
			$this->m_adminEditable = true;
			$this->m_tag = "NN";
			
			$systemObj = &System::getInstance();
			$dbInstance = &$systemObj->getDatabase();

			$this->setTable($dbInstance->getTable(SETTING_GROUP_TABLE));
		}
				
		static function withId($id)
		{
			$group = new SettingGroup();
			
			$group->load($id);
			
			return $group;
		}
		
		public function setTag($tag)
		{
			$this->m_tag = $tag;
		}

		public function getTag()
		{
			return $this->m_tag;
		}

		public function setName($name)
		{
			$this->m_name = trim($name);			
		}
		
		public function getName()
		{
			return $this->m_name;
		}

		public function setDescription($description)
		{
			$this->m_description = $description;
		}

		public function getDescription()
		{
			return $this->m_description;
		}
		
		public function setAdminEditable($adminEditable)
		{
			$this->m_adminEditable = $adminEditable;
		}

		public function getAdminEditable()
		{
			return $this->m_adminEditable;
		}

		public function loadGroupByTag($tag)
		{
			return $this->loadRow("tag='" . $tag . "'");
		}

		public function getAllGroups($adminEditable)
		{
			$whereClause = null;

			if ($adminEditable == true)
			{
				$whereClause = "admin_edit='true'";
			}
			return $this->loadMultipleRows($whereClause);
		}

		// internal methods
		protected function fromSQL($dbObject)
		{
			parent::fromSQL($dbObject);

			$this->setTag(trim($dbObject->tag));
			$this->setName(trim($dbObject->name));
			$this->setDescription(trim($dbObject->description));
			$this->setAdminEditable($dbObject->admin_edit == 'true' ? true : false);
		}

		protected function toArray()
		{
			$arrayRepresentation = parent::toArray();

			$arrayRepresentation['tag'] = $this->getTag();
			$arrayRepresentation['name'] = $this->getName();
			$arrayRepresentation['description'] = $this->getDescription();
			$arrayRepresentation['admin_edit'] = $this->getAdminEditable() == true ? "true" : "false";
			
			return $arrayRepresentation;
		}
	}
}
?>
