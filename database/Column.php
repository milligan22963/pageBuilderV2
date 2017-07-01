<?php
/**
 * @module Column
 *
 * @brief base class for all columns
 */

namespace afm
{
    // Includes
    include_once('IColumn.php');

	class Column implements IColumn
	{
	 	private $m_name = "none";
	 	private $m_type = null;
	 	private $m_typeLength = null;
	 	private $m_typePrecision = null;
	 	private $m_allowNull = false;
	 	private $m_primaryKey = false;
	 	private $m_autoIncrement = false;
	 	private $m_defaultValue = null;
	 	private $m_isIndex = false;
	 	
	 	function __construct($columnName)
	 	{
	 		$this->setName($columnName);
	 	}

		/**
		 * @fn setName
		 *
		 * @copydoc IColumn::setName
		 */
	 	public function setName($columnName)
	 	{
	 		$this->m_name = $columnName;
	 	}
	 	
		/**
		 * @fn getName
		 *
		 * @copydoc IColumn::getName
		 */
	 	public function getName()
	 	{
	 		return $this->m_name;
	 	}
	 	
		/**
		 * @fn setType
		 *
		 * @copydoc IColumn::setType
		 */
	 	public function setType($columnType)
	 	{
	 		$this->m_type = $columnType;
	 	}
	 	
		/**
		 * @fn getType
		 *
		 * @copydoc IColumn::getType
		 */
	 	public function getType()
	 	{
	 		return $this->m_type;
	 	}
	 	
		/**
		 * @fn setTypeLength
		 *
		 * @copydoc IColumn::setTypeLength
		 */
	 	public function setTypeLength($typeLength)
	 	{
	 		$this->m_typeLength = $typeLength;
	 	}
	 	
		/**
		 * @fn getTypeLength
		 *
		 * @copydoc IColumn::getTypeLength
		 */
	 	public function getTypeLength()
	 	{
	 		return $this->m_typeLength;
	 	}
	 	
		/**
		 * @fn setTypePrecision
		 *
		 * @copydoc IColumn::setTypePrecision
		 */
	 	public function setTypePrecision($precision)
	 	{
	 		$this->m_typePrecision = $precision; 		
	 	}
	 	
		/**
		 * @fn getTypePrecision
		 *
		 * @copydoc IColumn::getTypePrecision
		 */
	 	public function getTypePrecision()
	 	{
	 		return $this->m_typePrecision;
	 	}

		/**
		 * @fn setAllowNull
		 *
		 * @copydoc IColumn::setAllowNull
		 */
	 	public function setAllowNull($allowNull)
	 	{
	 		$this->m_allowNull = $allowNull;
	 	}
	 	
		/**
		 * @fn getAllowNull
		 *
		 * @copydoc IColumn::getAllowNull
		 */
	 	public function getAllowNull()
	 	{
	 		return $this->m_allowNull;
	 	}
	 	
		/**
		 * @fn setDefaultValue
		 *
		 * @copydoc IColumn::setDefaultValue
		 */
	 	public function setDefaultValue($defaultValue)
	 	{
	 		$this->m_defaultValue = $defaultValue;
	 	}
	 	
		/**
		 * @fn getDefaultValue
		 *
		 * @copydoc IColumn::getDefaultValue
		 */
	 	public function getDefaultValue()
	 	{
	 		return $this->m_defaultValue;
	 	}
	 	
		/**
		 * @fn setPrimaryKey
		 *
		 * @copydoc IColumn::setPrimaryKey
		 */
	 	public function setPrimaryKey($primaryKey)
		{
			$this->m_primaryKey = $primaryKey;
			$this->m_autoIncrement = $primaryKey;
		}
		
		/**
		 * @fn getPrimaryKey
		 *
		 * @copydoc IColumn::getPrimaryKey
		 */
		public function getPrimaryKey()
		{
			return $this->m_primaryKey;
		}

		/**
		 * @fn setAsIndex
		 *
		 * @copydoc IColumn::setAsIndex
		 */
		public function setAsIndex($asIndex)
		{
			$this->m_isIndex = $asIndex;
		}

		/**
		 * @fn isIndex
		 *
		 * @copydoc IColumn::isIndex
		 */
		public function isIndex()
		{
			return $this->m_isIndex;
		}
		
		/**
		 * @fn setAutoIncrement
		 *
		 * @copydoc IColumn::setAutoIncrement
		 */
		public function setAutoIncrement($autoIncrement)
		{
			$this->m_autoIncrement = $autoIncrement;
		}
		
		/**
		 * @fn getAutoIncrement
		 *
		 * @copydoc IColumn::getAutoIncrement
		 */
		public function getAutoIncrement()
		{
			return $this->m_autoIncrement;
		}
		
		/**
		 * @fn toString
		 *
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
			
			//
			if ($this->getPrimaryKey() == true)
			{
				$columnData .= ' PRIMARY KEY ';
			}
			
			if ($this->getAllowNull() == false)
			{
				$columnData .= ' NOT NULL';
			}
			
			if ($this->getAutoIncrement() == true)
			{
				
			}
			
			return $columnData;
		}
	}
}
?>
