<?php
/**
 * @module HtmlInputs
 *
 * @brief html input elements that one might find on a page of html
 */
namespace afm
{
    // Includes
    include_once('Element.php');
    include_once ('HtmlElements.php');

    // local defines
    define('INPUT', "input");
	define('FORM', "form");
	define('SELECT', "select");
	define('TEXT', "text");
	define('PASSWORD_TYPE', "password");
	define('SUBMIT', "submit");
	define('RESET', "reset");
    define('RADIO', "radio");
    define('CHECKBOX', "checkbox");
    define('BUTTON', "button");
    define('COLOR', "color");
	define('DATE', "date");
	define('DATETIME_LOCAL', "datetime-local");
	define('EMAIL_TYPE', "email");
	define('MONTH', "month");
	define('NUMBER', "number");
	define('RANGE', "range");
	define('SEARCH', "search");
	define('TEL', "tel");
	define('TIME', "time");
	define('URL', "url");
	define('WEEK', "week");
    define('OPTION', "option");
    define('MULTIPLE', "multiple");
    define('VALUE', "value");
    define('SELECTED', "selected");
    define('TEXTAREA', "textarea");
    define('ONCLICK', "onclick");
    define('DATALIST', "datalist");
    define('KEYGEN', "keygen");
    define('OUTPUT', "output");
    define('ACTION', "action");
    define('LIST_ELEMENT', "list");
    define('OPTION_GROUP', "optgroup");
    define('FIELD_SET', "fieldset");
    define('LEGEND', "legend");
    define('METHOD', "method");
    define('POST', "post");
    define('GET', "get");
    define('REQUIRED', "required");
    define('ENCTYPE', "enctype");
    define('NAME_TAG', "name");
    define('HIDDEN', "hidden");
    define('VALUE_ATTRIBUTE', "value");
    define('PLACEHOLDER_ATTR', "placeholder");
    
	class InputElement extends HtmlElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setElementName(INPUT);
		}
		
	    /**
		 * @fn setId
		 *
		 * @copydoc IElement::setId
		 */
		public function setId($objectId)
		{
			parent::setId($objectId);
			
			$this->addAttribute(NAME_TAG, $objectId);
		}

		public function setRequired($isRequired)
		{
			if ($isRequired == true)
			{
				$this->addAttribute(REQUIRED, REQUIRED);
			}
			else
			{
				$this->removeAttribute(REQUIRED);
			}
		}
		
		public function setInputType($inputType)
		{
			// if already there it will be replaced
			$this->addAttribute(TYPE, $inputType);
		}
		
		public function setValue($value)
		{
			$this->addAttribute(VALUE_ATTRIBUTE, $value);
		}
	}
	
	class HiddenElement extends InputElement
	{
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
			$this->setInputType(HIDDEN);
	    }
	}

    class ButtonElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(BUTTON);
	    }

		static public function withParent($parent, $id)
		{
			$button = new ButtonElement($id);

			$parent->addChildElement($button);

			return $button;
		}
	    
	    public function addClickHandler($handler)
	    {
		    $this->addAttribute(ONCLICK, $handler);
	    }
    }
    
    class KeygenInput extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(KEYGEN);
	    }
    }
    
    class OutputElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(OUTPUT);
	    }

	    public function setForElement($elementId)
	    {
		    $this->addAttribute(FOR_ATTR, $elementId);
	    }
    }
    
    class TextAreaInput extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(TEXTAREA);		    
	    }

	    /**
		 * @fn setId
		 *
		 * @copydoc IElement::setId
		 */
		public function setId($objectId)
		{
			parent::setId($objectId);
			
			$this->addAttribute(NAME_TAG, $objectId);
		}
    }
    
    class SelectOption extends HtmlElement
    {
	    public function __construct()
	    {
		    parent::__construct(null);
		    
		    $this->setElementName(OPTION);
	    }	    
    }
    
    class OptionGroup extends HtmlElement
    {
	    public function __construct($label)
	    {
		    parent::__construct(null);
		    
		    $this->setElementName(OPTION_GROUP);
		    
		    $this->addAttribute(LABEL, $label);
	    }
	    
	    public function addOption($option, $value, $isSelected)
	    {
		    $childElement = new SelectOption();
		    
		    $this->addChildElement($childElement);
		    
		    $childElement->addAttribute(VALUE, $option);
		    if ($isSelected == true)
		    {
			    $childElement->addAttribute(SELECTED, SELECTED);
			}
		    $childElement->setData($value);
		    
		    return $childElement;
	    }
    }
    
    class SelectElement extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(SELECT);
	    }

	    /**
		 * @fn setId
		 *
		 * @copydoc IElement::setId
		 */
		public function setId($objectId)
		{
			parent::setId($objectId);
			
			$this->addAttribute(NAME_TAG, $objectId);
		}
	    
	    public function addOptionGroup($optLabel)
	    {
		    $childElement = new OptionGroup($optLabel);
		    
		    $this->addChildElement($childElement);
		    
		    return $childElement;
	    }
	    
	    public function addOption($option, $value, $isSelected)
	    {
		    $childElement = new SelectOption();
		    
		    $this->addChildElement($childElement);
		    
		    $childElement->addAttribute(VALUE, $value);
		    if ($isSelected == true)
		    {
			    $childElement->addAttribute(SELECTED, SELECTED);
			}
		    $childElement->setData($option);
		    
		    return $childElement;
	    }
	    
	    public function setMultipleSelect($multipleSelect)
	    {
		    if ($multipleSelect == true)
		    {
			    $this->addAttribute(MULTIPLE, MULTIPLE); // name==value to set
		    }
		    else
		    {
			    $this->removeAttribute(MULTIPLE);
		    }
	    }
    }
    
    class DataListInput extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
		    $this->setElementName(DATALIST);		    
	    }
	    
	    public function addOption($option, $value)
	    {
		    $childElement = new SelectOption();
		    
		    $this->addChildElement($childElement);
		    
		    $childElement->addAttribute(VALUE, $selection);
		  
		    return $childElement;
	    }
    }

    class ListInput extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);
		    
			$this->setElementName(INPUT);
		}
	    
	    /**
		 * @fn setId
		 *
		 * @copydoc IElement::setId
		 */
		public function setId($objectId)
		{
			parent::setId($objectId);
			
			$this->addAttribute(NAME_TAG, $objectId);
		}
		
	    public function setDataListId($dataListId)
	    {
			$this->addAttribute(LIST_ELEMENT, $dataListId);		    
	    }
    }
    	
	class TextInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(TEXT);
		}
	}
	
	class PasswordInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(PASSWORD_TYPE);
		}
	}
	
	class SubmitInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(SUBMIT);
		}
	}

	class ResetInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(RESET);
		}
	}

	class RadioInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(RADIO);
		}
	}

	class CheckboxInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(CHECKBOX);
		}

		public function setChecked($isChecked)
		{
			if ($isChecked == true)
			{
				$this->addAttribute('checked', 'checked');
			}
			else
			{
				$this->removeAttribute('checked');
			}
		}
	}

	class ButtonInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(BUTTON);
		}
	}

	class ColorInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(COLOR);
		}
	}

	class DateInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(DATE);
		}
	}

	class DateTimeLocalInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(DATETIME_LOCAL);
		}
	}

	class EmailInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(EMAIL_TYPE);
		}

		public function setEmail($email)
		{
			$this->setValue($email);
		}
	}

	class MonthInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(MONTH);
		}
	}

	class NumberInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(NUMBER);
		}
	}

	class RangeInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(RANGE);
		}
	}

	class SearchInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(SEARCH);
		}
	}

	class TelephoneInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(TEL);
		}
	}

	class TimeInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(TIME);
		}
	}

	class UrlInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(URL);
		}
	}

	class WeekInput extends InputElement
	{
		public function __construct($id)
		{
			parent::__construct($id);
			
			$this->setInputType(WEEK);
		}
	}

	class LabeledInput extends DivElement
	{
		private $m_elementId;
		private $m_label;
		private $m_input;

		public function __construct($id)
		{
			parent::__construct($id . '_div');

			$this->m_elementId = $id;

			$this->m_label = new LabelElement($id . "_label");

			$this->m_input = null;
		}

		public function setLabelText($labelText)
		{
			$this->m_label->setData($labelText);
			$this->m_label->setForElement($this->m_elementId);

			$this->addChildElement($this->m_label);
		}

		public function setInputType($inputType)
		{
			switch ($inputType)
			{
				case SELECT:
				{
					$this->m_input = new SelectElement($this->m_elementId);
				}
				break;
				case TEXT:
				{
					$this->m_input = new TextInput($this->m_elementId);
				}
				break;
				case PASSWORD_TYPE:
				{
					$this->m_input = new PasswordInput($this->m_elementId);				    
				}
				break;
				case SUBMIT:
				{
					$this->m_input = new SubmitInput($this->m_elementId);
				}
				break;
				case RESET:
				{
					$this->m_input = new ResetInput($this->m_elementId);
				}
				break;
				case RADIO:
				{
					$this->m_input = new RadioInput($this->m_elementId);
				}
				break;
				case CHECKBOX:
				{
					$this->m_input = new CheckboxInput($this->m_elementId);
				}
				break;
				case BUTTON:
				{
					$this->m_input = new ButtonInput($this->m_elementId);
				}
				break;
				case COLOR:
				{
					$this->m_input = new ColorInput($this->m_elementId);
				}
				break;
				case DATE:
				{
					$this->m_input = new DateInput($this->m_elementId);
				}
				break;
				case DATETIME_LOCAL:
				{
					$this->m_input = new DateTimeLocalInput($this->m_elementId);
				}
				break;
				case EMAIL_TYPE:
				{
					$this->m_input = new EmailInput($this->m_elementId);
				}
				break;
				case MONTH:
				{
					$this->m_input = new MonthInput($this->m_elementId);
				}
				break;
				case NUMBER:
				{
					$this->m_input = new NumberInput($this->m_elementId);
				}
				break;
				case RANGE:
				{
					$this->m_input = new RangeInput($this->m_elementId);
				}
				break;
				case SEARCH:
				{
					$this->m_input = new SearchInput($this->m_elementId);
				}
				break;
				case TEL:
				{
					$this->m_input = new TelephoneInput($this->m_elementId);
				}
				break;
				case TIME:
				{
					$this->m_input = new TimeInput($this->m_elementId);
				}
				break;
				case URL:
				{
					$this->m_input = new UrlInput($this->m_elementId);
				}
				break;
				case WEEK:
				{
					$this->m_input = new WeekInput($this->m_elementId);
				}
				break;
				case TEXTAREA:
				{
					$this->m_input = new TextAreaInput($this->m_elementId);
				}
				break;
				case LIST_ELEMENT:
				{
					$this->m_input = new ListInput($this->m_elementId);				    
				}
				break;
			}

			$this->addChildElement($this->m_input);
		}

		public function getInputElement()
		{
			return $this->m_input;
		}

		public function setParent($parent)
		{
			$parent->addChildElement($this);
		}

		public function setRequired($isRequired)
		{
			if ($this->m_input != null)
			{
				$this->m_input->setRequired($isRequired);
			}
		}
	}

    class FieldSet extends HtmlElement
    {
	    public function __construct($id)
	    {
		    parent::__construct($id);

			$this->setElementName(FIELD_SET);
	    }
	    
		public function addLegend($legendText)
		{
			$childElement = Element::withName(LEGEND);
			
			$childElement->setData($legendText);
			
			$this->addChildElement($childElement);
			
			return $childElement;
		}

		public function addHiddenInput($id, $parent = null)
		{
			$hiddenInput = new HiddenElement($id);
			
			if ($parent != null)
			{
				$parent->addChildElement($hiddenInput);
			}
			else
			{
			    $this->addChildElement($hiddenInput);
			}
			
			return $hiddenInput;
		}
		
	    public function addTextInput($id, $parent = null)
	    {
		    $textInput = new TextInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($textInput);
			}
			else
			{
			    $this->addChildElement($textInput);
			}
		    
		    return $textInput;
	    }
	    
	    public function addPasswordInput($id, $parent = null)
	    {
		    $passwordInput = new PasswordInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($passwordInput);
			}
			else
			{
			    $this->addChildElement($passwordInput);
			}
		    
		    return $passwordInput;
	    }
	    
	    public function addSubmitButton($id, $parent = null)
	    {
		    $submitInput = new SubmitInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($submitInput);
			}
			else
			{
			    $this->addChildElement($submitInput);
			}

		    return $submitInput;
	    }

	    public function addResetButton($id, $parent = null)
	    {
		    $resetInput = new ResetInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($resetInput);
			}
			else
			{
			    $this->addChildElement($resetInput);
			}
		    
		    return $resetInput;
	    }
	    
	    public function addRadioButton($id, $parent = null)
	    {
		    $radioInput = new RadioInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($radioInput);
			}
			else
			{
			    $this->addChildElement($radioInput);
			}
		    
		    return $radioInput;		    
	    }

	    public function addCheckbox($id, $parent = null)
	    {
		    $checkBoxInput = new CheckboxInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($checkBoxInput);
			}
			else
			{
			    $this->addChildElement($checkBoxInput);
			}
		    
		    return $checkBoxInput;		    
	    }
	    
	    public function addButton($id, $parent = null)
	    {
		    $buttonInput = new ButtonInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($buttonInput);
			}
			else
			{
			    $this->addChildElement($buttonInput);
			}
		    
		    return $buttonInput;
	    }
	    
	    public function addColorInput($id, $parent = null)
	    {
		    $colorInput = new ColorInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($colorInput);
			}
			else
			{
			    $this->addChildElement($colorInput);
			}
		    
		    return $colorInput;
	    }
	    
	    public function addDateInput($id, $parent = null)
	    {
		    $dateInput = new DateInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($dateInput);
			}
			else
			{
			    $this->addChildElement($dateInput);
			}
		    
		    return $dateInput;
	    }

	    public function addDateTimeLocalInput($id, $parent = null)
	    {
		    $dateInput = new DateTimeLocalInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($dateInput);
			}
			else
			{
			    $this->addChildElement($dateInput);
			}
		    
		    return $dateInput;
	    }
	    
	    public function addEmailInput($id, $parent = null)
	    {
		    $emailInput = new EmailInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($emailInput);
			}
			else
			{
			    $this->addChildElement($emailInput);
			}
		    
		    return $emailInput;
	    }
	    
	    public function addMonthInput($id, $parent = null)
	    {
		    $monthInput = new MonthInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($monthInput);
			}
			else
			{
			    $this->addChildElement($monthInput);
			}
		    
		    return $monthInput;
	    }

	    public function addNumberInput($id, $parent = null)
	    {
		    $numberInput = new NumberInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($numberInput);
			}
			else
			{
			    $this->addChildElement($numberInput);
			}
		    
		    return $numberInput;
	    }
	    
	    public function addRangeInput($id, $parent = null)
	    {
		    $rangeInput = new RangeInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($rangeInput);
			}
			else
			{
			    $this->addChildElement($rangeInput);
			}
		    
		    return $rangeInput;
	    }

	    public function addSearchInput($id, $parent = null)
	    {
		    $searchInput = new SearchInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($searchInput);
			}
			else
			{
			    $this->addChildElement($searchInput);
			}
		    
		    return $searchInput;
	    }

	    public function addTelephoneNumberInput($id, $parent = null)
	    {
		    $telInput = new TelephoneInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($telInput);
			}
			else
			{
			    $this->addChildElement($telInput);
			}
		    
		    return $telInput;
	    }

	    public function addTimeInput($id, $parent = null)
	    {
		    $timeInput = new TimeInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($timeInput);
			}
			else
			{
			    $this->addChildElement($timeInput);
			}
		    
		    return $timeInput;
	    }

	    public function addUrlInput($id, $parent = null)
	    {
		    $urlInput = new UrlInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($urlInput);
			}
			else
			{
			    $this->addChildElement($urlInput);
			}
		    
		    return $urlInput;
	    }

	    public function addWeekInput($id, $parent = null)
	    {
		    $weekInput = new WeekInput($id);
		    
			if ($parent != null)
			{
				$parent->addChildElement($weekInput);
			}
			else
			{
			    $this->addChildElement($weekInput);
			}
		    
		    return $weekInput;
	    }

	    public function addLabeledInput($inputId, $labelText, $inputType, $parent = null)
	    {
			$labeledInput = new LabeledInput($inputId);

			$labeledInput->setLabelText($labelText);
			$labeledInput->setInputType($inputType);
		    
		    if ($parent != null)
		    {
				$labeledInput->setParent($parent);
		    }
		    else
		    {
				$labeledInput->setParent($this);
			}
			
			return $labeledInput;
	    }
    }
    
    class FormElement extends FieldSet
    {
	    public static $sm_formUrlEncoded = "application/x-www-form-urlencoded";
	    public static $sm_formMultipart = "multipart/form-data";
	    public static $sm_textPlain = "text/plain";
	    
	    private $m_validEncodings = array();
	    
	    public function __construct($id)
	    {
		    parent::__construct(null);
		    
			$this->setElementName(FORM);
			$this->setId($id);
			
			$this->setMethod(true); // default to post
			
			$this->m_validEncodings[] = self::$sm_formUrlEncoded;
			$this->m_validEncodings[] = self::$sm_formMultipart;
			$this->m_validEncodings[] = self::$sm_textPlain;
			$this->addAttribute('accept-charset', "utf-8");
	    }
	    
	    public function setEncodingType($encodingType)
	    {
		    $method = $this->getAttribute(METHOD);
		    
		    // see if it has a method yet, if it does, make sure it is "POST"
		    if (($method == null) || ($method == "POST"))
		    {
			    foreach ($this->m_validEncodings as $validEncoding)
			    {
				    if (strcmp($encodingType, $validEncoding) == 0)
				    {
						$this->addAttribute(ENCTYPE, $encodingType);
					    break;
				    }
			    }
			}
	    }
	        
		public function addLegend($legendText)
		{
			// not allowed/supported/etc.
		}

		public function setMethod($isPost)
		{
			if ($isPost == true)
			{
				$this->addAttribute(METHOD, POST);
			}
			else
			{
				$this->addAttribute(METHOD, GET);
				$this->removeAttribute(ENCTYPE); // nix it if it is there
			}
		}
		
	    public function setAction($action)
	    {
		    $this->addAttribute(ACTION, $action);
	    }
	    
	    public function addFieldSet($id)
	    {
		    $childElement = new FieldSet($id);
		    
		    $this->addChildElement($childElement);
		    
		    return $childElement;
	    }
    }

}
?>