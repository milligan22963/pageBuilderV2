<?php
/**
 * @module MySQLTable
 *
 * @brief derived class for all mysql table interaction
 */

namespace afm
{
	$systemObject = & System::getInstance();
	
	$baseDir = $systemObject->getBaseSystemDir();

    // Includes
    include_once($baseDir . 'database/Table.php');
    include_once('MySQLColumn.php');

	class MySQLTable extends Table
	{
		/**
		 * @fn create
		 *
		 * @copydoc ITable::create
		 */
		public function create()
		{
			$createString = "CREATE TABLE " . $this->getName() . " ( ";
			$indexArray = array();
			
			$columns = $this->getColumns();
			$colIndex = 0;
			$numColumns = count($columns);
			
			foreach ($columns as $column)
			{
				$createString .= $column->toString();
				
				if ($column->isIndex() == true)
				{
					$indexName = $this->getName() . '_' . $column->getName() . '_idx';
					$indexCommnd = 'CREATE INDEX on ' . $indexName . '(' . $column->getName() . ')';
					error_log('Index: ' . $indexCommnd);
					
					$indexArray[] = $indexCommnd;
				}
				
				$colIndex++;
				if ($colIndex < $numColumns)
				{
					$createString .= ', ';
				}

			}
			
			$createString .= " )";
			
			$dbInstance = $this->getDbInstance();
			
			error_log('Create: ' . $createString);
			
			$success = $dbInstance->executeCommand($createString);
			
			foreach ($indexArray as $index)
			{
				$dbInstance->executeCommand($index);
			}
			
			if ($success = true)
			{
				if ($this->getDescription() != null)
				{
					$addComment = 'COMMENT on table ' . $this->getName() . ' is ' . $this->getDescription();
					$dbInstance->executeCommand($addComment);
				}				
			}
			return $success;
		}
		
		/**
		 * @fn drop
		 *
		 * @copydoc ITable::drop
		 */
		public function drop()
		{
			$success = parent::drop();
			
			if ($success == true)
			{
				// if this table has any sequences then drop them too
				$columns = $this->getColumns();
				
				foreach ($columns as $column)
				{
					if ($column->isIndex() == true)
					{
						$indexName = $this->getName() . '_' . $column->getName() . '_idx';
						$indexCommnd = 'DROP INDEX on ' . $indexName . '(' . $column->getName() . ')';
						
						$this->getDbInstance()->executeCommand($indexCommnd);
					}
					
					if ($column->getPrimaryKey() == true)
					{
						$sequenceName = $this->getName() . '_' . $column->getName() . '_seq';
						$sequenceCommand = 'DROP SEQUENCE ' . $sequenceName;
												
						$this->getDbInstance()->executeCommand($sequenceCommand);
						
//						error_log("Dropping sequence: " . $sequenceName);
					}
//					else
//					{
//						error_log("Primary Key: " . $column->getPrimaryKey());
//					}
				}
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
				
		protected function &createDerivedColum($columnName)
		{
			$column = new MySQLColumn($columnName);
			
			return $column;			
		}

	}
} 
?>