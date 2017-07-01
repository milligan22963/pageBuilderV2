<?php
/**
 * @module ExtensionData
 *
 * @brief manages extension activation/de-activation etc for the system
 */
 namespace afm
 {
	include_once('Data.php');
    include_once('ExtensionTypes.php');
	
	class ExtensionData extends Data
	{
		private $m_path;
        private $m_location; // sectionId
        private $m_type;
		
		public function __construct()
		{
			parent::__construct();

            $this->m_path = null;
            $this->m_location = null;

			$systemObj = &System::getInstance();
			$dbInstance = &$systemObj->getDatabase();
			$this->setTable($dbInstance->getTable(EXTENSION_TABLE));

            $this->m_type = CUSTOM_TYPE;
		}
		
		static public function withPathAndLocation($path, $location)
		{
			$extData = new ExtensionData();

            $extData->setPath($path);
            $extData->setLocation($location);
			
			return $extData;
		}
		
		public function setPath($path)
		{
			$this->m_path = trim($path);
		}
		
		public function getPath()
		{
			return $this->m_path;
		}
		
		public function setLocation($location)
		{
			$this->m_location = trim($location);
		}
		
		public function getLocation()
		{
			return $this->m_location;
		}

        public function setType($type)
        {
			$extType = ExtensionType::withId($type);
			
			$this->m_type = $extType->getType();
        }

        public function getType()
        {
            return $this->m_type;
        }

		public function getTypeId()
		{
			// convert the type to the id representation
			$extType = ExtensionType::withType($this->m_type);

			return $extType->getId();
		}

		function loadExtension($path)
		{
			$success = $this->loadRow("path='" . $path . "'");
			
			return $success;
		}

        public function getExtensionCount($activeOnly = true)
        {
			$table = &$this->getTable();

            $whereClause = null;
            if ($activeOnly == true)
            {
                $whereClause = "active='true'";
            }
			$extCount = $table->numRows($whereClause);
            
            return $extCount;
        }

        /**
         * @brief get all the extensions (perhaps in a specific location)
         *
         * @param[in] $location - the location of the extension or null for any
         * @param[in] $activeOnly - only load active extensions
         */
        public function getAll($location = null, $activeOnly = true)
        {
            $extensionArray = array();

			$table = &$this->getTable();
			
            $whereClause = null;

            if ($activeOnly == true)
            {
                $whereClause = "active='true'";
            }

            if ($location != null)
            {
                if ($whereClause != null)
                {
                    // add in the 'and'
                    $whereClause .= ' and ';
                }
                else
                {
                    $whereClause = ''; // non null because we will be setting the location
                }
                $whereClause .= "location like '" . $location . "%' order by location asc";
            }

			$results = $table->loadRow($whereClause);
			
			if ($results != null)
			{
				if ($results != null)
				{
					$extensions = $results->fetchAll(\PDO::FETCH_OBJ);
					foreach ($extensions as $extension)
					{
                        $extData = new ExtensionData();

                        $extData->fromSQL($extension);

                        $extensionArray[] = $extData;
                    }
                }
				$results = null; // done w/ it
			}

            return $extensionArray;
        }

		// internal methods
		protected function fromSQL($dbObject)
		{
			parent::fromSQL($dbObject);
			
            $this->setPath($dbObject->path);
            $this->setLocation($dbObject->location);
            $this->setType($dbObject->type);
		}

		protected function toArray()
		{
			$arrayRepresentation = parent::toArray();

//            error_log('prepping save: ' . print_r($arrayRepresentation, true));

			$arrayRepresentation['path'] = $this->getPath();
			$arrayRepresentation['location'] = $this->getLocation();
			$arrayRepresentation['type'] = $this->getTypeId();

            error_log('data: ' . print_r($arrayRepresentation, true));
			
			return $arrayRepresentation;
		}
	}
}
?>
