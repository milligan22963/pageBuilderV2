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
    setValue : function(value)
    {
        this.object.value = value;
    },
    getValue : function()
    {
        return this.object.value;
    },
    setData : function(data)
    {
        this.object.data = data;
    },
    getData : function()
    {
        return this.object.data;
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
    onMouseUp : function(action, capture)
    {
        this.mouseUpAction = action;
        this.object.addEventListener("mouseup", action, capture);
    },
    onRemoveMouseUp : function()
    {
        this.object.removeEventListener("mouseup", this.mouseUpAction);
        this.mouseUpAction = null;
    },
    onMouseDown : function(action, capture)
    {
        this.mouseDownAction = action;
        this.object.addEventListener("mousedown", action, capture);
    },
    onRemoveMouseDown : function()
    {
        this.object.removeEventListener("mousedown", this.mouseDownAction);
        this.mouseDownAction = null;
    },
    onMouseMove : function(action, capture)
    {
        this.mouseMoveAction = action;
        this.object.addEventListener("mousemove", action, capture);
    },
    onRemoveMouseMove : function()
    {
        this.object.removeEventListener("mousemove", this.mouseMoveAction);
        this.mouseMoveAction = null;
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
    getWidth : function()
    {
        return this.object.style.width;
    },
	setHeight : function(height)
	{
        this.object.style.height = height;			
    },
    getHeight : function()
    {
        return this.object.style.height;
    },
    setTop : function(top)
    {
        this.object.style.top = top;
    },
    getTop : function()
    {
        return this.object.style.top;
    },
    setLeft : function(left)
    {
        this.object.style.left = left;
    },
    getLeft : function()
    {
        return this.object.style.left;
    },
    setOffsetTop : function(top)
    {
        this.object.offsetTop = top;
    },
    getOffsetTop : function()
    {
        return this.object.offsetTop;
    },
    setOffsetLeft : function(left)
    {
        this.object.offsetLeft = left;
    },
    getOffsetLeft : function()
    {
        return this.object.offsetLeft;
    },
    setOffsetWidth : function(width)
    {
        this.object.offsetWidth = width;
    },
    getOffsetWidth : function()
    {
        return this.object.offsetWidth;
    },
    setOffsetHeight : function(height)
    {
        this.object.offsetHeight = height;
    },
    getOffsetHeight : function()
    {
        return this.object.offsetHeight;
    },
    enable : function()
    {
        this.object.disabled = false;
    },
    disable : function()
    {
        this.object.disabled = true;
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
 *  HeadingElement
 */
function HeadingElement(depth)
{
    this.initialize('h' + depth);
}
HeadingElement.inheritsFrom(BaseElement);

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
 * LabeledInputElement
 */
function LabeledInputElement(labelText)
{
    this.initialize("div");
    this.inputField = new InputElement();

    var label = new LabelElement();

    label.setText(labelText);

    this.appendChild(label);
    this.appendChild(this.inputField);
}
LabeledInputElement.inheritsFrom(BaseElement);
LabeledInputElement.prototype.setType = function(type)
{
    this.inputField.addAttribute("type", type);
};
LabeledInputElement.prototype.setText = function(text)
{
    this.inputField.setValue(text);
};
LabeledInputElement.prototype.getText = function()
{
    return this.inputField.getValue();
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