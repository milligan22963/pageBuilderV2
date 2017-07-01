<?php
/**
 * @module Toolbox
 *
 * @brief tools for the toolbox
 */
namespace afm
{
	define('BOOLEAN', "BOOLEAN");
	define('INTEGER', "INTEGER");
	define('FLOAT', "FLOAT");
	define('STRING', "STRING");
	define('QUERY', "QUERY");
	define('EMAIL', "EMAIL");
	define('SALT', "SALT");
	define('RELATIVE_PATH', "RELATIVE_PATH");
	define('ABSOLUTE_PATH', "ABSOLUTE_PATH");
	define('PASSWORD', "PASSWORD");
	define('INVALID', "INVALID");

	/**
	 * @fn cleanseData
	 *
	 * @brief cleanse incoming data from the user
	 *
	 * @param[in] $rawData - data received from the user
	 *
	 * @return cleansed data
	 */
	function cleanseData($rawData)
	{
		$cleansedData = trim($rawData);
		$cleansedData = stripslashes($cleansedData);
		$cleansedData = htmlspecialchars($cleansedData, ENT_QUOTES);
		
		return $cleansedData;
	}

	function cleanseEmail($emailData)
	{
		$cleansedData = cleanseData($emailData);

		$cleansedData = filter_var($cleansedData, FILTER_SANITIZE_EMAIL);
		if ($cleansedData !== FALSE)
		{
			$cleansedData = filter_var($cleansedData, FILTER_VALIDATE_EMAIL);
		}
		
		return $cleansedData;
	}

	/**
	 * @fn isPageIncluded
	 *
	 * @brief checks to see if a page is included or is the executed script
	 *
	 * @param[in] $page - the page to check
	 *
	 * @return true if it is included, false otherwise
	 */
	function isPageIncluded($page)
	{
		$thePage = basename($page);
		$thisPage = htmlspecialchars(basename($_SERVER['SCRIPT_FILENAME']));
		
		return strcasecmp($thePage, $thisPage) != 0;
	}
	
	function getDirectoryList($path)
	{
		$directories = array();
		if (is_dir($path) == true)
		{
			if ($directoryHandle = opendir($path))
			{
				$path .= '/';
				while (($file = readdir($directoryHandle)) !== false)
				{
					if (is_dir($path . $file) == true)
					{
						if (($file != '.') && ($file != '..'))
						{
							$directories[] = $file;
						}
					}
				}
				closedir($directoryHandle);
			}
		}

		return $directories;
	}

	function getFileList($path, $name="*", $extension="*", $recursive = false)
	{
		$fileSet = array();
		
		// make sure it ends in a '/'
		if (substr($path, -1) != "/")
		{
			$path .= "/";
		}

		$checkFile = $name != "*" ? true : false;
		$checkExtension = $extension != "*" ? true : false;
		
		if (is_dir($path) == true)
		{
			if ($directoryHandle = opendir($path))
			{
				while (($file = readdir($directoryHandle)) !== false)
				{
					if (is_dir($file) == false)
					{
						$addIt = true;
						
						if ($checkFile == true)
						{
							if (strpos($file, $name) == -1)
							{
								$addIt = false;
							}
						}
						
						if ($checkExtension == true)
						{
							if (substr($file, strlen($file) - strlen($extension)) != $extension)
							{
								$addIt = false;
							}						
						}
						if ($addIt == true)
						{
							$fileSet[] = $path . $file;
						}
					}
					else if ($recursive == true)
					{
						if (($file != '.') && ($file != '..'))
						{
							$fileSet = array_merge($fileSet, getFileList($path . $file, $name, $extension, $recursive));
						}
					}
				}
				closedir($directoryHandle);
			}
		}
		return $fileSet;
	}
	
	function randomToken($length = 32)
	{
		$token = null;
		
	    if (!isset($length) || intval($length) <= 8 )
	    {
	      $length = 32;
	    }
	    
	    if (\function_exists('random_bytes'))
	    {
	        $token = \bin2hex(\random_bytes($length));
	    }
	    else if (\function_exists('mcrypt_create_iv'))
	    {
	        $token = \bin2hex(\mcrypt_create_iv($length, MCRYPT_DEV_URANDOM));
	    }
	    else if (\function_exists('openssl_random_pseudo_bytes'))
	    {
	        $token = \bin2hex(\openssl_random_pseudo_bytes($length));
	    }
	    
	    return $token;
	}
		
	/**
	 * @fn convertValue
	 *
	 * @brief converts a value to a specific type if it can, returns the default if it cant
	 *
	 * @param[in] $targetType as defined above
	 * @param[in] $sourceValue - the value to be converted
	 * @param[in] $default - the default value to use when conversion fails
	 *
	 * @return the value converted or the default value
	 */
	function convertValue($targetType, $sourceValue, $default)
	{
		$convertedValue = $default;
		
		switch ($targetType)
		{
			case BOOLEAN:
			{
				if (\strcasecmp($sourceValue, "false") == 0)
				{
					$convertedValue = false;
				}
				else
				{
					$convertedValue = true;
				}
			}
			break;
			case INTEGER:
			{
				if (($sourceValue != null) && (\strcasecmp($sourceValue, "null") != 0))
				{
					$convertedValue = \intval($sourceValue);
				}
			}
			break;
			case FLOAT:
			{
				if (($sourceValue != null) && (\strcasecmp($sourceValue, "null") != 0))
				{
					$convertedValue = \floatval($sourceValue);
				}				
			}
			break;
			case STRING:
			{
				// so we can take the default value for a true null if they typed in null
				if (($sourceValue != null) && (\strcasecmp($sourceValue, "null") != 0))
				{
					$convertedValue = $sourceValue;
				}
			}
			break;
			case QUERY:
			{
				// right now being handled within database
			}
			break;
			case EMAIL:
			{
				if (\filter_var($sourceValue, FILTER_VALIDATE_EMAIL) == true)
				{
					$convertedValue = $sourceValue;
				}
			}
			break;
			case SALT:
			{
				// generate a salt for this entry
				$convertedValue = randomToken(32);
					
			}
			break;
			case RELATIVE_PATH:
			{
			}
			break;
			case ABSOLUTE_PATH:
			{
			}
			break;
			case PASSWORD:
			{
				$convertedValue = \password_hash($sourceValue, PASSWORD_DEFAULT);
			}
			break;
		}
		
		return $convertedValue;
	}
}
?>