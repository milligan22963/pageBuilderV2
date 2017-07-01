<?php
/**
 * @module IExtensions
 *
 * @brief extension interface for all extensions to implement
 */
namespace afm
{
	// see extension_types table for options

	interface IExtension
	{
		/**
		 * @fn initialize
		 *
		 * @brief initializes the extension, the extension can check the page type to
		 *        load/select what is needed in that regard
		 *
		 * @param[in] $relativeExtensionPath - relative path to the extension
		 * @param[in] $extensionSettings - settings for the extension (from xml file)
		 */
		public function initialize($relativeExtensionPath, $extensionSettings);

		/**
		 * @brief sets the name of the extension
		 *
		 * @param[in] $name - the name of the extension
		 */
		public function setName($name);

		public function getName();

		/**
		 * @brief sets the description of the extension
		 *
		 * @param[in] $description - the description of the extension
		 */
		public function setDescription($description);

		/**
		 * @fn getType
		 *
		 * @brief gets the type of extensions
		 *        see the extension type table for possibilities
		 *
		 * @return extension type
		 */
		public function getType();

		public function preProcess();
		public function postProcess();
		 
		/**
		 * @brief allows the extension to be previewed
		 *
		 * @param[in] $parentElement - the parent element of this extension
		 */
		public function preview(& $parentElelement);

		/**
		 * @brief populates the page with the appropriate content for the given
		 *        page type.
		 *
		 * @param[in] $parentElement - the parent element of this extension
		 */
		public function populate(& $parentElelement);
		
		/**
		 * @fn activate
		 *
		 * @brief activates the extension
		 */
		public function activate();
		
		/**
		 * @fn deactivate
		 *
		 * @brief deactivates the extension
		 */
		public function deactivate();
		
		/**
		 * @fn reset
		 *
		 * @brief resets the extension back to its default configuration
		 */
		 public function reset();
	}	
}	
?>