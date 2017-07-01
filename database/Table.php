<?php
/**
 * @module Table
 *
 * @brief base class for all table interaction
 */

namespace afm
{
    // Includes
    include_once('ITable.php');
    include_once('Column.php');

	class Table implements ITable
	{
		private $m_name;
		private $m_columns;
		private $m_description = null;
		private $m_dbInstance;
		
		public function __construct($dbInstance)
		{
			$this->m_name = null;
			$this->m_columns = array();
			$this->m_dbInstance = $dbInstance;
		}
				
		/**
		 * @fn setName
		 *
		 * @copydoc ITable::setName
	 	 */
		public function setName($tableName)
		{
			$this->m_name = $tableName;
		}
		
		/**
		 * @fn getName
		 *
		 * @copydoc ITable::getName
	 	 */
		public function getName()
		{
			return $this->m_name;
		}

		/**
		 * @fn setDescription
		 * 
		 * @copydoc ITable::setDescription
	 	 */
		public function setDescription($description)
		{
			$this->m_description = $description;
		}

		/**
		 * @fn getDescription
		 * 
		 * @copydoc ITable::getDescription
	 	 */
		public function getDescription()
		{
			return $this->m_description;
		}

		/**
		 * @fn create
		 *
		 * @copydoc ITable::create
		 */
		public function create()
		{
			$createString = "CREATE TABLE " . $this->getName() . " ( ";
			
			foreach ($this->m_columns as $column)
			{
				$createString .= $column->toString();
			}
			
			$createString .= " ) COMMENT='" . $this->getDescription() . "'";
						
			$success = $this->m_dbInstance->executeCommand($createString);
			
			return $success;
		}
		
		/**
		 * @fn drop
		 *
		 * @copydoc ITable::drop
		 */
		public function drop()
		{
			$success = false;
			
			if ($this->m_dbInstance != null)
			{
				$dropString = "DROP TABLE " . $this->getName();

				$this->m_dbInstance->executeCommand($dropString);
				
				$success = true;
			}
			
			return $success;
		}	

		/**
		 * @fn doesExist
		 *
		 * @copydoc ITable::doesExist
		 */
		public function doesExist()
		{
			
		}	
		
		/**
		 * @fn createColumn
		 * 
		 * @copydoc ITable::createColumn
		 *
		 * @note when calling you need to do: $newColumn = &$someTable->createColumn("newcolumn");
		 */
		public function &createColumn($columnName, $dataType, $typeLength = null, $typePrecision = null, $allowNull = false, $isIndex = false, $isPrimary = false)
		{
			$column = &$this->createDerivedColum($columnName);
			
			$column->setType($dataType);
			$column->setTypeLength($typeLength);
			$column->setTypePrecision($typePrecision);
			$column->setAllowNull($allowNull);
			$column->setAsIndex($isIndex);
			$column->setPrimaryKey($isPrimary);
			
			$this->addColumn($column);
			
			return $column;
		}
		
		/**
		 * @copydoc ITable::getColumn
		 */
		public function getColumn($columnName)
		{
			$targetColumn = null;
			
			foreach ($this->m_columns as $column)
			{
				if ($column->getName() == $columnName)
				{
					$targetColumn = $column;
					break;
				}
			}
			
			return $targetColumn;
		}
		
		/**
		 * @fn load
		 * 
		 * @copydoc ITable::load
		 */
		public function load($columnDetails)
		{
			$systemObject = & System::getInstance();
			
			$configDir = $systemObject->getBaseSystemDir() . '/configuration/';
			include_once($configDir . 'XmlDefines.php');
			
			$columns = $columnDetails->getChildElements(COLUMN_ELEMENT);
			
			// load column from xml nodes
			foreach ($columns as $column)
			{
				$name = $column->getAttribute(NAME_ATTR);
				$dataType = $column->getAttribute(TYPE_ATTR);
				if (($name != null) && ($dataType != null))
				{
					$dataType = $this->m_dbInstance->getDataType($dataType);
					$typeLength = convertValue(INTEGER, $column->getAttribute(TYPE_LENGTH_ATTR), null);
					$typePrecision = convertValue(INTEGER, $column->getAttribute(TYPE_PRECISION_ATTR), null);
					$allowNull = convertValue(BOOLEAN, $column->getAttribute(ALLOW_NULL_ATTR), false);
					$isIndex = convertValue(BOOLEAN, $column->getAttribute(IS_INDEX_ATTR), false);
					$isPrimary = convertValue(BOOLEAN, $column->getAttribute(IS_PRIMARY_ATTR), false);
					$defaultValue = convertValue(STRING, $column->getAttribute(DEFAULT_ATTR), null);
					
					$tableColumn = &$this->createColumn($name, $dataType, $typeLength, $typePrecision, $allowNull, $isIndex, $isPrimary);
					
					if ($defaultValue != null)
					{
//						error_log('Default value: ' . $defaultValue);
						$tableColumn->setDefaultValue($defaultValue);
					}
				}
				else
				{
					error_log('Bad name (' . $name . ') or data type (' . $dataType . ')');
				}
			}
		}

		/**
		 * @fn addRow
		 *
		 * @copydoc ITable::addRow
		 */
		public function addRow($rowDetailsArray)
		{
			$assignedId = 0;

			$failed = false;
			$badColumn = null;
			$columnCount = count($rowDetailsArray);
			$currentColumn = 0;
			
			$command = "INSERT INTO " . $this->getName() . " (";
			$values = " VALUES (";
			foreach ($rowDetailsArray as $name => $value)
			{
				$targetColumn = $this->getColumn($name);
				if ($targetColumn != null)
				{
					$command .= $name;	
					$formatString = null;
					
					$columnType = $targetColumn->getType();
					$systemType = $this->m_dbInstance->getSystemDataType($columnType);

					if ($systemType != null)
					{
						$formatString = $this->m_dbInstance->getTypeModifier($systemType);
					}
					
					if ($formatString != null)
					{
						$value = \sprintf($formatString, $value);
					}				
					$values .= $value;	
					
					$currentColumn++;
					
					// more coming?
					if ($currentColumn < $columnCount)
					{
						$command .= ", ";
						$values .= ", ";
					}
				}
				else
				{
					$badColumn = $name;
					$failed = true;
					break;
				}
			}
			
			if ($failed == false)
			{
				$command .= ") ";
				$values .= ")";
				
//				error_log('Insert: ' . $command . $values);
				$this->m_dbInstance->executeCommand($command . $values);

				$assignedId = $this->getLastInsertId();
			}
			else
			{
				error_log('Unable to add row - bad column name: ' . $badColumn);
			}

			return $assignedId;
		}

		/**
		 * @fn deleteRow
		 *
		 * @copydoc ITable::deleteRow
		 */
		public function deleteRow($rowDetailsArray)
		{
			
		}

		/**
		 * @fn modifyRow
		 *
		 * @copydoc ITable::modifyRow
		 */
		public function modifyRow($rowDetailsArray, $whereClause)
		{
			$failed = false;
			$badColumn = null;
			$columnCount = count($rowDetailsArray);
			$currentColumn = 0;
			
			$command = "UPDATE " . $this->getName() . " SET ";
			foreach ($rowDetailsArray as $name => $value)
			{
				$targetColumn = $this->getColumn($name);
				if ($targetColumn != null)
				{
					$command .= $name . " = ";
					$formatString = null;
					
					$columnType = $targetColumn->getType();
					$systemType = $this->m_dbInstance->getSystemDataType($columnType);

					if ($systemType != null)
					{
						$formatString = $this->m_dbInstance->getTypeModifier($systemType);
					}
					
					if ($formatString != null)
					{
						$value = \sprintf($formatString, $value);
					}				
					$command .= $value;						
					
					$currentColumn++;
					
					// more coming?
					if ($currentColumn < $columnCount)
					{
						$command .= ", ";
					}
				}
				else
				{
					$badColumn = $name;
					$failed = true;
					break;
				}
			}
			
			$command .= " WHERE " . $whereClause;
			
			if ($failed == false)
			{
				error_log('Update: ' . $command);
				$this->m_dbInstance->executeCommand($command);
			}
			else
			{
				error_log('Unable to add row - bad column name: ' . $badColumn);
			}
		}

		/**
		 * @copydoc ITable::loadRow
		 */
		public function loadRow($whereClause = null)
		{
			$command = "SELECT * FROM " . $this->getName();
			
			if ($whereClause != null)
			{
				$command .= " WHERE " . $whereClause;
			}

//			error_log($command);
			return $this->m_dbInstance->issueCommand($command);
		}

		/**
		 * @copydoc ITable::loadRows
		 */
		public function loadRows($queryCommand)
		{
			error_log('Running query: ' . $queryCommand);

			return $this->m_dbInstance->issueCommand($queryCommand);
		}

		public function loadRowsWithLimit($whereClause = null, $startRow = null, $rowCount = null)
		{
			$command = "SELECT * FROM " . $this->getName();
			
			if ($whereClause != null)
			{
				$command .= " WHERE " . $whereClause;
			}

			if ($startRow != null)
			{
				$command .= " OFFSET " . $startRow;
			}

			if ($rowCount != null)
			{
				$command .= " LIMIT " . $rowCount;
			}

			return $this->m_dbInstance->issueCommand($command);			
		}

		/**
		 * @copydoc ITable::numRows
		 */
		 public function numRows($whereClause = null)
		 {
			 $numRows = 0;
			 
			 $command = "SELECT count(*) FROM " . $this->getName();
			 
			 if ($whereClause != null)
			 {
				 $command .= " WHERE " . $whereClause;
			 }
			 
			 $results = $this->m_dbInstance->issueCommand($command);
			 if ($results != null)
			 {
				 $rows = $results->fetch(\PDO::FETCH_NUM);
				 $numRows = intval($rows[0]); // should be one row with one entry
			 }
			 
			 return $numRows;
		 }

		/**
		 * @copydoc ITable::createQuery
		 */
		 public function createQuery()
		 {
			include_once('Query.php');

			$queryObj = new Query($this);

			return $queryObj;
		 }

		 /**
		  * @copydoc ITable::getLastInsertId
		  *
		  * This makes an assumption (which is true for AFM tables) that each table will have an *id* and a *time_stamp* column
		  */
		 public function getLastInsertId()
		 {
			 $lastId = 0;

			 $command = "select id from " . $this->getName() . " order by time_stamp desc limit 1";

			 $results = $this->m_dbInstance->issueCommand($command);
			 if ($results != null)
			 {
				 $row = $results->fetch(\PDO::FETCH_NUM);
//				 error_log('Row: ' . print_r($row, true));
				 $lastId = intval($row[0]);
			 }

			 return $lastId;
		 }

		 // internal
		protected function addColumn($column)
		{
			$this->m_columns[] = $column;
		}
		
		protected function getDbInstance()
		{
			return $this->m_dbInstance;
		}
		
		protected function getColumns()
		{
			return $this->m_columns;
		}
		
		protected function hasColumn($columnName)
		{
			$columnExists = false;
			
			foreach ($this->m_columns as $column)
			{
				if ($column->getName() == $columnName)
				{
					$columnExists = true;
					break;
				}
			}
			
			return $columnExists;
		}
		
		// derived classes can override as needed/desired
		protected function &createDerivedColum($columnName)
		{
			$column = new Column($columnName);
			
			return $column;			
		}
	}
} 
?>