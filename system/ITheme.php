<?php
/**
 * @module ITheme
 *
 * @brief theme interface for all themes to implement
 */
namespace afm
{
	// elements
	define('THEME_ROOT_ELEMENT', "theme");
	define('THEME_DESCRIPTION_ELEMENT', "description");
	define('THEME_CODE_ELEMENT', "code");
	define('THEME_PATH_ELEMENT', "path");
	define('THEME_AUTHOR_ELEMENT', "author");
	define('THEME_SETTINGS_ELEMENT', "settings");
	define('THEME_IMAGE_ELEMENT', "image");
	
	// attributes
	define('THEME_NAME_ATTR', "name");
	define('THEME_VERSION_ATTR', "version");
	define('THEME_REQUIRES_ATTR', "requires");
	define('THEME_CLASS_ATTR', "class");
	define('THEME_TYPE_ATTR', "type");
	define('THEME_WEBSITE_ATTR', "website");
	define('THEME_SOURCE_ATTR', "src");
	define('THEME_ID_ATTR', "id");

	interface ITheme
	{
		/**
		 * @brief initializes the theme, the theme can check the page type to
		 *        load/select what is needed in that regard
		 *
		 * @param[in] $themeURL - relative path to the theme
		 */
		public function initialize($themeURL);

		/**
		 * @brief sets the theme as active so it knows how to handle things
		 *
		 * @param[in] $active - true if active, false otherwise
		 */
		public function setActive($isActive);

		/**
		 * @brief returns a flag indicating if this theme is active or not
		 *
		 * @return true if active, false otherwise
		 */
		public function getActive();

		/**
		 * @brief loads a theme assets, prelude to instantiating the final version.
		 *
		 * @param[in] $Path - the path to the theme to be loaded
		 */
		 public function load($path);

		/**
		 * @brief populates the given page with the content specific to this theme
		 *
		 * @param[in] $parentElement - the parent element to place this preview
		 */
		public function populate(& $parentElement);

		public function setPreview($isPreview);
		public function getPreview();

		/**
		 * @brief create a preview of the content
		 *
		 * @param[in] $parentElement - the parent element to place this preview
		 */
		 public function preview(& $parentElement);

		/**
		 * @brief gets the area to show some content
		 *
		 * @return the element to hold the main content of the page
		 */
		public function &getMainContentArea();
		
		/**
		 * @brief returns the directory of the theme
		 *
		 * @return the directory the theme is in
		 */
		public function getThemeDirectory();

		/**
		 * @brief activates the theme to be the current site theme
		 */
		public function activate();
		
		/**
		 * @brief deactivates the theme as the current site them
		 */
		public function deactivate();
		
		/**
		 * @brief resets the theme back to its default configuration
		 */
		 public function reset();
	}	
}	
?>