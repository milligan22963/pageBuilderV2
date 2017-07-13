function BaseElement(nodeType)
{
    this.object = null;
	this.parent = new Array(); // we only want one of these inherited
    this.nodeType = nodeType;
    this.afmName = "afm";
}
BaseElement.prototype = 
{
    initialize : function(nodeType)
    {
        this.object = document.createElement(nodeType);
    },
    setText : function(text)
    {
        this.object.textContent = text;
    },
    getText : function()
    {
        return this.object.textContent;
    },
    setId : function(id)
    {
        this.object.id = id;
    },
    getId : function()
    {
        return this.object.id;
    },
    setAction : function(action)
    {
        this.object.addEventListener("change", action);
    },
    setClickHandler : function(action)
    {
        this.object.addEventListener("click", action);
    },
    appendChild : function(child)
    {
        this.object.appendChild(child.object);
    },
    prependChild : function(child)
    {
        this.object.insertBefore(child.object, this.object.firstChild);
    },
    removeChild : function(child)
    {
        this.object.removeChild(child.object);
    },
    addAttribute : function(name, value)
    {
        this.object.setAttribute(name, value);
    },
    setTooltip : function(tooltip)
    {
        this.addAttribute('title', tooltip);
    },
	addClass: function (newClass)
	{        
        this.object.classList.add(newClass);
	},
    removeClass: function(oldClass)
    {
        this.object.classList.remove(oldClass);
    },
	setWidth : function(width)
	{
        this.object.style.width = width;			
	},
	setHeight : function(height)
	{
        this.object.style.height = height;			
	},
    populate : function(parent)
    {
        if (parent.afmName !== undefined)
        {
            parent.appendChild(this);
        }
        else
        {
            parent.appendChild(this.object);
        }
    }
};

/**
 * DivElement
 */
function DivElement()
{
    this.initialize("div");
}
DivElement.inheritsFrom(BaseElement);

/**
 * SectionElement
 */
function SectionElement()
{
    this.initialize("section");
}
SectionElement.inheritsFrom(BaseElement);

/**
 * FormElement
 */
function FormElement()
{
    this.initialize("form");
}
FormElement.inheritsFrom(BaseElement);

/**
 *  ImageElement
 */
function ImageElement()
{
    this.initialize("img");
}
ImageElement.inheritsFrom(BaseElement);
ImageElement.prototype.setImage = function(image)
{
    this.object.setAttribute("src", image);
};

/**
 * ButtonElement
 */
function ButtonElement()
{
    this.initialize("button");
    this.icon = null;
    this.activeIcon = null;
    this.pressedIcon = null;
    this.disabledIcon = null;
    this.action = null;
    this.checked = false;

    var thisObj = this;

    this.onDown = function(evt)
    {
        if (this.pressedIcon != null)
        {
            thisObj.toggleCheckState();
        }
    };
    this.onClick = function(evt)
    {
        if (thisObj.action != null)
        {
            thisObj.action(thisObj, evt);
        }
    };
}
ButtonElement.inheritsFrom(BaseElement);
ButtonElement.prototype.setActiveIcon = function(icon)
{
    this.activeIcon = icon;
    this.showButton(this.activeIcon); // default is active
};
ButtonElement.prototype.setPressedIcon = function(icon)
{
    this.pressedIcon = icon;
};
ButtonElement.prototype.setDisabledIcon = function(icon)
{
    this.disabledIcon = icon;
};
ButtonElement.prototype.setAction = function(action)
{
    var thisObj = this;
    this.action = action;
    this.setClickHandler(thisObj.onClick);
    this.object.addEventListener("mousedown", thisObj.onDown);
};
ButtonElement.prototype.setCheckState = function(isChecked)
{
    if (this.pressedIcon != null)
    {
        this.checked = isChecked;

        if (this.checked == true)
        {
            this.showButton(this.pressedIcon);
        }
        else
        {
            this.showButton(this.activeIcon);
        }
    }
};
ButtonElement.prototype.getCheckState = function()
{
    return this.checked;
};
ButtonElement.prototype.toggleCheckState = function()
{
    this.setCheckState(this.checked == true ? false : true);
};
ButtonElement.prototype.showButton = function(image)
{
    if ((this.pressedIcon != null) || (image == this.activeIcon))
    {
        if (this.icon == null)
        {
            this.icon = new ImageElement();

            this.icon.setImage(image);

            this.appendChild(this.icon);
        }
        else
        {
            this.icon.setImage(image);
        }
    }
};

/**
 * LabelElement
 */
function LabelElement()
{
    this.initialize("label");
}
LabelElement.inheritsFrom(BaseElement);

/**
 * SpanElement
 */
function SpanElement()
{
    this.initialize("span");
}
SpanElement.inheritsFrom(BaseElement);

/**
 * BreakElement
 */
function BreakElement()
{
    this.initialize("br");
}
BreakElement.inheritsFrom(BaseElement);

/**
 * HorizontalRuleElement
 */
function HorizontalRuleElement()
{
    this.initialize("hr");
}
HorizontalRuleElement.inheritsFrom(BaseElement);

/**
 * CanvasElement
 */
function CanvasElement()
{
    this.initialize("canvas");
}
CanvasElement.inheritsFrom(BaseElement);

/**
 *  InputElement
 */
function InputElement()
{
    this.initialize("input");
}
InputElement.inheritsFrom(BaseElement);
InputElement.prototype.setType = function(type)
{
    this.addAttribute("type", type);
};

/**
 * CheckBoxElement
 */
function CheckBoxElement()
{
    this.setType("checkbox");
}
CheckBoxElement.inheritsFrom(InputElement);

/**
 * FileInputElement
 */
function FileInputElement()
{
    this.setType("file");

    // needs to be added to parent form - override populate however if parent already known, then just add it
    this.addAttribute("enctype", "multipart/form-data");
}
FileInputElement.inheritsFrom(InputElement);

/**
 * TextAreaElement
 */
function TextAreaElement()
{
    this.setType("textarea");
}
TextAreaElement.inheritsFrom(InputElement);

/**
 * ToolbarElement
 */
function ToolbarElement()
{
    this.initialize("div");

    this.addClass('toolbar');
}
ToolbarElement.inheritsFrom(BaseElement);
ToolbarElement.prototype.addIconButton = function(active, pressed, action)
{
    var button = new ButtonElement();

    button.addClass('toolbar');

    button.setActiveIcon(active);
    button.setPressedIcon(pressed);
    button.setAction(action);

    this.appendChild(button);

    return button;
};
ToolbarElement.prototype.addButton = function(text, action)
{
    var button = new ButtonElement();

    button.addClass('toolbar');

    button.setText(text);
    button.setAction(action);

    this.appendChild(button);

    return button;
};

/**
 * StatusBarElement
 */
function StatusBarElement()
{
    this.initialize("div");
    this.addClass('statusbar');
}
StatusBarElement.inheritsFrom(BaseElement);
StatusBarElement.prototype.addSection = function(sectionClass)
{
    var section = new SectionElement();

    section.addClass(sectionClass);
    
    this.appendChild(section);

    return section;
};