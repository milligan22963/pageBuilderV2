<?php
/**
 * @module MySQL
 *
 * @brief derived class for all mysql database interaction
 */

namespace afm
{
	$systemObject = & System::getInstance();
	
	$baseDir = $systemObject->getBaseSystemDir();
	
    // Includes
	include_once($baseDir . "/system/Toolbox.php");
    include_once($baseDir . 'database/Database.php');
    include_once('MySQLTable.php');
	
	class MYSQLDatabase extends Database
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->setDSN('mysql');
			
			// add our database types
			$this->addDataType(INTEGER_TYPE, "integer");
			$this->addDataType(VARCHAR_TYPE, "varchar");
			$this->addDataType(CHAR_TYPE, "char");
			$this->addDataType(BOOLEAN_TYPE, "boolean");
			$this->addDataType(TIMESTAMP_TYPE, "timestamp");
			$this->addDataType(TIMESTAMP_ZONE_TYPE, "timestamp"); // looks like mysql stores everything as UTC
			
			// use the default type modifiers			
		}

	 	/**
		 * @fn doesTableExist
		 *
		 * @copydoc IDatabase::doesTableExist
		 */
		public function doesTableExist($tableName)
		{
	    	$result = false;
	    	
	        // query the tables in the db and then see if it is there
	        $command = "select exists (SELECT * from information_schema.tables where table_schema='";
			$command .= $this->getDatabaseName() . "' and table_name='"
	            . $this->getPrefix() . $tableName . "');";
	        
	        $results = $this->issueCommand($command);
	        if ($results != null)
	        {
                if ($row = $results->fetch(\PDO::FETCH_LAZY))
                {
	                $result = convertValue(BOOLEAN, $row->exists, false);
                }				
			}
	        return $result;
		}

		/**
		 * @copydoc IDatabase::getSystemTableName
		 */
		public function getSystemTableName()
		{
			return "sys";
		}

	 	protected function &createDerivedTable()
	 	{
		 	$table = new MySQLTable($this);
		 	
		 	return $table;
	 	}

		/**
		 * @copydoc Database::getDBCreateStatement
		 */
		protected function getDBCreateStatement($databaseName)
		{
			$createCommand = "CREATE DATABASE " . $databaseName;

			return $createCommand;
		}

		/**
		 * @copydoc Database::getGrantPrivledgeStatement
		 */
		protected function getGrantPrivledgeStatement($databaseName)
		{
			$grantCommand = "GRANT ALL ON `" . $databaseName . "`.* TO '" . $this->getUserName() . "'@'" . $this->getHostName() . "';";
			$grantCommand .= "FLUSH PRIVILEGES;";

			return $grantCommand;
		}
	}
} 
?>