<?php
/**
 * @module Query
 *
 * @brief base class for all table queries
 */

namespace afm
{
    include_once('IQuery.php');

    class JoinInformation
    {
        private $m_joinTableId;
        private $m_joinTable;
        private $m_joinColumn;
        private $m_targetTableId;
        private $m_targetTable;
        private $m_targetColumn;
        private $m_queryColumns;

        public function __construct()
        {
            $this->m_joinTable = null;
        }

        public function setJoinInformation($tableId, & $table, $column)
        {
            $this->m_joinTableId = $tableId;
            $this->m_joinTable = $table;
            $this->m_joinColumn = $column;
        }

        public function setTargetInformation($targetTableId, & $targetTable, $targetColumn)
        {
            $this->m_targetTableId = $targetTableId;
            $this->m_targetTable = $targetTable;
            $this->m_targetColumn = $targetColumn;
        }

        public function getTableId()
        {
            return $this->m_joinTableId;
        }

        public function getName()
        {
            $tableName = null;
            if ($this->m_joinTable != null)
            {
                $tableName = $this->m_joinTable->getName();
            }

            return $tableName;
        }

        public function addQueryColumn($queryColumn)
        {

        }

        public function toQuery()
        {
            $queryString = " JOIN " . $this->m_joinTable->getName() . " as " . $this->m_joinTableId;
            $queryString .= " on ";
            $queryString .= $this->m_targetTableId . "." . $this->m_targetColumn . ' = ' . $this->m_joinTableId . "." . $this->m_joinColumn;

            return $queryString;
        }
    }

	class Query implements IQuery
	{
        private $m_nextJoinTableId = 2;
        private $m_targetTableId = null;
        private $m_targetTable = null;
        private $m_joinTables = null;
        private $m_columns = null;
        private $m_rowOffset = 0;
        private $m_limit = 0;
        private $m_whereClauses = null;
        private $m_orderClauses = null;

        public function __construct($targetTable)
        {
            $this->m_joinTables = array();
            $this->m_columns = array();
            $this->m_whereClauses = array();
            $this->m_orderClauses = array();

            $this->m_targetTable = $targetTable;
        }

		/**
		 * @copydoc IQuery::setRowOffset
		 */
        public function setRowOffset($offset)
        {
            $this->m_rowOffset = intval($offset);
        }

		/**
		 * @copydoc IQuery::setLimit
		 */
        public function setLimit($numRows)
        {
            $this->m_limit = intval($numRows);
        }

		/**
		 * @copydoc IQuery::addJoin
		 */
        public function addJoin(& $foreignTable, $foreignColumn, $localColumn)
        {
            if ($this->m_targetTableId == null)
            {
                $this->m_targetTableId = "T1";
            }
            $joinTableId = "T" . $this->m_nextJoinTableId++;

            $joinInformation = new JoinInformation();

            $joinInformation->setJoinInformation($joinTableId, $foreignTable, $foreignColumn);

            $joinInformation->setTargetInformation($this->m_targetTableId, $this->m_targetTable, $localColumn);

            $this->m_joinTables[$foreignTable->getName()] = $joinInformation;
        }

		/**
		 * @copydoc IQuery::addQueryColumn
		 */
		public function addQueryColumn($columnName, & $table = null)
        {
            if ($table != null)
            {
                $added = false;

                $targetName = $table->getName();

                foreach ($this->m_joinTables as $table)
                {
                    if ($table->getName() == $targetName)
                    {
                        $table->addQueryColumn($columnName);
                        $added = true;
                    }
                }

                if (($added == false) && ($this->m_targetTable->getName() == $targetName))
                {
                    $this->m_columns[] = $columnName;
                }
            }
            else
            {
                $this->m_columns[] = $columnName;
            }
        }

		/**
		 * @copydoc IQuery::addWhereClause
		 */
		public function addWhereClause($whereClause, & $table = null)
        {
            $targetId = "T1";

            // each where clause needs to be organized by table
            if ($table != null)
            {
                // find this one in joins
                foreach ($this->m_joinTables as $tableName=>$info)
                {
                    if ($tableName == $table->getName())
                    {
                        $targetId = $info->getTableId();
                    }
                } 
            }

            if (array_key_exists($targetId, $this->m_whereClauses) == false)
            {
                $this->m_whereClauses[$targetId] = array(); // array of arrays
            }

            $this->m_whereClauses[$targetId][] = $whereClause;
        }

		/**
		 * @copydoc IQuery::addOrderClause
		 */
		public function addOrderClause($orderClause, & $table = null)
        {
            $targetId = "T1";

            // each where clause needs to be organized by table
            if ($table != null)
            {
                // find this one in joins
                foreach ($this->m_joinTables as $tableName=>$info)
                {
                    if ($tableName == $table->getName())
                    {
                        $targetId = $info->getTableId();
                    }
                } 
            }

            if (array_key_exists($targetId, $this->m_orderClauses) == false)
            {
                $this->m_orderClauses[$targetId] = array(); // array of arrays
            }

            $this->m_orderClauses[$targetId][] = $orderClause;
        }

		/**
		 * @copydoc IQuery::execute
		 */
		public function execute()
        {
            $dbObjects = null;

            $queryString = $this->toQuery();

            $results = $this->m_targetTable->loadRows($queryString);
            if ($results != null)
            {
                $dbObjects = $results->fetchAll(\PDO::FETCH_OBJ);
                $results = null;
            }
            return $dbObjects;
        }

        // internal
        protected function toQuery()
        {
            $queryString = "select ";

            $totalColumns = count($this->m_columns);
            if ($totalColumns > 0)
            {
                $columnIndex = 0;
                foreach ($this->m_columns as $columnName)
                {
                    if ($this->m_targetTableId != null)
                    {
                        $queryString .= $this->m_targetTableId . '.';
                    }
                    $queryString .= $columnName;
                    $columnIndex++;

                    // any more coming?
                    if ($columnIndex < $totalColumns)
                    {
                        $queryString .= ", ";
                    }
                }
            }
            else
            {
                $queryString .= " * ";
            }

            $queryString .= " from ";

            $queryString .= $this->m_targetTable->getName();
            if ($this->m_targetTableId != null)
            {
                $queryString .= ' as ' .  $this->m_targetTableId;
            }

            $hasJoins = false;

            // any joins?
            if (count($this->m_joinTables) > 0)
            {
                $hasJoins = true;
                foreach ($this->m_joinTables as $joinTableInfo)
                {
                    $queryString .= $joinTableInfo->toQuery();
                }
            }

            // where clauses
            if (count($this->m_whereClauses) > 0)
            {
                $queryString .= " where ";
                foreach ($this->m_whereClauses as $tableId=>$clauses)
                {
                    $clauseCount = 0;
                    $totalClauses = count($clauses);
                    foreach ($clauses as $clause)
                    {
                        if ($hasJoins == true)
                        {
                            $queryString .= $tableId . ".";
                        }
                        $queryString .= $clause;
                        $clauseCount++;
                        if ($clauseCount < $totalClauses)
                        {
                            $queryString .= " and ";
                        }
                    }
                }
            }

            // order clauses
            if (count($this->m_orderClauses) > 0)
            {
                $queryString .= " order by ";
                foreach ($this->m_orderClauses as $tableId=>$clauses)
                {
                    $clauseCount = 0;
                    $totalClauses = count($clauses);
                    foreach ($clauses as $clause)
                    {
                        if ($hasJoins == true)
                        {
                            $queryString .= $tableId . ".";
                        }
                        $queryString .= $clause;
                        $clauseCount++;
                        if ($clauseCount < $totalClauses)
                        {
                            $queryString .= " and ";
                        }
                    }
                }
            }

            if ($this->m_rowOffset > 0)
            {
                $queryString .= " offset " . $this->m_rowOffset;
            }

            if ($this->m_limit > 0)
            {
                $queryString .= " limit " . $this->m_limit;
            }

            return $queryString;
        }
	}
} 
?>