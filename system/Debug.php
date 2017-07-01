<?php
/**
 * @module Debug
 *
 * @brief tools for debugging
 */
namespace afm
{
	/**
	 * @fn installErrorHandler
	 *
	 * @brief installs our error handler for debugging purposes
	 */
	function installErrorHandler()
	{
		error_reporting(E_ALL); 
		$old_error_handler = set_error_handler("userErrorHandler");
	}

	/**
	 * @fn userErrorHandler
	 *
	 * @brief handles errors for the system
	 *
	 * @param[in] $errno - the error number
	 * @param[in] $errmsg - the error message
	 * @param[in] $filename - the name of the file where it occurred
	 * @param[in] $linenum - the line number where the issue occurred
	 * @param[in] $vars - the variable arguments
	 */
	function userErrorHandler ($errno, $errmsg, $filename, $linenum,  $vars) 
	{
	     $time=date("d M Y H:i:s"); 
	     // Get the error type from the error number 
	     $errortype = array (1    => "Error",
	                         2    => "Warning",
	                         4    => "Parsing Error",
	                         8    => "Notice",
	                         16   => "Core Error",
	                         32   => "Core Warning",
	                         64   => "Compile Error",
	                         128  => "Compile Warning",
	                         256  => "User Error",
	                         512  => "User Warning",
	                         1024 => "User Notice");
	      $errlevel=$errortype[$errno];
	            
	      $errfile=fopen("errors.csv","a"); 
	      fputs($errfile,"\"$time\",\"$filename . ':' . $linenum\",\"($errlevel) $errmsg\"\r\n"); 
	      fclose($errfile); 
	      
	      return false;
	}
}	
?>