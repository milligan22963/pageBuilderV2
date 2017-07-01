<?php
/**
 * @module IQuery
 *
 * @brief interface class for all table queries
 */

namespace afm
{	
	interface IQuery
	{
		/**
		 * @brief sets the starting row offset
		 *  
		 * @param[in] $offset - the offset to start at when returning data
		 */
        public function setRowOffset($offset);

		/**
		 * @brief sets the desired number of rows to return
		 *  
		 * @param[in] $numRows - the max number of rows to return
		 */
        public function setLimit($numRows);

		/**
		 * @brief adds a table to join with when return results
		 *  
		 * @param[in] $foreignTable - the foreign table to join with
		 * @param[in] $foreignColumn - the foreign table column to join on
		 * @param[in] $localColumn - the local table column to join with
		 */
        public function addJoin(& $foreignTable, $foreignColumn, $localColumn);

		/**
		 * @brief  adds a column to be queried and the target table if refrencing a join
		 * 
		 * @param[in] $columnName - the name of the column to be queried
		 * @param[in] $table - the table to query on, if null then the target table
		 */
		public function addQueryColumn($columnName, & $table = null);

		/**
		 * @brief  adds a where clause to specify what to return
		 * 
		 * @param[in] $whereClause - the clause to utilize such as id="1"
		 * @param[in] $table - the table to base the where clause on, if null then the target table
		 */
		public function addWhereClause($whereClause, & $table = null);

		/**
		 * @brief  adds a ordering clause
		 * 
		 * @param[in] $orderClause - the clause to utilize such as order by time_stamp asc etc
		 * @param[in] $table - the table to base the where clause on, if null then the target table
		 */
		public function addOrderClause($orderClause, & $table = null);

		/**
		 * @brief issues the query to the associated table and returns the results
		 *  
		 * @return the results of the query
		 */
		public function execute();
	}
} 
?>