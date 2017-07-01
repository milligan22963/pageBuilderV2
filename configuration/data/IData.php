<?php
/**
 * @module IData
 *
 * @brief interface data class for the system tables
 */
 namespace afm
 {
	interface IData 
	{
		/**
		 * @fn load
		 *
		 * @brief loads the given item by the specififed id
		 *
		 * @param[in] $id to be loaded
		 */
		public function load($id);
		
		/**
		 * @fn save
		 *
		 * @brief saves the given item
		 *
		 */
		public function save();
		
		/**
		 * @fn setId
		 *
		 * @brief sets the id for this instance based on the data
		 *        base assigned id
		 *
		 * @param[in] $id is the id to be assigned to this class instance
		*/
		public function setId($id);
		 
		/**
		 * @fn getId
		 *
		 * @brief returns the database assigned id
		 *
		 * @return the database assigned id
		 */
		public function getId();
		
		/**
		 * @fn setActive
		 *
		 * @brief sets the item as active
		 *
		 * @param[in] $isActive with true indicates
		 *              the item is active, false
		 *              otherwise
		 */
		public function setActive($isActive);
		
		/**
		 * @fn getActive
		 *
		 * @brief returns the current state of the object
		 *
		 * @return the active state of the object, true active,
		 *         false otherwise
		 */
		public function getActive();
		
		/**
		 * @fn setTimestamp
		 *
		 * @brief sets the time the entry was created in the database
		 *
		 * @param[in] the time when the entry was createe in the database
		 */
		public function setTimestamp($timeStamp);
		
		/**
		 * @fn getTimestamp
		 *
		 * @brief returns the time when the entry was created in the database
		 *
		 * @return the time when the entry was created in the database
		 */
		public function getTimestamp();
	}
}
?>
