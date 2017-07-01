<?php
/**
 * @module HtmlObjects
 *
 * @brief base class for display types and objects
 */
namespace afm
{
    // Includes
    include_once('Element.php');

    class CssLinkObject extends Element
    {
        public function __construct($cssFile)
        {
            parent::__construct();

            $this->setElementName(LINK);

            $this->addAttribute(RELATIVE, STYLE_SHEET);
            $this->addAttribute(HREF, $cssFile);
            $this->addAttribute(TYPE, CSS_TYPE);
        }
    }

    class CssInlineObject extends Element
    {
        public function __construct($cssData)
        {
            parent::__construct();

            $this->setElementName(STYLE);

            $this->addChildElement($cssData);
        }

        public function addChildElement($cssData)
        {
            parent::addChildElement(Element::withData($cssData));
        }
    }

    class JSFileObject extends Element
    {
        public function __construct($jsFile)
        {
            parent::__construct();
            
            $this->setElementName(SCRIPT);
            $this->setData(" "); // if I short close the tag, gets screwed.  add a space so its fully qualified i.e. <script></script> opposed to <script />
            $this->addAttribute(LANGUAGE, JS_LANGUAGE);
            $this->addAttribute(SOURCE, $jsFile);
            $this->addAttribute(TYPE, JS_TYPE);
        }
    }

    class JSInlineObject extends Element
    {
        public function __construct($jsData)
        {
            parent::__construct();
            
            $this->setElementName(SCRIPT);

            $this->addAttribute(LANGUAGE, JS_LANGUAGE);
            $this->addAttribute(TYPE, JS_TYPE);

            $this->addChildElement($jsData);
        }

        public function addChildElement($jsData)
        {
            parent::addChildElement(Element::withData($jsData));
        }
    }

    class MetaDataObject extends Element
    {
        public function __construct($name, $value)
        {
            parent::__construct();
            
            $this->setElementName(META);

            $this->addAttribute($name, $value);
        }
    }    
}
?>