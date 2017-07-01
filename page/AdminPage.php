<?php
/**
 * @module AdminPage
 *
 * @brief class for all html pages for rendering
 */
 namespace afm
 {
    // Includes
    include_once('HtmlPage.php');

	define('ADMIN_OPTION', "adminOption");

    class AdminPage extends HtmlPage
    {
        function __construct()
        {
            parent::__construct();

            $this->setDomain(PAGE_ADMIN_DOMAIN);
        }
    }
}
?>