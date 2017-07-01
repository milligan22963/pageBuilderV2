<?php
/**
 * @module Element
 *
 * @brief base class for display elements and objects
 */

namespace afm
{
    // local defines used across elements
    define('LINK', "link");
    define('SCRIPT', "script");
    define('STYLE', "style");
    define('META', "meta");
    define('RELATIVE', "rel");
    define('HREF', "href");
    define('TYPE', "type");
    define('LANGUAGE', "language");
    define('SOURCE', "src");
    define('STYLE_SHEET', "stylesheet");
    define('CSS_TYPE', "text/css");
    define('JS_TYPE', "text/javascript");
    define('JS_LANGUAGE', "javascript");
  
    // Includes
    include_once('IElement.php');
	
	/**
	 * @class Element representing a display element
	 */
    class Element implements IElement
    {
        private $m_data;
        private $m_elementName;
        private $m_children;
        private $m_attributes;

		/**
		 * @ctor __construct
		 *
		 * @brief standard constructor for this class
		 */
        public function __construct()
        {
            $this->m_data = null;
            $this->m_elementName = null;
            $this->m_children = null;
            $this->m_attributes = array();
        }

		/**
		 * @brief static function to create an element with the given name and data
		 *
		 * @param typeName - the name of the element such as div, label, etc
		 * @param data - the data to be contained by the element
		 */
        public static function withNameAndData($typeName, $data)
        {
            $element = new Element();
            $element->setValues($typeName, $data);

            return $element;
        }

		/**
		 * @brief static function to create an element with the given name
		 *
		 * @param typeName - the name of the element such as div, label, etc
		 */
        public static function withName($typeName)
        {
            $element = new Element();
            $element->setValues($typeName, null);

            return $element;
        }

		/**
		 * @brief static function to create an element with the given data
		 *
		 * @param typeName - the name of the element such as div, label, etc
		 * @param data - the data to be contained by the element
		 */
        public static function withData($data)
        {
            $element = new Element();
            $element->setValues(null, $data);

            return $element;            
        }

		/**
		 * @brief function to set the name and data portion of an element
		 *
		 * @param typeName - the name of the element such as div, label, etc
		 * @param data - the data to be contained by the element
		 */
        protected function setValues($elementName, $data)
        {
            $this->m_elementName = $elementName;
            $this->m_data = $data;
        }
        
        /*----------------------------------------------------------------
	        IElement interface implementation
          ----------------------------------------------------------------*/

	    /**
		 * @copydoc IElement::setId
		 */
		public function setId($objectId)
		{
			$this->m_attributes[ID_TAG] = $objectId;
		}

	    /**
		 * @copydoc IElement::getId
		 */
		public function getId()
		{
			$id = 0;
			
			if (array_key_exists(ID_TAG, $this->m_attributes) == TRUE)
			{
				$id = $this->m_attributes[ID_TAG];
			}
			return $id;
		}
		
	    /**
		 * @copydoc IElement::setTypeName
		 */
        public function setElementName($elementName)
        {
            $this->m_elementName = $elementName;
        }
        
	    /**
		 * @copydoc IElement::getElementName
		 */
        public function getElementName()
        {
            return $this->m_elementName;
        }

	    /**
		 * @copydoc IElement::setData
		 */
        public function setData($data)
        {
            $this->m_data = $data;
        }
        
	    /**
		 * @copydoc IElement::getData
		 */
        public function getData()
        {
            return $this->m_data;
        }

	    /**
		 * @copydoc IElement::addElement
		 */
        public function addElement($elementName)
        {
            $element = Element::withName($elementName);

            $this->addChildElement($element);

            return $element;
        }

		/**
		 * @copydoc IElement::getElement
		 */
		 public function getElement($elementName)
		 {
			 $element = null;
			 
			 // this will fail if more than one child with the same name
			 foreach ($this->m_children as $childElement)
			 {
				 if ($childElement->getElementName() == $elementName)
				 {
					 $element = $childElement;
					 break;
				 }
			 }
			 
/*			 if ($element == null)
			 {
				 foreach ($this->m_children as $childElement)
				 {
					 error_log('The child: ' . $childElement->getElementName());
				 }
			 }*/
			 
			 return $element;
		}
		 
		/**
		 * @copydoc IElement::getElements
		 */
		public function getElements($elementName)
		{
			$elements = array();
			
			 // this will fail if more than one child with the same name
			 if ($this->m_children != null)
			 {
				foreach ($this->m_children as $childElement)
				{
					if ($childElement->getElementName() == $elementName)
					{
						$elements[] = $childElement;
					}
				}
			 }			
			return $elements;
		}

		/**
		 * @copydoc IElement::addClass
		 */
		public function addClass($class)
		{
			if (array_key_exists(CLASS_TAG, $this->m_attributes) == FALSE)
			{
				$this->m_attributes[CLASS_TAG] = $class;
			}
			else
			{
				$this->m_attributes[CLASS_TAG] .= " " . $class;
			}
		}

		/**
		 * @copydoc IElement::addAttribute
		 */
        public function addAttribute($name, $value)
        {
            $this->m_attributes[$name] = $value;
        }
        
		/**
		 * @copydoc IElement::hasAttribute
		 */
        public function hasAttribute($name)
        {
	        $doesHaveAttribute = false;
	        
	        if (array_key_exists($name, $this->m_attributes) == TRUE)
	        {
		        $doesHaveAttribute = true;
	        }
	        
	        return $doesHaveAttribute;
        }
        
		/**
		 * @copydoc IElement::getAttribute
		 */
        public function getAttribute($name)
		{
			$attributeValue = null;
			
			if (array_key_exists($name, $this->m_attributes) == TRUE)
			{
				$attributeValue = $this->m_attributes[$name];
			}
			
			return $attributeValue;
		}
        
		/**
		 * @copydoc IElement::removeAttribute
		 */
        public function removeAttribute($name)
        {
	        if (array_key_exists($name, $this->m_attributes) == TRUE)
	        {
		        unset($this->m_attributes[$name]);
	        }
        }
        
		/**
		 * @copydoc IElement::addChildElement
		 */
        public function addChildElement($element)
        {
            if ($this->m_children == null)
            {
                $this->m_children = array();
            }

            $this->m_children[] = $element;
        }
        
		/**
		 * @copydoc IElement::getChildElements
		 */
        public function getChildElements($childName = null)
        {
	        $childElements = array();
	        
	        if ($childName != null)
	        {
		        foreach ($this->m_children as $child)
		        {
			        if ($child->getElementName() == $childName)
			        {
				        $childElements[] = $child;
			        }
		        }
	        }
	        else
	        {
		        $childElements = $this->m_children;
	        }
	        return $childElements;
        }

		/**
		 * @copydoc IElement::hasChildren
		 */
        public function hasChildren()
        {
            return $this->m_children != null; // if their are children then true, else false
        }

		/**
		 * @copydoc IElement::render
		 */
        public function render()
        {
            $hasElementName = true;

            // render this display element
            $renderedText = "";

            if ($this->getElementName() != null)
            {
                $renderedText = "<" . $this->getElementName();

                foreach ($this->m_attributes as $name => $value)
                {
                    $renderedText .= " " . $name . '="' . $value . '"';
                }
            }
            else
            {
                $hasElementName = false;
            }
            
            // DWM - note for a section displaying it as <section /> causes the browser to handle it poorly
            // and run the next element into the current  right now I will render each element such as
            // <index></index> opposed to <index />

			// Right now I only display either data or the children, not both.  Should that be so?
         //   if (($this->m_data != null) || ($this->hasChildren() == true))
         //   {
                if ($hasElementName == true)
                {	                
                    $renderedText .= ">";// . PHP_EOL;
                }

                if ($this->m_data != null)
                {
                    $renderedText .= $this->m_data;
                }
                else if ($this->hasChildren() == true)
                {
	                $renderedText .= PHP_EOL;
                    foreach ($this->m_children as $childObject)
                    {
                        $renderedText .= $childObject->render();
                    }
                }

                if ($hasElementName == true)
                {
                    $renderedText .= "</" . $this->getElementName() . ">";
                }
/*            }
            else
            {
                $renderedText .= "/>";                
            }*/
            $renderedText .= PHP_EOL;

            return $renderedText;
        }
    }
}
?>