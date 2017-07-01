<?php
/**
 * @module ITable
 *
 * @brief base class for all table interaction
 */

namespace afm
{	
	interface ITable
	{
		/**
		 * @fn setName
		 * 
		 * @brief  set the name for this table, assumes its a full name
		 *         i.e. all prefix, postfix, midfix are included
		 *  
		 * @param $tableName - the full name of the table as it will appear in the database
	 	 */
		public function setName($tableName);
		
		/**
		 * @fn getName
		 * 
		 * @brief  get the full name for this table
		 *
		 * @return the full name of the table
	 	 */
		public function getName();

		/**
		 * @fn setDescription
		 * 
		 * @brief  set the description of this table
		 *
		 * @param $description the description of the table
	 	 */
		public function setDescription($description);

		/**
		 * @fn getDescription
		 * 
		 * @brief  get the description of this table
		 *
		 * @return the description of the table
	 	 */
		public function getDescription();
	
		/**
		 * @fn create
		 * 
		 * @brief  to create a table associated with the given database
		 *			assumes that the database is already authorized etc.
		 *  
		 * @return true if success, false otherwise
	 	 */
		public function create();
		
		/**
		 * @fn drop
		 * 
		 * @brief  to drop a table associated with the given database
		 *			assumes that the database is already authorized etc.
		 *  
		 * @return true if success, false otherwise
	 	 */
		public function drop();

		/**
		 * @fn doesExist
		 * 
		 * @brief  to drop a table associated with the given database
		 *			assumes that the database is already authorized etc.
		 *  
		 * @return bool with true indicating it exists, false otherwise
	 	 */
		public function doesExist();

		/**
		 * @fn createColumn
		 * 
		 * @brief  to create a column to be used in the given table
		 *  
		 * @param $columnName - the name of the column
		 *
		 * @return a new column to be defined prior to create being called
		 *
		 * @note when calling you need to do: $newColumn = &$someTable->createColumn("newcolumn");
		 */
		public function createColumn($columnName, $dataType, $typeLength = null, $typePrecision = null, $allowNull = false, $isIndex = false, $isPrimary = false);

		/**
		 * @brief  returns a column based on the provided name
		 *  
		 * @param $columnName - the name of the column
		 *
		 * @return a the column if it exists
		 */
		public function getColumn($columnName);
		
		/**
		 * @fn load
		 * 
		 * @brief  loads a column from the database into the memory representation
		 *  
		 * @param $columnDetails - the details for a given column
		 */
		public function load($columnDetails);
				
		/**
		 * @fn addRow
		 *
		 * @brief adds a row of data to the table
		 *
		 * @param[in] $rowDetailsArray - the details of the row to add
		 */
		public function addRow($rowDetailsArray);

		/**
		 * @fn deleteRow
		 *
		 * @brief deletes a row of data from the table
		 *
		 * @param[in] $rowDetailsArray - the required details to remove the row
		 */
		public function deleteRow($rowDetailsArray);

		/**
		 * @fn modifyRow
		 *
		 * @brief modifies a row of data in the table
		 *
		 * @param[in] $rowDetailsArray - the required details to modify the row
		 * @param[in] $whereClause - the clause to limit the update to such as id='1'
		 */
		public function modifyRow($rowDetailsArray, $whereClause);

		/**
		 * @fn loadRow
		 *
		 * @brief loads a row from the table
		 *
		 * @param[in] $whereClause - the clause to base the lading on such as id='1'
		 */
		public function loadRow($whereClause = null);
		
		/**
		 * @brief loads a number of rows based on a custom query (See Query.php)
		 *
		 * @param[in] $queryCommand - the query to be executed
		 */
		public function loadRows($queryCommand);

		/**
		 * @fn loadRowsWithLimit
		 *
		 * @brief loads multiple rows form the table with limits and clauses
		 *
		 * @param[in] $whereClause - the clause to base the lading on such as id='1'
		 * @param[in] $startRow - the starting row to load from
		 * @param[in] $rowCount - the number of rows to load
		 */
		public function loadRowsWithLimit($whereClause = null, $startRow = null, $rowCount = null);

		/**
		 * @fn numRows
		 *
		 * @brief determines the number of rows that match a given criteria
		 *
		 * @param[in] $whereClause - the criteria to base the count on
		 *
		 * @return the number of rows matching the criteria
		 */
		 public function numRows($whereClause = null);

		/**
		 * @brief creates a query for the given table
		 *
		 * @return the new query that can be run against this table
		 */
		 public function createQuery();

		 /**
		  * @brief gets that last insertion id for the given
		  *        table
		  *
		  * @return the last id for the item in the table
		  */
		 public function getLastInsertId();
	}
} 
?>