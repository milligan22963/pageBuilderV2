<?php
/**
 * @module IElement
 *
 * @brief base class for display elements and objects
 */

namespace afm
{
	/**
	 * Common defines for display objects
	 */
	define('ID_TAG', "id");
	define('CLASS_TAG', "class");

	/**
	 * @interface IElement
	 *
	 * @brief defines what a display element is and can do
	 */	
    interface IElement
    {
	    /**
		 * @fn setId
		 *
		 * @brief sets the id of the element, most likely a string
		 *
		 * @param[in] objectId - the id for this object
		 */
		public function setId($objectId);

	    /**
		 * @fn getId
		 *
		 * @brief returns the id of the element.
		 *
		 * @return an id representing this element, most likely a string
		 */
		public function getId();
		
	    /**
		 * @fn setElementName
		 *
		 * @brief sets the name for this element such as label, div, etc
		 *
		 * @param[in] typeName - the name of this element such as label, div, etc.
		 */
        public function setElementName($elementName);

	    /**
		 * @fn getElementName
		 *
		 * @brief returns the name for this element.
		 *
		 * @return a typename for this element such as div, label, etc
		 */
        public function getElementName();
		
	    /**
		 * @fn setData
		 *
		 * @brief sets the data for this element
		 *
		 * @param[in] data - the data this element will contain
		 */
        public function setData($data);
        
	    /**
		 * @fn getData
		 *
		 * @brief returns the data for this element.
		 *
		 * @return a data value for this element
		 */
        public function getData();

	    /**
		 * @fn addElement
		 *
		 * @brief adds the specified element to the page tree
		 *
		 * @param[in] elementName - the name of the element to add
		 *
		 * @return the newly added element
		 */
        public function addElement($elementName);

		/**
		 * @fn getElement
		 *
		 * @brief finds the given element by name if it exists and returns it
		 *
		 * @param[in] elementName - the name of the element to get
		 *
		 * @return, the element if it exists, null otherwise
		 */
		 public function getElement($elementName);

		/**
		 * @fn getElements
		 *
		 * @brief finds the given elements by name
		 *
		 * @param[in] elementName - the name of the element(s) to get
		 *
		 * @return, an array of the element(s)
		 */
		public function getElements($elementName);
		 
		/**
		 * @fn addClass
		 *
		 * @brief adds a class to the class specifies for the given display element
		 *
		 * @param[in] class - the class string to add to this element
		 */
		public function addClass($class);

		/**
		 * @fn addAttribute
		 *
		 * @brief adds an attribute name/value pair to the given display element
		 *
		 * @param[in] name - the name of the attribtue to add
		 * @param[in] value - the value for the given attribute
		 */
        public function addAttribute($name, $value);
        
		/**
		 * @fn hasAttribute
		 *
		 * @brief checks for the existance of an attribute name
		 *
		 * @param[in] name - the name of the attribtue to find
		 *
		 * @return true if there, false otherwise
		 */
        public function hasAttribute($name);

		/**
		 * @fn getAttribute
		 *
		 * @brief checks for the existance of an attribute
		 *
		 * @param[in] name - the name of the attribtue to find
		 *
		 * @return the attribute value if there or null otherwise
		 */
        public function getAttribute($name);

		/**
		 * @fn removeAttribute
		 *
		 * @brief removes an attribute name/value pair from the given display element
		 *
		 * @param[in] name - the name of the attribtue to remove
		 */
        public function removeAttribute($name);
        
		/**
		 * @fn addChildElement
		 *
		 * @brief adds a display element to the element
		 *
		 * @param[in] elementObject - the child element to add
		 */
        public function addChildElement($elementObject);
        
		/**
		 * @fn getChildElements
		 *
		 * @brief gets all of the child elements of the given name
		 *
		 * @param[in] element name which is desired, null if all
		 */
        public function getChildElements($childName = null);
        
		/**
		 * @fn hasChildren
		 *
		 * @brief determines if their are child elements associated wtih this element
		 *
		 * @return true if their are children, false otherwise
		 */
        public function hasChildren();

		/**
		 * @fn render
		 *
		 * @brief converts all of the elements from object to a string representation for presentation to the user
		 *
		 * @return a string representation of the element tree
		 */
        public function render();
    }
}
?>