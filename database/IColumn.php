<?php
/**
 * @module Database
 *
 * @brief base class for all database interaction
 */

namespace afm
{
	interface IColumn
	{
		/**
		 * @fn setName
		 *
		 * @brief sets the name of this column
		 *
		 * @param[in] columnName - the name of the column
		 */
	 	public function setName($columnName);
	 	
		/**
		 * @fn getName
		 *
		 * @brief gets the name of this column
		 *
		 * @return the name of the column
		 */
	 	public function getName();
	 	
		/**
		 * @fn setType
		 *
		 * @brief sets the type of this column
		 *
		 * @param[in] columnType to set for this column
		 */
	 	public function setType($columnType);
	 	
		/**
		 * @fn getType
		 *
		 * @brief gets the type of this column
		 *
		 * @return the type for this column
		 */
	 	public function getType();
	 	
		/**
		 * @fn setTypeLength
		 *
		 * @brief sets the length for the type of this column
		 *
		 * @param[in] typeLength which is the length of this column such as 256 for a varchar etc.
		 */
	 	public function setTypeLength($typeLength);
	 	
		/**
		 * @fn getTypeLength
		 *
		 * @brief gets the length for the type of this column
		 *
		 * @return the length of this column such as 256 for a varchar etc.
		 */
	 	public function getTypeLength();
	 	
		/**
		 * @fn setTypePrecision
		 *
		 * @brief sets the precision of the given type for this column
		 *
		 * @param[in] precision - the precision such as 2 for x.2 etc.
		 */
	 	public function setTypePrecision($precision);
	 	
		/**
		 * @fn getTypePrecision
		 *
		 * @brief returns the precision of the given type for this column
		 *
		 * @return the precision for this column if set, null otherwise
		 */
	 	public function getTypePrecision();

		/**
		 * @fn setAllowNull
		 *
		 * @brief indicates if this column can be null or not
		 *
		 * @param[in] allowNull - where true indicates null is allowed, false otherwise
		 */
	 	public function setAllowNull($allowNull);
	 	
		/**
		 * @fn getAllowNull
		 *
		 * @brief indicates if this column can be null or not
		 *
		 * @return true indicates null is allowed, false otherwise
		 */
	 	public function getAllowNull();
	 	
		/**
		 * @fn setDefaultValue
		 *
		 * @brief sets the default value for the column which will be used opposed to null
		 *
		 * @param[in] defaultValue - the value to insert if none is given
		 */
	 	public function setDefaultValue($defaultValue);
	 	
		/**
		 * @fn getDefaultValue
		 *
		 * @brief gets the default value for the column which will be used opposed to null
		 *
		 * @return the default value to insert if none is given
		 */
	 	public function getDefaultValue();
	 	
		/**
		 * @fn setPrimaryKey
		 *
		 * @brief indicates if this column is a primary key or not, true if it is, false otherwise
		 *
		 * @param[in] primaryKey with true indicating it is a primary key, false otherwise
		 */
	 	public function setPrimaryKey($primaryKey);
		
		/**
		 * @fn getPrimaryKey
		 *
		 * @brief indicates if this column is a primary key or not, true if it is, false otherwise
		 *
		 * @return true indicating it is a primary key, false otherwise
		 */
		public function getPrimaryKey();
		
		/**
		 * @fn setAsIndex
		 *
		 * @brief sets this column to be an index if true, false it will not be
		 *
		 * @param[in] asIndex - true to be an indexed column, false otherwise
		 */
		public function setAsIndex($asIndex);

		/**
		 * @fn isIndex
		 *
		 * @brief gets this column to be an index if true, false it will not be
		 *
		 * @return true to be an indexed column, false otherwise
		 */
		public function isIndex();
		
		/**
		 * @fn setAutoIncrement
		 *
		 * @brief sets this column to be an autoincrement column if true, false otherwise
		 *
		 * @param[in] autoIncrement - true to be an autoincrement column, false otherwise
		 */
		public function setAutoIncrement($autoIncrement);
		
		/**
		 * @fn getAutoIncrement
		 *
		 * @brief gets this column to be an autoincrement column if true, false otherwise
		 *
		 * @return true to be an autoincrement column, false otherwise
		 */
		public function getAutoIncrement();
		
		/**
		 * @fn toString
		 *
		 * @brief converts the column to a string to be used in a create table database call
		 *
		 * @return the string to allow this column to be created in the database
		 */
		public function toString();
	}
}
?>
