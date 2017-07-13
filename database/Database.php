<?php
/**
 * @module Database
 *
 * @brief base class for all database interaction
 */

namespace afm
{
	// defined for local use
	define('DB_NAME', 'dbname');
	define('DB_PASSWORD', 'password');
	define('DB_USER', 'user');
	define('DB_HOST', 'host');
	define('DB_PREFIX', 'prefix');
	define('DB_TYPE', "db_type");
	
	// query string parsing
	define('TABLE_ENTRY', 'table');
	define('QUESTION_ENTRY', 'question');
	define('WHERE_ENTRY', 'where');

    // Includes
    include_once('IDatabase.php');
    include_once('Table.php');

	class Database implements IDatabase
	{
		private $m_prefix;
		private $m_activeTables;
		private $m_dsnName;
		private $m_dbUserName;
		private $m_dbPassword;
		private $m_hostName;
		private $m_dbName;
		
		private $m_typeModifiers;
		
		private $m_dataTypes;
		
		private $m_dbConnection = null;
		
		public function __construct()
		{
			$this->m_prefix = null;	
			$this->m_activeTables = array();
			$this->m_typeModifiers = array();	
			$this->m_dataTypes = array();
			$this->m_dbName = null;
			
			// load default type modifiers
			$this->addTypeModifier(BOOLEAN_TYPE, "%s");
			$this->addTypeModifier(INTEGER_TYPE, "'%s'");
			$this->addTypeModifier(VARCHAR_TYPE, "'%s'");
			$this->addTypeModifier(CHAR_TYPE, "'%s'");
			$this->addTypeModifier(TIMESTAMP_TYPE, "to_timestamp(%s)");
			$this->addTypeModifier(TIMESTAMP_ZONE_TYPE, "to_timestamp(%s)");
		}
		
		protected function setDSN($dsnName)
		{
			$this->m_dsnName = $dsnName;
		}
		
		/**
		 * @copydoc IDatabase::initialize
		 *
		 * @note - format is 'dsn:dbname=example;user=nobody;password=change_me;host=localhost'
	 	 */
	 	public function initialize($databaseName, $userName, $password, $localHost = LOCAL_HOST)
	 	{
		 	$success = false;

		 	$this->m_dbConnection = null; // close down the old connection
		 	
		 	// connect to the database and then load any tables that are there
		 	$connectionString = $this->m_dsnName . ":" . DB_NAME . "=" . $databaseName . ";" . DB_USER . "=" . $userName . ";" . DB_PASSWORD . "=" . $password;
		 	$connectionString .= ";" . DB_HOST . "=" . $localHost;
		 	try
		 	{
			 	$this->m_dbConnection = new \PDO($connectionString, $userName, $password);
			 	$this->m_dbUserName = $userName;
			 	$this->m_dbPassword = $password;
			 	$this->m_hostName = $localHost;
				$this->m_dbName = $databaseName;
			 	$success = true;
			}
			
			catch (\PDOException $e)
			{
				$this->m_dbConnection = null; // ensure it is cleared
				error_log('Connection failed: ' . $e->getMessage());
			}
		 	return $success;
	 	}

		/**
		 * @copydoc IDatabase::setPrefix
	 	 */
	 	public function setPrefix($prefix)
	 	{
		 	$this->m_prefix = $prefix;
	 	}

		/**
		 * @copydoc IDatabase::getPrefix
	 	 */
	 	public function getPrefix()
	 	{
		 	return $this->m_prefix;
	 	}

	 	/**
		 * @copydoc IDatabase::createDatabase
		 */
	 	public function createDatabase($databaseName, $replace)
	 	{
		 	$success = false;
		 	
		 	if ($this->m_dbConnection != null)
		 	{
				// drop it if there
				if ($replace == true)
				{
					$this->dropDatabase($databaseName, false);
				}

			 	// create command
				$createCommand = $this->getDBCreateStatement($databaseName);
			 	
				 $success = $this->executeCommand($createCommand);
				 if ($success == true)
				 {
					$this->m_dbName = $databaseName;
					$grantCommand = $this->getGrantPrivledgeStatement($databaseName);
					$this->executeCommand($grantCommand);
				 }
		 	}
		 	return $success;
	 	}

	 	/**
		 * @copydoc IDatabase::dropDatabase
		 */
		public function dropDatabase($databaseName, $mustExist)
		{
			$success = false;
			
			$dropCommand = $this->getDBDropStatement($databaseName, $mustExist);

			$success = $this->executeCommand($dropCommand);

			if ($success == true)
			{
				if ($databaseName == $this->m_dbName)
				{
					$this->m_dbName = null;
				}
			}
			else
			{
				error_log('Dropped failed for: ' . $databaseName);
			}

			return $success;
		}

		/**
		 * @copydoc IDatabase::createTable
		 *
		 * @note when calling you need to do: $newTable = &$someDatabase->createTable("newtable");
	 	 */
	 	public function &createTable($name)
	 	{
		 	$table = &$this->createDerivedTable();
		 	
		 	$table->setName($this->m_prefix . $name);
		 	
		 	$this->addTable($table);
		 	
		 	return $table;
	 	}

		/**
		 * @copydoc IDatabase::dropTable
	 	 */
	 	public function dropTable($name)
	 	{
		 	$success = false;
		 	
		 	$tableName = $this->m_prefix . $name;
		 	
		 	foreach ($this->m_activeTables as $table)
		 	{
			 	if ($table->getName() == $tableName)
			 	{
				 	$success = $table->drop();
				 	break;
			 	}
		 	}
		 	
		 	return $success;
	 	}
	 	
	 	/**
		 * @copydoc IDatabase::doesTableExist
		 */
		public function doesTableExist($tableName)
		{
			return false;
		}

	 	/**
		 * @copydoc IDatabase::getTable
		 */
	 	public function &getTable($name)
	 	{
		 	$targetTable = null;
		 	
		 	$tableName = $this->m_prefix . $name;
		 	
//		 	error_log('Looking for: ' . $tableName);
		 	foreach ($this->m_activeTables as $table)
		 	{
//			 	error_log('Checking: ' . $table->getName());
			 	if ($table->getName() == $tableName)
			 	{
				 	$targetTable = $table;
				 	break;
			 	}
		 	}
		 	return $targetTable;
	 	}
	 	
	 	/**
		 * @copydoc IDatabase::loadTable
		 */
		 public function &loadTable($xmlTableFile, $createTable = false)
		 {
			$table = null;
			 
			$systemObject = & System::getInstance();
			
		    $baseDir = $systemObject->getBaseSystemDir();
		
		    // Includes
		    include_once($baseDir . 'configuration/XmlDefines.php');
		    include_once($baseDir . 'page/XmlPage.php');
		    
			 // open the xml file and *create each of the tables
			 // as defined by the file
			 // *with create meaning a memory representation, not actually
			 // create it in the database
			 $xmlTable = XmlPage::withDocument($xmlTableFile);
			 
			 $tableElement = $xmlTable->getElement(TABLE_ELEMENT);
			 
			 if ($tableElement != null)
			 {
				 if ($createTable == true)
				 {
					 // check dependencies
					 $dependencyElement = $tableElement->getElement(DEPENDENCIES_ELEMENT);
					 if ($dependencyElement != null)
					 {
						$dependenciesFound = true;
					 
						$dependencies = $dependencyElement->getElements(DEPENDENCY_ELEMENT);
						foreach ($dependencies as $dependency)
						{
							$requiredTable = $dependency->getAttribute(NAME_ATTR);
							
							// see if this one exists
							if ($this->getTable($requiredTable) == null)
							{
								error_log("Cant find required table: " . $requiredTable);
								$dependenciesFound = false;
								break;
							}
						}
						
						if ($dependenciesFound == false)
						{
							return null; // cant do it right now
						}
					}
				}
				 
				 $name = $tableElement->getAttribute(NAME_ATTR);
				 // version is used to detect upgrades when a table already exists
				 $version = $tableElement->getAttribute(VERSION_ATTR);
				 $columns = $tableElement->getElement(COLUMNS_ELEMENT);
				 
				 //error_log('Name: ' . $name);
				 // the actual create will be done when the developer
				 // calls createTable on the table object.
				 $table = &$this->createTable($name);
				 
				 $table->load($columns);
			 }

			 // do we want to also load the default data at this time or have it loaded later, pass a flag to indicate?
			 // load later by request, if install we may want to load it however the tables may already exist.  If the user wants to replace the table
			 // then we will want to load the data however if they don't then it isn't needed of course this is only during install so does it matter?
			 if (($table != null) && ($createTable == true))
			 {
				 // now we create it
				 $table->create();
				 
				 // look for default data and populate it if it is there
				 $dataSets = $tableElement->getElements(DATASET_ELEMENT);
				 foreach ($dataSets as $dataSet)
				 {
					 $rowData = array();
					 
					 $dataEntries = $dataSet->getElements(DATA_ELEMENT);
					 foreach ($dataEntries as $dataEntry)
					 {
						 $value = $dataEntry->getAttribute(VALUE_ATTR);
						 $type = $dataEntry->getAttribute(TYPE_ATTR);
						 if ($type != null)
						 {
							if ($type != QUERY)
							{
								$value = convertValue($type, $value, $value);
								
								if ($type == BOOLEAN)
								{
									$value = $value ? "true" : "false";
								}
							}
							else
							{
								$value = $this->queryValue($value);
							}
						 }
						 $rowData[$dataEntry->getAttribute(NAME_ATTR)] = $value;
					 }
					 $table->addRow($rowData);
				 }
			 }
			 return  $table;
		 }

	 	/**
		 * @copydoc IDatabase::executeCommand
		 */
	 	public function executeCommand($command)
	 	{
		 	$success = false;
		 	
		 	if ($this->m_dbConnection != null)
		 	{
				// returns number of rows affected, which when maybe 0 when creating a database...
				// so while a statement may not affect any rows, it is still successful
				// of course 0 may look like false and vice versa so do a hard check on FALSE
				$errorValue = $this->m_dbConnection->exec($command);
			 	if (($errorValue >= 0) && ($errorValue !== FALSE))
			 	{
				 	$success = true;
			 	}
				else
				{
					error_log('SQL Execute Error: ' . $errorValue);
				}
		 	}
		 	else
		 	{
			 	error_log('Database::executeCommand - connection invalid');
		 	}
		 	
		 	return $success;
	 	}

	 	/**
		 * @copydoc IDatabase::issueCommand
		 */
	 	public function issueCommand($command)
	 	{
		 	$query = null;
		 			 	
		 	if ($this->m_dbConnection != null)
		 	{
			 	$query = $this->m_dbConnection->prepare($command);
			 	if ($query->execute() == FALSE)
			 	{
				 	error_log("Found nothing: " . $command);
				 	
				 	$query = null;
			 	}
		 	}
		 	else
		 	{
			 	error_log('No db connection?');
		 	}
		 	return $query;
	 	}
	 	
	 	/**
		 * @copydoc IDatabase::getDataType
		 */
	 	public function getDataType($systemType)
	 	{
		 	$databaseType = null;
		 	
		 	if (array_key_exists($systemType, $this->m_dataTypes) == true)
		 	{
			 	$databaseType = $this->m_dataTypes[$systemType];
		 	}
		 	return $databaseType;
	 	}
	 	
	 	/**
		 * @copydoc IDatabase::getTypeModifier
		 */
	 	public function getTypeModifier($dataType)
	 	{
		 	$modifierValue = null;
		 	
		 	if (array_key_exists($dataType, $this->m_typeModifiers) == true)
		 	{
			 	$modifierValue = $this->m_typeModifiers[$dataType];
		 	}
		 	return $modifierValue;
	 	}
	 	
	 	/**
		 * @copydoc IDatabase::getSystemDataType
		 */
	 	public function getSystemDataType($dbSpecificType)
	 	{
		 	$systemType = null;
		 	
		 	foreach ($this->m_dataTypes as $systemTypeId=>$specificType)
		 	{
			 	if ($specificType == $dbSpecificType)
			 	{
				 	$systemType = $systemTypeId;
				 	break;
			 	}
		 	}
		 	
		 	if ($systemType == null)
		 	{
			 	foreach ($this->m_dataTypes as $dataType)
			 	{
				 	error_log('We know about data type: "' . $dataType . '"');
			 	}
		 	}
		 	return $systemType;
	 	}

		/**
		 * @copydoc IDatabase::getSystemTableName
		 */
		public function getSystemTableName()
		{
			return "";
		}


	 	// internal methods

		/**
		 *  @brief allow derived databases to provide a db specific create statement
		 *
		 * @param[in] databaseName - the name of the database to be created
		 *
		 * @return a creation statement in the chosen database's particular idiom
		 */
		protected function getDBCreateStatement($databaseName)
		{
			$createCommand = 'CREATE DATABASE `' . $databaseName . '`;';

			return $createCommand;
		}

		/**
		 * @brief allows derived databases to provide a grant statement on newly created db's
		 *
		 * @param[in] databaseName - the name of the database to receive grant privledges
		 *
		 * @return a grant privledge statement for a newly created database
		 */
		protected function getGrantPrivledgeStatement($databaseName)
		{
			$grantCommand = "GRANT ALL ON `" . $databaseName . "`.* TO '" . $this->m_dbUserName . "'@'" . $this->m_hostName . "';";
			$grantCommand .= "FLUSH PRIVILEGES;";

			return $grantCommand;
		}

		/**
		 * @brief allows derived databases to provide a drop statement
		 *
		 * @param[in] databaseName - the name of the database to be dropped
		 * @param[in] mustExist - true if it must exist, false otherwise
		 *
		 * @return a drop statement in the chosen database's particular idiom
		 */
		protected function getDBDropStatement($databaseName, $mustExist)
		{
			$dropCommand = "DROP DATABASE ";
			if ($mustExist == false)
			{
				$dropCommand .= " IF EXISTS ";
			}
			$dropCommand .= $databaseName;

			return $dropCommand;
		}

	 	protected function getConnection()
	 	{
		 	return $this->m_dbConnection;
	 	}
	 	
	 	protected function getUserName()
	 	{
		 	return $this->m_dbUserName;
	 	}

		protected function getHostName()
		{
			return $this->m_hostName;
		}

		protected function getPassword()
		{
			return $this->m_dbPassword;
		}

		protected function getDatabaseName()
		{
			return $this->m_dbName;
		}
	 	
	 	protected function addTable($table)
	 	{
		 	$this->m_activeTables[] = $table;
	 	}
	 	
	 	protected function &createDerivedTable()
	 	{
		 	$table = new Table($this);
		 	
		 	return $table;
	 	}
	 	
	 	protected function addDataType($systemType, $specificType)
	 	{
		 	$this->m_dataTypes[$systemType] = $specificType;
	 	}
	 	
	 	protected function addTypeModifier($systemType, $modifierValue)
	 	{
		 	$this->m_typeModifiers[$systemType] = $modifierValue;
	 	}
	 	
	 	// derived can override if needed
	 	protected function generateQuery($queryVariables)
	 	{
		 	$additionalEntry = false;
		 	
		 	$queryString = 'select ' . $queryVariables[QUESTION_ENTRY] . ' from ' . $this->getPrefix() . $queryVariables[TABLE_ENTRY] . ' where ';
		 	 
		 	 foreach ($queryVariables[WHERE_ENTRY] as $whereEntry)
		 	 {
			 	 if (strlen($whereEntry) > 0)
			 	 {
				 	 if ($additionalEntry == true)
				 	 {
					 	 $queryString .= ' and ';
				 	 }
				 	 else
				 	 {
					 	 $additionalEntry = true;
				 	 }
				 	 $queryString .= $whereEntry;				 	 
			 	 }
		 	 }
		 	 
		 	 return $queryString;
	 	}
	 	
	 	protected function queryValue($dataString)
	 	{
		 	$value = null;
		 	
			// @ indicates the table name
			// & indicates the constraints
			// ? indicates what we want
			// @user_types&name="admin"?id evaluates to select id from user_types where name="admin"
			// grab the value and see if it references another table
			$queryValues = $this->convertData($dataString);
			
			$queryString = $this->generateQuery($queryValues);
			
//			error_log('Query: ' . $queryString);
			
			$results = $this->issueCommand($queryString);
			
			if ($results != null)
			{
                if ($row = $results->fetch(\PDO::FETCH_LAZY))
                {
	                $value = $row->{$queryValues[QUESTION_ENTRY]};
//	                error_log('Value: ' . $value);
                }				
			}
		 	return $value;
	 	}
	 	
	 	/*
$testString1 = '@user_types&name="admin"?id';
$testString2 = '&name="admin"@user_types?id';
$testString3 = '&name="admin"?id@user_types';
$testString4 = '@user_types?id&name="admin"';
$testString5 = '&name="admin"@user_types&data="someone"?id';
		 */
		protected function convertData($dataString)
		{
			$convertedData = array();
			// since the order may be variable
			$atPos = strpos($dataString, '@');
			$andPos = strpos($dataString, '&'); // can be multiples
			$questPos = strpos($dataString, '?');
			
			if (($atPos !== false) && ($andPos !== false) && ($questPos !== false))
			{
				$numWhereClauses = substr_count($dataString, '&');
				
				if ($numWhereClauses > 1)
				{
					if ($andPos < $atPos)
					{
						$andPos = strpos($dataString, '&', $atPos);
					}
				}
				
				// if @ before & and ?
				if (($atPos < $andPos) && ($atPos < $questPos))
				{
					// front of the string
					if ($andPos < $questPos)
					{
						$convertedData[TABLE_ENTRY] = substr($dataString, $atPos + 1, ($andPos - $atPos) - 1);
					}
					else
					{
						$convertedData[TABLE_ENTRY] = substr($dataString, $atPos + 1, ($questPos - $atPos) - 1);				
					}
				}
				else if (($atPos < $questPos) && ($atPos > $andPos)) // @ after & but before ?
				{
					$convertedData[TABLE_ENTRY] = substr($dataString, $atPos + 1, ($questPos - $atPos) - 1);			
				}
				else if (($atPos > $andPos) && ($atPos > $questPos)) // @ after both & and ?
				{
					$convertedData[TABLE_ENTRY] = substr($dataString, $atPos + 1);
				}
				else
				{
					$convertedData[TABLE_ENTRY] = 'else'; //substr($dataString, $atPos + 1);			
				}
				
				// table name found, remove it
				$dataString = str_replace('@' . $convertedData[TABLE_ENTRY], "", $dataString);
		
				$questPos = strpos($dataString, '?');
				$andPos = strpos($dataString, '&', $questPos);
				
				if ($andPos === false)
				{
					$convertedData[QUESTION_ENTRY] = substr($dataString, $questPos + 1);
				}
				else
				{
					$convertedData[QUESTION_ENTRY] = substr($dataString, $questPos + 1, ($andPos - $questPos) - 1);			
				}
				
				// strip this out
				$dataString = str_replace('?' . $convertedData[QUESTION_ENTRY], "", $dataString);
				
				// now we just have & left
				$convertedData[WHERE_ENTRY] = array_filter(explode('&', $dataString), 'strlen'); // note some of these might be "empty" depending on where the & lies
			}
			else
			{
				$convertedData[] = 'no values';
			}
			
			return $convertedData;
		}
	}
} 
?>