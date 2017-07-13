<?php
/**
 * @module IConfiguration
 *
 * @brief general system information
 */
namespace afm
{
	interface IConfiguration
	{
		public function set($name, $value);
		
		public function get($name, $default=null);
		
		public function setFileName($fileName);
		
		public function getFileName();
		
		public function loadFile($fileName = null);
		
		public function saveFile($fileName = null);
		
		public function render();
	}	
}	
?>