<?php
/**
 * @module Database
 *
 * @brief base class for all database interaction
 */

namespace afm
{
	define('POSTGRES', 1);
	define('MYSQL', 2);
	define('LOCAL_HOST', 'localhost');
	
	// System database type defines for the system which
	// will be mapped by the target database implementation
	define('INTEGER_TYPE', "INTEGER_TYPE");
	define('VARCHAR_TYPE', "VARCHAR_TYPE");
	define('CHAR_TYPE', "CHAR_TYPE");
	define('BOOLEAN_TYPE', "BOOLEAN_TYPE");
	define('TIMESTAMP_TYPE', "TIMESTAMP_TYPE");
	define('TIMESTAMP_ZONE_TYPE', "TIMESTAMP_ZONE_TYPE");


	interface IDatabase
	{
		/**
		 * @fn initialize
		 * 
		 * @brief  initializes the database and loads any existing tables
		 *  
		 * @param $databaseName - the name of the database to utilize associated with the user
		 *				and password
		 * @param $userName - the user name for the database
		 * @param $password - the password for the user name for the database
		 *
		 * @return true on success, false otherwise
	 	 */
	 	public function initialize($databaseName, $userName, $password, $localHost = LOCAL_HOST);
	 	
		/**
		 * @fn setPrefix
		 * 
		 * @brief  set the preix to be used with all tables
		 *  
		 * @param $prefix - the prefix to be appended to a table
	 	 */
	 	public function setPrefix($prefix);

		/**
		 * @fn getPrefix
		 * 
		 * @brief  get the preix to be used with all tables
		 *  
		 * @return the prefix to be appended to a table
	 	 */
	 	public function getPrefix();

	 	/**
		 * @fn createDatabase
		 *
		 * @brief called to create a database for the given user
		 *
		 * @param[in] $databaseName - the name of the database to create
		 *
		 * @return true on success, false otherwise
		 */
	 	public function createDatabase($databaseName);
	 	
		/**
		 * @fn createTable
		 * 
		 * @brief  creates a table of the given name with the stored
		 *         prefix.
		 *  
		 * @param $name - the base name of the table to be created
		 *
		 * @return a table instance for the current database schema that
		 *         the caller can add columns to and then actually create in the db
		 *
		 * @note when calling you need to do: $newTable = &$someDatabase->createTable("newtable");
	 	 */
	 	public function &createTable($name);

		/**
		 * @fn dropTable
		 * 
		 * @brief  drops a table of the given name with the stored
		 *         prefix.
		 *  
		 * @param $name - the base name of the table to be dropped
		 *
		 * @return true on success, false otherwise
	 	 */
	 	public function dropTable($name);
	 	
	 	/**
		 * @fn doesTableExist
		 *
		 * @param[in] $name - the table to check
		 *
		 * @return true if it exists, false otherwise
		 */
		public function doesTableExist($tableName);
		 
	 	/**
		 * @fn getTable
		 *
		 * @brief returns a partiuclar table if it exists
		 *
		 * @param[in] $name - the name of the table to retrieve
		 *
		 * @return the table in question otherwise null
		 */
	 	public function &getTable($name);
	 	
	 	/**
		 * @fn loadTable
		 *
		 * @brief loads the table contained in the specific xml data file
		 *
		 * @param[in] $xmlTableFile - the xml file containing the table specifications
		 * @param[in] $createTable - true to load create the table and the default data if any for the table, otherwise skip it
		 *
		 * @return a reference to the new table
		 *
		 * @note when calling you need to do: $newTable = &$someDatabase->loadTable("mytable.xml", false);
		 */
		 public function &loadTable($xmlTableFile, $createTable = false);
	 	
	 	/**
		 * @fn executeCommand
		 *
		 * @brief executes a command on the associated database
		 *
		 * @param[in] $command - the command to process/execute
		 *
		 * @return true on success, false otherwise
		 */
	 	public function executeCommand($command);
	 	
	 	/**
		 * @fn issueCommand
		 *
		 * @brief issues a command on the associated database
		 *
		 * @param[in] $command - the command to issue
		 *
		 * @return query object on success, false otherwise
		 */
	 	public function issueCommand($command);
	 	
	 	/**
		 * @fn getDataType
		 *
		 * @brief gets the database specific type for a given system type
		 *
		 * @param[in] the system type of database data type
		 *
		 * @return the database specific type
		 */
	 	public function getDataType($systemType);
	 	
	 	/**
		 * @fn getTypeModifier
		 *
		 * @brief gets the database specific type modifier for a given system type
		 *
		 * @param[in] the system type modifier for the database data type
		 *
		 * @return the database specific type modifier
		 */
	 	public function getTypeModifier($dataType);
	 	
	 	/**
		 * @fn getSystemDataType
		 *
		 * @brief gets the generic system type for a database specific type
		 *
		 * @param[in] the database specific type
		 *
		 * @return the generic system type for the given specific type
		 */
	 	public function getSystemDataType($dbSpecificType);
	}
} 
?>