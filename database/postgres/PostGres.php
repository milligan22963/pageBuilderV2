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
		 * @fn createDatabase
		 *
		 * @copydoc IDatabase::createDatabase
		 */
		 /*
-- DROP DATABASE postgres;

CREATE DATABASE postgres
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'C'
    LC_CTYPE = 'C'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

COMMENT ON DATABASE postgres
    IS 'default administrative connection database';

GRANT TEMPORARY, CONNECT ON DATABASE postgres TO PUBLIC;

GRANT ALL ON DATABASE postgres TO postgres;

GRANT CONNECT ON DATABASE postgres TO daniel;		 */
	 	public function createDatabase($databaseName)
	 	{
		 	$success = false;
		 	
		 	$dbConnection = $this->getConnection();
		 	
		 	if ($dbConnection != null)
		 	{
			 	// create command
			 	$createCommand = "CREATE DATABASE " . $databaseName . " WITH OWNER = " . $this->getUserName() . " ENCODING = 'UTF8'";
			 	$createCommand .= " LC_COLLATE = 'C' LC_CTYPE = 'C' CONNECTION LIMIT = -1;";

			 	$success = $this->executeCommand($createCommand);
			 	if ($success == true)
			 	{
			 		$createCommand = "GRANT ALL ON DATABASE " . $databaseName . " TO " . $this->getUserName() . ";";
			 		$success = $this->executeCommand($createCommand);
			 	}
		 	}
		 	else
		 	{
			 	error_log('No active connection');
		 	}
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

	 	protected function &createDerivedTable()
	 	{
		 	$table = new PostGresTable($this);
		 	
		 	return $table;
	 	}
	}
} 
?>