<?php
/**
 * @module Settings
 *
 * @brief manages the settings table for the system
 */
 namespace afm
 {
	include_once('Data.php');
	
	// read only implies admin cannot edit
	// however some items are read/write but can
	// only be updated via the website i.e. active theme
	class Setting extends Data
	{
		private $m_groupId;
		private $m_description;
		private $m_name;
		private $m_value;
		private $m_type; // the type that this corresponds to such as boolean
		private $m_readOnly;	/* once set doesn't change */
		private $m_adminEdit; /* indicates a admin can edit directly */
		
		public function __construct()
		{
			parent::__construct();
			
			$this->m_group = "none";
			$this->m_name = "none";
			$this->m_value = null;
			$this->m_readOnly = true;
			$this->m_adminEdit = false;
			$this->m_description = "none";
			$this->m_type = STRING;

			$systemObj = &System::getInstance();
			$dbInstance = &$systemObj->getDatabase();

			$this->setTable($dbInstance->getTable(SETTING_TABLE));
		}
		
		static public function withNameAndValue($name, $value)
		{
			$setting = new Setting();
			
			$setting->setName($name);
			$setting->setValue($value);
			
			return $setting;
		}
		
		public function setGroupId($groupId)
		{
			$this->m_groupId = $groupId;
		}

		public function getGroupId()
		{
			return $this->m_groupId;
		}

		public function setDescription($description)
		{
			$this->m_description = $description;
		}

		public function getDescription()
		{
			return $this->m_description;
		}

		public function setName($name)
		{
			$this->m_name = $name;
		}
		
		public function getName()
		{
			return $this->m_name;
		}
		
		public function setValue($value)
		{
			$this->m_value = $value;
		}
		
		public function getValue()
		{
			return $this->m_value;
		}

		public function setType($type)
		{
			$this->m_type = $type;
		}
		
		public function getType()
		{
			return $this->m_type;
		}

		public function setReadOnly($readOnly)
		{
			$this->m_readOnly = $readOnly;
		}

		public function getReadOnly()
		{
			return $this->m_readOnly;
		}

		public function setAdminEdit($adminEdit)
		{
			$this->m_adminEdit = $adminEdit;
		}

		public function getAdminEdit()
		{
			return $this->m_adminEdit;
		}
				
		public function getSetting($settingName)
		{
			return $this->loadRow("name='" . $settingName . "'");
		}

		public function getAllSettings($readOnly, $adminEditable)
		{
			$whereClause = "read_only='";

			if ($readOnly == true)
			{
				$whereClause .= "true";
			}
			else
			{
				$whereClause .= "false";
			}

			$whereClause .= "' and admin_edit='";

			if ($adminEditable == true)
			{
				$whereClause .= "true";
			}
			else
			{
				$whereClause .= "false";
			}

			$whereClause .= "'";

			return $this->loadMultipleRows($whereClause);
		}

		/**
		 * @brief special handling of save for settings
		 * @copydoc IData::save
		 */
		public function save()
		{
			// if it is a new entry or its not read only then we can save it
			if (($this->isNewEntry() == true) || ($this->getReadOnly() == false))
			{
				if ($this->getAdminEdit() == true)
				{
					parent::save();
				}
			}
		}

		// internal methods
		protected function fromSQL($dbObject)
		{
			parent::fromSQL($dbObject);
			
			$this->setGroupId($dbObject->group_id);
			$this->setDescription(trim($dbObject->description));
			$this->setName(trim($dbObject->name));
			$this->setValue(trim($dbObject->value));
			$this->setType(trim($dbObject->type));
			$this->setAdminEdit($dbObject->admin_edit == 1 ? true : false);
			$this->setReadOnly($dbObject->read_only == 1 ? true : false);
		}

		protected function toArray()
		{
			$arrayRepresentation = parent::toArray();

			$arrayRepresentation['group_id'] = $this->getGroupId();
			$arrayRepresentation['description'] = $this->getDescription();
			$arrayRepresentation['name'] = $this->getName();
			$arrayRepresentation['value'] = $this->getValue();
			$arrayRepresentation['type'] = $this->getType();
			$arrayRepresentation['read_only'] = $this->getReadOnly() == true ? "true" : "false";
			$arrayRepresentation['admin_edit'] = $this->getAdminEdit() == true ? "true" : "false";

			return $arrayRepresentation;
		}
	}
}
?>
