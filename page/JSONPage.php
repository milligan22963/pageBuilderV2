<?php
/**
 * @module JSONPage
 *
 * @brief class for all JSON pages for rendering
 */
 namespace afm
 {
    // Includes
    include_once('Page.php');

    // module defines
	define('JSON_SUCCESS', "success");
	define('JSON_TRUE', "true");
	define('JSON_FALSE', "false");

    class JSONPage extends Page
    {
	    private $m_jsonTree;
	    
        function __construct()
        {
            parent::__construct();
            
            $this->m_jsonTree = array();
        }

		static public function withData($data)
		{
			$page = new JSONPage();
			
			$page->load($data);
			
			return $page;
		}
	
		public function createArray()
		{
			$newArray = array();
			
			$this->m_jsonTree[] = $newArray;
			
			return $newArray;
		}
		
		public function addObject($name, $value)
		{
			$this->m_jsonTree[$name] = $value;
		}
				
		public function load($dataString)
		{
			$this->m_jsonTree = json_decode($dataString, true);			
  		}
			
        public function render()
        {
			# Set the appropriate headers for this document
			header("Content-Type: application/json");
			
	        $renderedPage = json_encode($this->m_jsonTree);
	        
            if ($this->isDirectDisplay() == true)
            {	            
                echo $renderedPage;
            }
            return $renderedPage;
        }        
    }
}
?>