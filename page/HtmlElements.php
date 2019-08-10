<?php
/**
 * @module HtmlElements
 *
 * @brief html elements that one might find on a page of html
 */
namespace afm
{
    // Includes
    include_once('Element.php');

    // local defines
	define('LABEL_ELEMENT', "label");
	define('SPAN_ELEMENT', "span");
    define('DIV_ELEMENT', "div");
    define('SECTION_ELEMENT', "section");
    define('ASIDE_ELEMENT', "aside");
    define('HEADER_ELEMENT', "header");
	define('HEADING_ELEMENT', "h");
    define('FOOTER_ELEMENT', "footer");
    define('ANCHOR_ELEMENT', "a");
	define('IMAGE_ELEMENT', "img");
	define('BOLD_ELEMENT', "b");
	define('ITALIC_ELEMENT', "i");
	define('STRONG_ELEMENT', "strong");
	define('EMPHASIS_ELEMENT', "em");
	define('HORIZONTAL_RULE_ELEMENT', "hr");
	define('BREAK_ELEMENT', "br");
	define('BLOCKQUOTE_ELEMENT', "blockquote");
	define('QUOTE_ELEMENT', "q");
	define('ABBREVIATION_ELEMENT', "abbr");
	define('CITATION_ELEMENT', "cite");
	define('DEFINITION_ELEMENT', "dfn");
	define('ADDRESS_ELEMENT', "address");
	define('INSERTED_ELEMENT', "ins");
	define('DELETED_ELEMENT', "del");
	define('STRIKE_THROUGH_ELEMENT', "s");
	define('ORDERED_LIST_ELEMENT', "ol");
	define('UNORDERED_LIST_ELEMENT', "ul");
	define('LIST_ITEM_ELEMENT', "li");
	define('DEFINITION_LIST_ELEMENT', "dl");
	define('DEFINITION_TERM_ELEMENT', "dt");
	define('DEFINITION_DETAIL_ELEMENT', "dd");

    define('HREF_ATTR', "href");
    define('FOR_ATTR', "for");
	define('SRC_ATTR', "src");
	define('ALT_ATTR', "alt");
	define('TITLE_ATTR', "title");

	class HtmlElement extends Element
	{
	    public function __construct($id)
	    {
		    parent::__construct();

			if ($id != null)
			{
				$this->setId($id);
			}
	    }

		public function setToolTip($toolTip)
		{
			$this->addAttribute(TITLE_ATTR, $toolTip);
		}

	}

	class DefinitionTermElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(DEFINITION_TERM_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$dtElement = new DefinitionTermElement($id);

			$dtElement->setData($text);

			$parentElement->addChildElement($dtElement);

			return $dtElement;
		}
	}

	class DefinitionDetailElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(DEFINITION_DETAIL_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$ddElement = new DefinitionDetailElement($id);

			$ddElement->setData($text);

			$parentElement->addChildElement($ddElement);

			return $ddElement;
		}
	}

	class DefinitionListElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(DEFINITION_LIST_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id)
		{
			$defListElement = new DefinitionListElement($id);

			$parentElement->addChildElement($defListElement);

			return $defListElement;
		}

		public function addDefinitionItem($termId, $termText, $definitionId, $definitionText)
		{
			$definitionElements = array();

			// these go in pairs
			$definitionElements[] = DefinitionTermElement::withParent($this, $termId, $termText);
			$definitionElements[] = DefinitionDetailElement::withParent($this, $definitionId, $definitionText);

			return $definitionElements;
		}
	}

	class ListItemElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(LIST_ITEM_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$listElement = new ListItemElement($id);

			$listElement->setData($text);

			$parentElement->addChildElement($listElement);

			return $listElement;
		}		
	}
    
	class OrderedListElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(ORDERED_LIST_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id)
		{
			$listElement = new ListElement($id);

			$parentElement->addChildElement($listElement);

			return $listElement;
		}

		public function addListItem($id, $text)
		{
			$listItem = ListItemElement::withParent($this, $id, $text);

			return $listItem;
		}
	}

	class UnOrderedListElement extends HtmlElement  
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(UNORDERED_LIST_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id)
		{
			$listElement = new UnOrderedListElement($id);

			$parentElement->addChildElement($listElement);

			return $listElement;
		}

		public function addListItem($id, $text)
		{
			$listItem = ListItemElement::withParent($this, $id, $text);

			return $listItem;
		}
	}

	class BoldElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(BOLD_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$boldElement = new BoldElement($id);
			$boldElement->setData($text);

			$parentElement->addChildElement($boldElement);

			return $boldElement;
		}
	}

	class ItalicElement extends HtmlElement 
	{
	    public function __construct()
	    {
		    parent::__construct(null);

			$this->setElementName(ITALIC_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$italicElement = new ItalicElement($id);
			$italicElement->setData($text);

			$parentElement->addChildElement($italicElement);

			return $italicElement;
		}
	}

	class StrongElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(STRONG_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$strongElement = new StrongElement($id);
			$strongElement->setData($text);

			$parentElement->addChildElement($strongElement);

			return $strongElement;
		}
	}

	class EmphasisElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(EMPHASIS_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$emphasisElement = new EmphasisElement($id);
			$emphasisElement->setData($text);

			$parentElement->addChildElement($emphasisElement);

			return $emphasisElement;
		}
	}

	class HorizontalRuleElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(HORIZONTAL_RULE_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id)
		{
			$hrElement = new HorizontalRuleElement($id);

			$parentElement->addChildElement($hrElement);

			return $hrElement;
		}
	}

	class BreakElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(BREAK_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id)
		{
			$brElement = new BreakElement($id);

			$parentElement->addChildElement($brElement);

			return $brElement;
		}
	}

	class BlockQuoteElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(BLOCKQUOTE_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$blockElement = new BlockQuoteElement($id);

			$blockElement->setData($text);

			$parentElement->addChildElement($blockElement);

			return $blockElement;
		}
	}

	class QuoteElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(QUOTE_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$quoteElement = new QuoteElement($id);

			$quoteElement->setData($text);

			$parentElement->addChildElement($quoteElement);

			return $quoteElement;
		}
	}

	class AbbreviationElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(ABBREVIATION_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$abbreviationElement = new AbbreviationElement($id);

			$abbreviationElement->setData($text);

			$parentElement->addChildElement($abbreviationElement);

			return $abbreviationElement;
		}
	}

	class CitationElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(CITATION_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$citeElement = new CitationElement($id);

			$citeElement->setData($text);

			$parentElement->addChildElement($citeElement);

			return $citeElement;
		}
	}

	class DefinitionElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(DEFINITION_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$definitionElement = new DefinitionElement($id);

			$definitionElement->setData($text);

			$parentElement->addChildElement($definitionElement);

			return $definitionElement;
		}
	}

	class AddressElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(ADDRESS_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$addressElement = new AddressElement($id);

			$addressElement->setData($text);

			$parentElement->addChildElement($addressElement);

			return $addressElement;
		}
	}

	class InsertedElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(INSERTED_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$insertedElement = new InsertedElement($id);

			$insertedElement->setData($text);

			$parentElement->addChildElement($insertedElement);

			return $insertedElement;
		}
	}

	class DeletedElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(DELETED_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$deletedElement = new DeletedElement($id);

			$deletedElement->setData($text);

			$parentElement->addChildElement($deletedElement);

			return $deletedElement;
		}
	}

	class StrikeThroughElement extends HtmlElement 
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(STRIKE_THROUGH_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$strikeThroughElement = new StrikeThroughElement($id);

			$strikeThroughElement->setData($text);

			$parentElement->addChildElement($strikeThroughElement);

			return $strikeThroughElement;
		}
	}

    class LabelElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(LABEL_ELEMENT);
	    }
	    
		static public function withParent($parentElement, $id, $text)
		{
			$labelElement = new LabelElement($id);
			$labelElement->setData($text);

			$parentElement->addChildElement($labelElement);

			return $labelElement;
		}

	    // set who this label is for
	    public function setForElement($elementId)
	    {
		    $this->addAttribute(FOR_ATTR, $elementId);
	    }
	}
	
	class SpanElement extends HtmlElement
	{
		public function __construct($id)
		{
			parent::__construct($id);

			$this->setElementName(SPAN_ELEMENT);
		}
	}
    
    class DivElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(DIV_ELEMENT);
	    }

		static public function withParent($parentElement, $id)
		{
			$divElement = new DivElement($id);

			$parentElement->addChildElement($divElement);

			return $divElement;
		}
    }
    
    class SectionElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(SECTION_ELEMENT);
	    }

		static public function withParent($parentElement, $id)
		{
			$sectionElement = new SectionElement($id);

			$parentElement->addChildElement($sectionElement);

			return $sectionElement;
		}
    }
    
    class AsideElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(ASIDE_ELEMENT);
	    }

		static public function withParent($parentElement, $id)
		{
			$asideElement = new AsideElement($id);

			$parentElement->addChildElement($asideElement);

			return $asideElement;
		}
    }
    
    class HeaderElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(HEADER_ELEMENT);
	    }

		static public function withParent($parentElement, $id)
		{
			$headerElement = new HeaderElement($id);

			$parentElement->addChildElement($headerElement);

			return $headerElement;
		}
    }
    
    class FooterElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(FOOTER_ELEMENT);
	    }

		static public function withParent($parentElement, $id)
		{
			$footerElement = new FooterElement($id);

			$parentElement->addChildElement($footerElement);

			return $footerElement;
		}
    }
    
    class AnchorElement extends HtmlElement
    {
	    public function __construct($anchorId)
	    {
		    parent::__construct($anchorId);
		    
		    $this->setElementName(ANCHOR_ELEMENT);
	    }

		static public function withParent($parentElement, $id)
		{
			$anchorElement = new AnchorElement($id);

			$parentElement->addChildElement($anchorElement);

			return $anchorElement;
		}
	    
	    public function setLink($link)
	    {
		    $this->addAttribute(HREF_ATTR, $link);		    
	    }	    
    }

	class ImageElement extends HtmlElement 
	{
	    public function __construct($imageId)
	    {
		    parent::__construct($imageId);
		    
		    $this->setElementName(IMAGE_ELEMENT);
	    }

		static public function withParent($parentElement, $id)
		{
			$imageElement = new ImageElement($id);

			$parentElement->addChildElement($imageElement);

			return $imageElement;
		}

		public function setImageSource($source)
		{
			$this->addAttribute(SRC_ATTR, $source);
		}

		public function setAlternateText($altText)
		{
			$this->addAttribute(ALT_ATTR, $altText);
		}
	}

	class HeadingElement extends HtmlElement
	{
	    public function __construct($id, $level)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(HEADING_ELEMENT . $level);
	    }

		static public function withParent($parentElement, $id, $level)
		{
			$headingElement = new HeadingElement($id, $level);

			$parentElement->addChildElement($headingElement);

			return $headingElement;
		}	    
	}
}
?>