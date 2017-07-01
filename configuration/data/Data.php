<?php
/**
 * @module data
 *
 * @brief base data class for the system tables
 */
 namespace afm
 {
	include_once('IData.php');
	
	define('NEW_DB_ENTRY', 0); // db defaults to 1 and counts up
	define('DATA_ACTIVE_CHANGED', 1);
	
	class Data implements \JsonSerializable
	{
		private $m_id;
		private $m_active;
		private $m_timeStamp;
		private $m_changes;
		private $m_table = null;
		
		public function __construct()
		{
			$this->m_id = NEW_DB_ENTRY;
			$this->m_active = false;
			$this->m_changes = 0;
			$this->m_timeStamp = null;//date("Y-m-d H:i:s");
		}
		
		public function reset()
		{
			$this->m_id = NEW_DB_ENTRY;
			$this->m_active = false;
			$this->m_changes = 0;
			$this->m_timeStamp = null;
		}
		/**
		 * @fn load
		 *
		 * @copydoc IData::load
		 */
		public function load($id)
		{
			if ($this->m_table != null)
			{				
				$resultSet = $this->m_table->loadRow("id='" . $id . "'");
				
				if ($resultSet != null)
				{
	                if ($row = $resultSet->fetch(\PDO::FETCH_LAZY))
	                {
		                $this->fromSQL($row);
		            }
					$resultSet = null; // done w/ it
				}
			}
			// get the table to load from
			// build the query and process it
		}

		/**
		 * @fn save
		 *
		 * @copydoc IData::save
		 */
		public function save()
		{
			if ($this->m_table != null)
			{
				$userData = $this->toArray();
				
				if ($this->getId() == NEW_DB_ENTRY)
				{
					$this->setId($this->m_table->addRow($userData));
				}
				else
				{
					$this->m_table->modifyRow($userData, "id='" . $this->getId() . "'");
				}
			}
		}

		/**
		 * @fn setId
		 *
		 * @copydoc IData::setId
		 */
		public function setId($id)
		{
			$this->m_id = $id;
		}
		 
		/**
		 * @fn getId
		 *
		 * @copydoc IData::getId
		 */
		public function getId()
		{
			return $this->m_id;
		}
		
		/**
		 * @fn setActive
		 *
		 * @copydoc IData::setActive
		 */
		public function setActive($isActive)
		{
			$this->m_active = $isActive;
			
			$this->setChange(DATA_ACTIVE_CHANGED);
		}
		
		/**
		 * @fn getActive
		 *
		 * @copydoc IData::getActive
		 */
		public function getActive()
		{
			return $this->m_active;
		}
		
		/**
		 * @fn setTimestamp
		 *
		 * @copydoc IData::setTimestamp
		 */
		public function setTimestamp($timeStamp)
		{
			$this->m_timestamp = $timeStamp;
		}
		
		/**
		 * @fn getTimestamp
		 *
		 * @copydoc IData::getTimestamp
		 */
		public function getTimestamp()
		{
			return $this->m_timestamp;
		}

		/**
		 * @brief JsonSerializable
		 */		
	    public function jsonSerialize()
		{
			$jsonData =
			[
                'id' => $this->getId(),
                'active' => $this->getActive() == true ? 'true' : 'false',
				'timeStamp'=> $this->getTimeStamp()
			];

            return $jsonData;
        }

		// internal methods
		protected function fromSQL($dbObject)
		{
			$this->setId($dbObject->id);
			$this->setActive($dbObject->active == 1 ? true : false);
			$this->setTimestamp($dbObject->time_stamp);
			
			$this->clearChanges();
		}
		
		protected function setChange($changedBit)
		{
			$this->m_changes |= $changedBit;
		}
		
		protected function isChanged($changedBit)
		{
			$hasChanged = ($this->m_changes & $changedBit) ? true : false;
			
			return $hasChanged;
		}
		
		protected function clearChanges()
		{
			$this->m_changes = 0;
		}
		
		protected function toArray()
		{
			$arrayRepresentation = array();
		
//			$arrayRepresentation['id'] = $this->getId();  // db sets this - auto-increment

			if ($this->isChanged(DATA_ACTIVE_CHANGED) == true)
			{
				$arrayRepresentation['active'] = $this->getActive() == true ? "true" : "false";
			}
//			$arrayRepresentation['time_stamp'] = $this->getTimestamp(); // db creates it, we don't set it
			
			return $arrayRepresentation;
		}
		
		protected function setTable(&$table)
		{
			$this->m_table = $table;
		}
		
		protected function &getTable()
		{
			return $this->m_table;
		}

		protected function isNewEntry()
		{
			return $this->m_id == NEW_DB_ENTRY;
		}

		protected function loadRow($whereClause)
		{
			$success = false;

			$resultSet = $this->m_table->loadRow($whereClause);
			
			if ($resultSet != null)
			{
                if ($row = $resultSet->fetch(\PDO::FETCH_LAZY))
                {
	                $this->fromSQL($row);
	                
	                $success = true;
	            }
				$resultSet = null; // done w/ it
			}
			return $success;
		}

		protected function loadMultipleRows($whereClause)
		{
			$dataArray = array();

			$resultSet = $this->m_table->loadRow($whereClause);
			
			if ($resultSet != null)
			{
				$sqlObjs = $resultSet->fetchAll(\PDO::FETCH_OBJ); 
                foreach ($sqlObjs as $sqlObj)
                {
					$dataObj = clone $this;
					$dataObj->fromSQL($sqlObj);
					$dataArray[] = $dataObj;
	            }
				$resultSet = null; // done w/ it
			}

			return $dataArray;
		}

		protected function loadMultipleRowsWithLimit($whereClause, $startRow, $rowCount)
		{
			$dataArray = array();

			$resultSet = $this->m_table->loadRowsWithLimit($whereClause, $startRow, $rowCount);
			
			if ($resultSet != null)
			{
				$sqlObjs = $resultSet->fetchAll(\PDO::FETCH_OBJ); 
                foreach ($sqlObjs as $sqlObj)
                {
					$dataObj = clone $this;
					$dataObj->fromSQL($sqlObj);
					$dataArray[] = $dataObj;
	            }
				$resultSet = null; // done w/ it
			}

			return $dataArray;
		}	}
}
?>
