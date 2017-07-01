<?php
/**
 * @module UserPage
 *
 * @brief class for all user pages for rendering
 */
 namespace afm
 {
    // Includes
    include_once('HtmlPage.php');

	define('USER_OPTION', "userOption");
    define('EXTENSION_OPTION', "extOption");

    class UserPage extends HtmlPage
    {
        function __construct()
        {
            parent::__construct();

            $this->setDomain(PAGE_USER_DOMAIN);
        }
    }
}
?>