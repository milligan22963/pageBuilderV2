<?php
/**
 * @module Page
 *
 * @brief base class for all pages for rendering
 */

namespace afm
{ 
    // Includes
    include_once('Element.php');

    class Page extends Element
    {
        private $m_displayElements;
        private $m_directDisplay;
        private $m_domain;

        function __construct()
        {
	        parent::__construct();
	        
            $this->m_displayElements = array();
            $this->m_directDisplay = false;
            $this->m_domain = PAGE_USER_DOMAIN;
        }
        
        public function setDomain($domain)
        {
	        $this->m_domain = $domain;
        }
        
        public function getDomain()
        {
	        return $this->m_domain;
        }

        public function setDirectDisplay($directDisplay)
        {
            $this->m_directDisplay = $directDisplay;
        }

		// these are here to allow for grouped objects that have their own section
        public function addDisplayElement($typeName, $element)
        {
            if (array_key_exists($typeName, $this->m_displayElements) != TRUE)
            {
                $this->m_displayElements[$typeName] = array(); // new array for this type
            }
            $this->m_displayElements[$typeName][] = $element;
        }

        protected function getDisplayElements($typeName)
        {
	        $elements = null;
	        
            if (array_key_exists($typeName, $this->m_displayElements) == TRUE)
			{
				$elements = $this->m_displayElements[$typeName];
			}
            return $elements;
        }

        protected function isDirectDisplay()
        {
            return $this->m_directDisplay;
        }
    }
}
?>