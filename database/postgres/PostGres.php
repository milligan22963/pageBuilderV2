<?php
/**
 * @module PostGRES
 *
 * @brief base class for all database interaction
 */

namespace afm
{
	$systemObject = & System::getInstance();
	
	$baseDir = $systemObject->getBaseSystemDir();
	
    // Includes
	include_once($baseDir . "/system/Toolbox.php");
    include_once($baseDir . 'database/Database.php');
    include_once('PostGresTable.php');
	
	class PGSQLDatabase extends Database
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->setDSN('pgsql');
			
			// add our database types
			$this->addDataType(INTEGER_TYPE, "integer");
			$this->addDataType(VARCHAR_TYPE, "varchar");
			$this->addDataType(CHAR_TYPE, "char");
			$this->addDataType(BOOLEAN_TYPE, "boolean");
			$this->addDataType(TIMESTAMP_TYPE, "timestamp");
			$this->addDataType(TIMESTAMP_ZONE_TYPE, "timestamp with time zone");
			
			// use the default type modifiers			
		}

	 	/**
		 * @copydoc IDatabase::createDatabase
		 */
	 	public function createDatabase($databaseName, $replace)
	 	{
		 	$success = parent::createDatabase($databaseName, $replace);
		 	
		 	if ($success == true)
		 	{
				 $this->initialize($databaseName, $this->getUserName(), $this->getPassword(), $this->getHostName());
		 	}
		 	return $success;
	 	}

		/**
		 * @copydoc IDatabase::dropDatabase
		 */
		public function dropDatabase($databaseName, $mustExist)
		{
			$success = false;

			// we need to drop the current connection and reconnect to the system table
			// then drop the current database
			$this->initialize($this->getSystemTableName(), $this->getUserName(), $this->getPassword(), $this->getHostName());

			$success = parent::dropDatabase($databaseName, $mustExist);
			
			return $success;
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
	        //select table_name from information_schema.tables where table_schema = 'public';
	        $command = "select exists (SELECT * from information_schema.tables where table_schema='public' and table_name='"
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
			return "postgres";
		}

	 	protected function &createDerivedTable()
	 	{
		 	$table = new PostGresTable($this);
		 	
		 	return $table;
	 	}

		/**
		 * @copydoc Database::getDBCreateStatement
		 */
		protected function getDBCreateStatement($databaseName)
		{
			$createCommand = "CREATE DATABASE " . $databaseName . " WITH OWNER = " . $this->getUserName() . " ENCODING = 'UTF8'";
			$createCommand .= " LC_COLLATE = 'C' LC_CTYPE = 'C' CONNECTION LIMIT = -1;";

			return $createCommand;
		}

		/**
		 * @copydoc Database::getGrantPrivledgeStatement
		 */
		protected function getGrantPrivledgeStatement($databaseName)
		{
			$grantCommand = "GRANT ALL ON DATABASE " . $databaseName . " TO " . $this->getUserName() . ";";

			return $grantCommand;
		}
	}
} 
?>