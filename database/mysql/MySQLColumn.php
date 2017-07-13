<?php
/**
 * @module MySQLColumn
 *
 * @brief MySQL specific column definition
 */

namespace afm
{
	$systemObject = & System::getInstance();
	
	$baseDir = $systemObject->getBaseSystemDir();

    // Includes
    include_once($baseDir . 'database/Column.php');

	class MySQLColumn extends Column
	{		
		/**
		 * @copydoc IColumn::toString
		 */
		public function toString()
		{
			$columnData = $this->getName() . ' ' . $this->getType();
			
			if ($this->getTypeLength() != null)
			{
				$columnData .= '(' . $this->getTypeLength();
				if ($this->getTypePrecision() != null)
				{
					$columnData .= ',' . $this->getTypePrecision();
				}
				$columnData .= ')';
			}
			
			// set if primary key, implicit that it is not null so don't include that
			// in addition
			if ($this->getPrimaryKey() == true)
			{
				$columnData .= ' PRIMARY KEY ';
			}
			else if ($this->getAllowNull() == false)
			{
				$columnData .= ' NOT NULL ';
			}
			
			if ($this->getAutoIncrement() == true)
			{
				$columnData .= " AUTO_INCREMENT ";
			}
			else if ($this->getDefaultValue() != null)
			{
				$columnData .= " DEFAULT " . $this->getDefaultValue();
			}
						
			return $columnData;
		}
	}
}
?>
