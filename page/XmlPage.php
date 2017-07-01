<?php
/**
 * @module XmlPage
 *
 * @brief class for all xml pages for rendering
 */
 namespace afm
 {
    // Includes
    include_once('Page.php');

    // module defines

    class XmlPage extends Page
    {
        function __construct($rootElement)
        {
            parent::__construct();
            
            $this->setElementName($rootElement);
        }

		static public function withDocument($document)
		{
			$page = new XmlPage("afm");
			
			$page->load($document);
			
			return $page;
		}
		
		public function addNode($nodeName, $parentNode = null)
		{
			$nodeElement = new Element();
			
			$nodeElement->setElementName($nodeName);
			
			if ($parentNode != null)
			{
				$parentNode->addChildElement($nodeElement);
			}
			else
			{
				$this->addChildElement($nodeElement);
			}
			return $nodeElement;	
		}
		
		public function getNode($nodeName)
		{
			$node = null;
			
			if ($nodeName != $this->getElementName())
			{
				// look for this child
				$node = $this->getElement($nodeName);
			}
			else
			{
				$node = $this;
			}
						
			return $node;
		}
		
	    /**
		 * @fn setData
		 *
		 * @copydoc IElement::setData
		 */
        public function setData($data)
        {
	        $trimmedString = trim($data);

			// set the data if its not empty	        
	        if (strlen($trimmedString) > 0)
	        {
		        parent::setData($trimmedString);
	        }
        }
		
		public function load($file)
		{
	      /* Open the file for reading */
	      $xmlFile = new \DOMDocument();

	      $xmlFile->load($file);

	      $topNode = $xmlFile->documentElement;
	      if ($topNode != null)
	      {
	      	$this->processChild($this, $topNode, true);
	      }
  		}
			
        public function render()
        {
            # Set the appropriate headers for this document
            header("Content-Type: application/xhtml+xml;charset=iso-8859-1");

            $renderedPage = '<?xml version="1.0" encoding="ISO-8859-1" ?>' . PHP_EOL;

            /*--------------------------------------------------------------------
	            Dump header definition, namespace etc
            ------------------------------------------------------------------*/

            // render all of the page content
            $renderedPage .= parent::render();// . PHP_EOL;
            

//            error_log("XML: " . $renderedPage);

            if ($this->isDirectDisplay() == true)
            {	            
                echo $renderedPage;
            }
            return $renderedPage;
        }
        
	    private function processChild(& $parent, $node, $isTopNode = false)
	    {
	/**
	 * @note these are the possible elements we might find
	 *			I am not worrying about all of them
	 *
	 *  1 XML_ELEMENT_NODE
	 *  2 XML_ATTRIBUTE_NODE
	 *  3 XML_TEXT_NODE
	 *  4 XML_CDATA_SECTION_NODE
	 *  5 XML_ENTITY_REFERENCE_NODE
	 *  6 XML_ENTITY_NODE
	 *  7 XML_PROCESSING_INSTRUCTION_NODE
	 *  8 XML_COMMENT_NODE
	 *  9 XML_DOCUMENT_NODE
	 * 10 XML_DOCUMENT_TYPE_NODE
	 * 11 XML_DOCUMENT_FRAGMENT_NODE
	 * 12 XML_NOTATION_NODE
	 */
//	 		error_log('Parent: ' . $parent->getElementName());
	 		
	    	if ($node != null)
	    	{
	    		$nodeName = $node->nodeName;
	    		if ($node->prefix != NULL)
	    		{
	    			$nodeName = substr($node->nodeName, strpos($nodeName, ':') + 1);
	    		}
		        $childElement = null;
		        switch ($node->nodeType)
		        {
		        	case XML_ELEMENT_NODE:
	        		{
						if ($isTopNode == false)
						{
//							error_log('Adding child element: ' . $nodeName . ' to: ' . $parent->getElementName());
			        		$childElement = $parent->addElement($nodeName);
			        	}
			        	else
			        	{
//				        	error_log('Setting parent name to: ' . $nodeName);
				        	$parent->setElementName($nodeName);
				        	$childElement = $parent;
			        	}
	        		}
	        		break;
		        	case XML_ATTRIBUTE_NODE:
		        	{
//			        	error_log('Adding attribute: ' . $nodeName . ' with value: ' . $node->nodeValue . ' to: ' . $parent->getElementName());
	        			$parent->addAttribute($nodeName, $node->nodeValue);
	        			$childElement = $parent; // when adding attributes, we must remain on the same parent/child
	        		}
	        		break;
		        	case XML_TEXT_NODE:
		        	case XML_CDATA_SECTION_NODE:
		        	{
//			        	error_log('Setting data for node: ' . $parent->getElementName() . ' value: ' . $node->nodeValue);
			        	$parent->setData($node->nodeValue);
	        		}
	        		break;
		        	default:
	        		{
	//        			print "NodeType: " . $node->nodeType . " node name: " . $node->nodeName . "<br/>";
	        		}
	        		break;
		        }
		        
		        if ($node->hasChildNodes())
		        {
//			        error_log('Processing children for: ' . $parent->getElementName() . ' and child element: ' . $childElement->getElementName());
			        foreach ($node->childNodes as $childNode)
			        {
//				        error_log('Child Node: ' . $childNode->nodeName);
				    	$this->processChild($childElement, $childNode);
		            }
		        }
		        
		        if ($node->hasAttributes())
		        {
//			        error_log('Processing attributes for: ' . $parent->getElementName() . ' and child element: ' . $childElement->getElementName());
		        	foreach ($node->attributes as $childNode)
		        	{
//			        	error_log('Adding child attr: ' . $childNode->nodeName);
		        		$this->processChild($childElement, $childNode);
		        	}
		        }
	    	}
	    }
    }
}
?>