<?php
/**
 * @module IAdminOperation
 *
 * @brief interface for admin operation classes
 */
namespace afm
{
	interface IAdminOperation
	{
		/**
		 * @brief initializes the operation
		 *
		 * @param[in] $paramArray - parameters passed into the operation
		 */		 
		public function initialize(& $paramArray);

		/**
		 * @brief processes the operation and returns an indication if
		 *        it has processed the operation or needs to be displayed
		 *
		 * @return a page to display if needed, otherwise null
		 */
		public function process();
		
		/**
		 * @brief used to populate the operation onto
		 *        the page to be rendered if not an ajax call
		 *
		 * @param[in] $parent - the parent elment to populate on...
		 */
		public function populate(& $parent);
	}	
}	
?>