/**
 * DialogObject
 * 
 * NOTE: requires inheritance.js
 * 			and tools.js
 */
function DialogObject()
{
	this.parent = new Array(); // we only want one of these inherited
	this.isPopulated = false;
	this.objectId = 0;
	this.text = null;
	this.action = null;
	this.children = null; // create in initialize
	this.properties = {};
	this.classData = null; // create in initialize
	this.debugMode = false;
	this.width = 0;
	this.height = 0;
}

DialogObject.prototype = 
{
	getId: function()
	{
		return this.objectId;
	},

	initialize: function(objectId, text, action)
	{
		this.objectId = objectId;
		this.text = text;
		this.action = action;
		this.children = new Array();
		this.classData = new Array();
		this.properties = {};
	},

	addChild: function(childField)
	{
		this.children.push(childField);
	},

	populate: function(parentId)
	{
		// populate my children
		if (this.isPopulated == false)
		{
			if (this.debugMode == true)
			{
				console.log('I am: ' + this.constructor.name);
			}

			for (var index = 0; index < this.children.length; index++)
			{
				this.children[index].populate(parentId);			
			}

			// at this point the object should exist in the document tree
			var displayObj = document.getElementById(this.objectId);
	
			for (var classIndex = 0; classIndex < this.classData.length; classIndex++)
			{
				var classes = this.classData[classIndex].split(" ");

				classes.forEach(function(classDef)
				{
					displayObj.classList.add(classDef);
				}, this);
			}

			// if any properties, then assign them
			if (isObjEmpty(this.properties) == false)
			{
				addAttributes(this.objectId, this.properties);
			}

			if (this.width != 0)
			{
				displayObj.style.width = this.width;
			}

			if (this.height != 0)
			{
				displayObj.style.height = this.height;
			}
		}
		this.isPopulated = true;
	},

	setProperty: function (property, value)
	{
		var displayObj = document.getElementById(this.objectId);

		if (this.isPopulated == true)
		{
			displayObj.setAttribute(property, value);
		}
		else
		{
			this.properties[property] = value;
		} 
	},

	hasClass: function (queryClass)
	{
		var displayObj = document.getElementById(this.objectId);
		
		return displayObj.classList.contains(queryClass);
	},

	addClass: function (newClass)
	{
		if (this.isPopulated == true)
		{
			var displayObj = document.getElementById(this.objectId);
			
			displayObj.classList.add(newClass);
		}
		else
		{
			this.classData.push(newClass);
		}
	},
	
	removeClass: function (oldClass)
	{
		if (this.isPopulated == true)
		{
			var displayObj = document.getElementById(this.objectId);
			
			displayObj.classList.remove(oldClass);
		}
	},

	toggleClass: function (toggleClass)
	{
		if (this.isPopulated == true)
		{
			var displayObj = document.getElementById(this.objectId);
			
			displayObj.classList.toggle(toggleClass);
		}
	},

	setWidth : function(width)
	{
		if (this.isPopulated == true)
		{
			var displayObj = document.getElementById(this.objectId);
			
			displayObj.style.width = width;			
		}
		else
		{
			this.width = width;
		}
	},

	setHeight : function(height)
	{
		if (this.isPopulated == true)
		{
			var displayObj = document.getElementById(this.objectId);
			
			displayObj.style.height = height;			
		}
		else
		{
			this.height = height;
		}
	}
};

/**
 * Label
 */
function LabelElement(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
LabelElement.inheritsFrom(DialogObject);
LabelElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<label id="' + this.objectId + '">';
		childEntry += this.text + '</label>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['LabelElement']].populate.call(this, this.objectId);
	}
};

/**
 * Form
 */
function FormElement(objectId, action)
{
	this.initialize(objectId, 'none', action);
}
FormElement.inheritsFrom(DialogObject);
FormElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<form id="' + this.objectId + '"></form>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['FormElement']].populate.call(this, this.objectId);
	}
};

/**
 * Button
 */
function ButtonElement(objectId, text, action)
{
	this.initialize(objectId, text, action);
	this.icon = null;
}
ButtonElement.inheritsFrom(DialogObject);
ButtonElement.prototype.setIcon = function(icon)
{
	this.icon = icon;
};
ButtonElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<button id="' + this.objectId + '" type="button">';

		if (this.icon != null)
		{
			childEntry += '<img src="' + this.icon + '"/>';
		}
		else
		{
			childEntry += this.text;
		}
		childEntry += '</button>'

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['ButtonElement']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			addEventHandler(this.objectId, 'click', this.action);
		}
	}
};

/**
 * Break
 */
function BreakElement(objectId)
{
	this.initialize(objectId, 'none', null);
}
BreakElement.inheritsFrom(DialogObject);
BreakElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<br id="' + this.objectId + '"/>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['BreakElement']].populate.call(this, this.objectId);
	}
};

/**
 * HorizontalRule
 */
function HorizontalRuleElement(objectId)
{
	this.initialize(objectId, 'none', null);
}
HorizontalRuleElement.inheritsFrom(DialogObject);
HorizontalRuleElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<hr id="' + this.objectId + '"/>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['HorizontalRuleElement']].populate.call(this, this.objectId);
	}
};

/**
 * Canvas
 */
function CanvasElement(objectId, action)
{
	this.initialize(objectId, 'none', action);
}
CanvasElement.inheritsFrom(DialogObject);
CanvasElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<canvas id="' + this.objectId + '"></canvas>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['CanvasElement']].populate.call(this, this.objectId);
	}
};

/**
 * CheckBox
 */
function CheckBoxElement(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
CheckBoxElement.inheritsFrom(DialogObject);
CheckBoxElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<input type="checkbox" id="' + this.objectId + '"/>';

		if (this.text != null)
		{
			childEntry += '<label for="' + this.objectId + '">' + this.text + '</label>';
		}

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['CheckBoxElement']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			addEventHandler(this.objectId, 'click', this.action);
		}
	}
};

/**
 * Selection
 */
function SelectionElement(objectId, text, action)
{
	this.selections = new Array();

	this.initialize(objectId, text, action);
}
SelectionElement.inheritsFrom(DialogObject);
SelectionElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<label for="' + this.objectId + '" id="' + this.objectId + '_label">';
		childEntry += this.text  + ':</label>';
		childEntry += '<select id="' + this.objectId + '" name="' + this.objectId + '">';
		for (var option = 0; option < this.selections.length; option++)
		{
			childEntry += '<option value="' + this.selections[option].value + '">' + this.selections[option].name + '</option>'; 
		}
		childEntry += '</select>';
		
		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['SelectionElement']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			addEventHandler(this.objectId, 'change', this.action);
		}
	}
};

SelectionElement.prototype.addSelection = function(name, value)
{
	if (this.isPopulated == false)
	{
		var selectionObject = {'name': name, 'value': value};
		this.selections.push(selectionObject);
	}
	else
	{
		appendHtml(parentId, '<option value="' + value + '">' + name + '</option>', false);
	}
};

/**
 * Section
 */
function SectionElement(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
SectionElement.inheritsFrom(DialogObject);
SectionElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<section id="' + this.objectId + '">';

		if (this.text != null)
		{
			childEntry += "<label>" + this.text + "</label>";
		}

		childEntry += '</section>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['SectionElement']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			addEventHandler(this.objectId, 'click', this.action);
		}
	}
};

/**
 * Svg
 */
function SvgElement(objectId, action)
{
	this.initialize(objectId, null, action);
}
SvgElement.inheritsFrom(DialogObject);
SvgElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<svg id="' + this.objectId + '"/>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['SvgElement']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			// assign the action of the button
			addEventHandler(this.objectId, 'click', this.action);
		}
	}
};

/**
 * Input
 */
function InputElement(objectId, text, inputType, action)
{
	this.inputType = inputType;

	this.initialize(objectId, text, action);
}
InputElement.inheritsFrom(DialogObject);
InputElement.prototype.setType = function(type) { this.inputType = type;};
InputElement.prototype.setReadOnly = function(readOnly) { this.setOption("readonly", true);};
InputElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		var childEntry = '<label for="' + this.objectId + '" id="' + this.objectId + '_label">';
		childEntry += this.text  + ':</label><input type="' + this.inputType;
		childEntry += '"  id="' + this.objectId + '"/>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['InputElement']].populate.call(this, this.objectId);

		if (this.action != null)
		{
			addEventHandler(this.objectId, 'change', this.action);
		}
	}
};

/**
 * FileInput
 */
function FileInputElement(objectId, text, parentFormId, action)
{
	this.parentFormId = parentFormId;

	this.initialize(objectId, text, action);

	this.setType("file");
}
FileInputElement.inheritsFrom(InputElement);
FileInputElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create the input field
		this.parent[this['FileInputElement']].populate.call(this, this.objectId);

		// ensure form has proper encoding type 				//enctype="multipart/form-data"
		addAttributes(this.objectId, {enctype:"multipart/form-data"});
	
//		$('#' + this.parentFormId).attr("enctype", "multipart/form-data");

		if (this.action != null)
		{
			addEventHandler(this.objectId, 'change', this.action);
		}
	}
};

/**
 * TextArea
 */
function TextAreaElement(objectId, text, action)
{
	this.initialize(objectId, text, action);

	this.setType("textarea");
}
TextAreaElement.inheritsFrom(InputElement);

/**
 * DivElement
 */
function DivElement(objectId, action)
{
	this.initialize(objectId, null, action);
}
DivElement.inheritsFrom(DialogObject);
DivElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<div id="' + this.objectId + '"></div>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['DivElement']].populate.call(this, this.objectId);
	}
};

/**
 * HeaderElement
 */
function HeaderElement(objectId, action)
{
	this.initialize(objectId, null, action);
}
HeaderElement.inheritsFrom(DialogObject);
HeaderElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<header id="' + this.objectId + '"></header>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['HeaderElement']].populate.call(this, this.objectId);
	}
};

/**
 * FooterElement
 */
function FooterElement(objectId, action)
{
	this.initialize(objectId, null, action);
}
FooterElement.inheritsFrom(DialogObject);
FooterElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<footer id="' + this.objectId + '"></footer>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['FooterElement']].populate.call(this, this.objectId);
	}
};

/**
 * HeadingElement
 */
function HeadingElement(objectId, text, action)
{
	this.initialize(objectId, text, action);
	this.level = 1;
}
HeadingElement.inheritsFrom(DialogObject);
HeadingElement.prototype.setLevel = function(level) { this.level = level;};
HeadingElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<h' + this.level + ' id="' + this.objectId + '">';

		if (this.text != null)
		{
			childEntry += this.text;
		}
		childEntry += '</h' + this.level + '>';

		appendHtml(parentId, childEntry, false);

		// add my children to myself
		this.parent[this['HeadingElement']].populate.call(this, this.objectId);
	}
};

/**
 * Span
 */
function SpanElement(objectId, text, action)
{
	this.initialize(objectId, text, action);
}
SpanElement.inheritsFrom(DialogObject);
SpanElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<span id="' + this.objectId + '">';
		
		if (this.text != null)
		{
			childEntry += this.text;
		}
		childEntry += '</span>';

		appendHtml(parentId, childEntry, false);

		if (this.action != null)
		{
			addEventHandler(this.objectId, 'click', this.action);
		}

		// add my children to myself
		this.parent[this['SpanElement']].populate.call(this, this.objectId);
	}
};

/**
 * ToolbarElement
 */
function ToolbarElement(objectId, resize, action)
{
	this.initialize(objectId, null, action);

	this.resize = resize;
	this.buttons = new Array();
}
ToolbarElement.inheritsFrom(DialogObject);
ToolbarElement.prototype.addButton = function(id, name, action)
{
	var fields = {};
	
	if (name != null)
	{
		fields['text'] = name;
	}
	fields['id'] = id;
	fields['click'] = action;
	this.buttons.push(fields);
};
ToolbarElement.prototype.addIconButton = function(id, name, icon, action)
{
	var fields = {};
	
	if (name != null)
	{
		fields['text'] = name;
	}
	fields['id'] = id;
	fields['click'] = action;
	fields['icons'] = {primary : icon};
	
	this.buttons.push(fields);
};
ToolbarElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<div id="' + this.objectId + '" class="toolbar"></div>';

		appendHtml(parentId, childEntry, false);

		if (this.resize == true)
		{
			setStyle(this.objectId, 'resize', 'both');
		}

		this.buttons.forEach(function(buttonObj)
		{
			var button = new ButtonElement(buttonObj.id, buttonObj.text, buttonObj.click);

			if (buttonObj.hasOwnProperty("icons") == true)
			{
				var iconSet = buttonObj.icons;
				if (iconSet.hasOwnProperty("primary") == true)
				{
					button.setIcon(iconSet.primary);
				}
			}
			button.addClass('toolbar_button');
			this.addChild(button);

		}, this);

		// add my children to myself
		this.parent[this['ToolbarElement']].populate.call(this, this.objectId);
	}
};

/**
 * TODO
 * 
 * We need to be able to specify where text aligns around an element such as n,e,w,s or the like
 */

/**
 * Dialog object for a dialog window
 * 
 * @param objectId - the id of the object which this dialog is utilizing i.e. a hidden div
 * @param title - the title of the dialog
 */
function Dialog(objectId, title)
{
	this.container = null;
	this.dialogChildren = new Array();
	this.objectId = objectId;
	this.title = title;
	this.dlgWidth = 0;	/* overrides the base width/height so the container gets the fields and not the overlay */
	this.dlgHeight = 0;
	this.dlgLeft = 0;
	this.dlgTop = 0;
	this.autoClose = 0; /* if non zero then timeout */
	this.resetDialogOnClose = false;
	this.target = null;
	this.submitmethod = null;
	this.modal = true;
	this.savePosition = false;
	this.returnValue = 0;
	this.showEffect = 'slide';
	this.hideEffect = 'slide';
	this.dragElement = null;
	this.resizeActive = false;
	this.buttonDeck = null;
	this.initCall = function () { };
	this.closeWindow = function () { };
	
	this.initialize(objectId, title, null);

	/*
	 * use thisObj as timeout may be calling this
	 */
	var thisObj = this;
	this.show = function()
	{
		thisObj.initCall();

		thisObj.removeClass('modal_dialog');
		if (thisObj.autoClose != 0)
		{
			var autoCloseTimer = setTimeout(thisObj.hide, thisObj.autoClose);
		}

		var dialogEntry = document.getElementById(this.container.objectId);

		// initial placement
		if (dialogEntry.style.top == "")
		{
			var saveObj = getData(thisObj.objectId);
			thisObj.dlgLeft = (window.innerWidth / 2) - (dialogEntry.offsetWidth / 2);
			thisObj.dlgTop = (window.innerHeight / 2) - dialogEntry.offsetHeight;

			if (saveObj != null)
			{
				if (saveObj.xPos != null)
				{
					thisObj.dlgLeft = saveObj.xPos;
				}

				if (saveObj.yPos != null)
				{
					thisObj.dlgTop = saveObj.yPos;
				}

				if ((saveObj.width != null) && (saveObj.width > 0))
				{
					thisObj.dlgWidth = saveObj.width;
					dialogEntry.style.width = thisObj.dlgWidth;
				}

				if ((saveObj.height != null) && (saveObj.height > 0))
				{
					thisObj.dlgHeight = saveObj.height;
					dialogEntry.style.height = thisObj.dlgHeight;
				}
			}
			dialogEntry.style.top = thisObj.dlgTop + "px";
			dialogEntry.style.left = thisObj.dlgLeft + "px";
		}
	};

	this.hide = function()
	{
		if (thisObj.savePosition == true)
		{
			var saveObj = { height: thisObj.dlgHeight, width: thisObj.dlgWidth, xPos: thisObj.dlgLeft, yPos: thisObj.dlgTop};

			saveData(thisObj.objectId, saveObj, 1);
		}
		thisObj.closeWindow(); // call the close
		thisObj.addClass('modal_dialog');
		if (thisObj.resetDialogOnClose == true)
		{
			removeChildren(thisObj.objectId);
		}
	};
	
	this.onDrag = function()
	{
		thisObj.dragElement = document.getElementById(thisObj.container.objectId);
	};

	this.onMove = function(e)
	{
		if (thisObj.dragElement != null)
		{
			var x = parseInt(thisObj.dragElement.style.left, 10);
			var y = parseInt(thisObj.dragElement.style.top, 10);

			thisObj.dlgLeft = x + e.movementX;
			thisObj.dlgTop = y + e.movementY;

			thisObj.dragElement.style.left = thisObj.dlgLeft + "px";
			thisObj.dragElement.style.top = thisObj.dlgTop + "px";
		}
	};

	this.onDrop = function()
	{
		thisObj.dragElement = null;
	};

	this.setShowEffect = function(showEffect)
	{
		this.showEffect = showEffect;
	};

	this.setHideEffect = function(hideEffect)
	{
		this.hideEffect = hideEffect;
	};

}
Dialog.inheritsFrom(DialogObject);
Dialog.prototype.setWidth = function(width)
{
	this.dlgWidth = width;
};
Dialog.prototype.setHeight = function(height)
{
	this.dlgHeight = height;
};
Dialog.prototype.setAutoCloseTimeout = function(timeout)
{
	this.autoClose = timeout;
};
Dialog.prototype.setResetOnClose = function(resetOnClose)
{
	this.resetDialogOnClose = resetOnClose;
};
Dialog.prototype.setSavePosition = function(savePosition)
{
	this.savePosition = savePosition;
};
Dialog.prototype.setButtonDeck = function(buttonDeck)
{
	this.buttonDeck = buttonDeck;
};
Dialog.prototype.populate = function(parentId)
{
	var containerId = this.objectId + '_modal_box';

	this.container = new DivElement(containerId);

	this.container.addClass('modal_box');

	this.container.setWidth(this.dlgWidth);
	this.container.setHeight(this.dlgHeight);

	var thisObj = this;
	var titleBarId = null;

	// add in title bar if desired
	if (this.title != null)
	{
		titleBarId = this.objectId + '_dlg_title_bar';
		var titleBar = new DivElement(titleBarId);

		titleBar.addClass('dlg_title_bar');

		var titleClose = new SpanElement(this.objectId + '_dlg_title_close', null, function() { thisObj.hide(); return false; });
		titleClose.addClass('close');
		titleBar.addChild(titleClose);
		
		var titleLabel = new HeadingElement(this.objectId + '_dlg_title_label', this.title, null); // no action for right now

		titleLabel.setLevel(2);
		titleLabel.addClass('dlg_title_label');

		titleBar.addChild(titleLabel);

		this.container.addChild(titleBar);
	}

	var dialogContent = new DivElement(this.objectId + '_modal_content');
	dialogContent.addClass('modal_content');
	this.container.addChild(dialogContent);

	// add in buttons if desired/required
	if (this.buttonDeck != null)
	{
		this.container.addChild(this.buttonDeck);
	}

	this.dialogChildren.forEach(function(element)
	{
		dialogContent.addChild(element);
	}, this);

	// call the parent object method to add this container in
	this.parent[this['Dialog']].addChild.call(this, this.container);

	// moment of truth
	// should it stay or should it go
	var element = document.getElementById(containerId);
	if (element == null)
	{
		this.parent[this['Dialog']].populate.call(this, this.objectId);

		element = document.getElementById(containerId); // should exist now
	}
	else
	{
		this.isPopulated = true; // already got it covered
	}

	// psuedo resize tracking
	// don't return false as we want it to bubble up
	element.onmousedown = function()
	{
		thisObj.resizeActive = true;
	};

	element.onmouseup = function()
	{
		if (thisObj.resizeActive == true)
		{
			thisObj.dlgWidth = element.clientWidth;
			thisObj.dlgHeight = element.clientHeight;

			thisObj.resizeActive = false;
		}
	};

	// now that we have a dialog we make it move
	if (titleBarId!= null)
	{
		var titleBarElement = document.getElementById(titleBarId);

		titleBarElement.onmousedown = function ()
		{
			thisObj.onDrag();
			return false;
		};

		titleBarElement.onmousemove = function (e)
		{
			thisObj.onMove(e);
			return false;
		};

		var dialog = document.getElementById(this.objectId);
		dialog.onmouseup = function()
		{
			thisObj.onDrop();
			return false;
		};
/*		dialog.onmouseout = function ()
		{
			thisObj.onDrop();
			return false;
		}*/
	}
};
Dialog.prototype.addChild = function(childObj)
{
	this.dialogChildren.push(childObj);
};

/**
 * test
 * 
 * for testing out the module aspects related to this module
 */
function test_elements(targetId)
{
	// does it already exist? we may need to eradicate it
	// unless we have a reset method
	var dialogObj = new Dialog(targetId, "Test");

//	dialogObj.setResetOnClose(true);
	dialogObj.setWidth(400);
	dialogObj.setSavePosition(true);
//	dialogObj.setAutoCloseTimeout(2000);

	var form = new FormElement("orville", null);

	dialogObj.addChild(form);

	var label = new LabelElement("orvlab", "Label Data", null);
	form.addChild(label);

	var breakObj = new BreakElement("orvbreak");
	form.addChild(breakObj);

	form.addChild(new HorizontalRuleElement("orvhr"));

	var buttonObj = new ButtonElement("orvbutt", "Press", function() { alert('pressed'); return false;});
	form.addChild(buttonObj);

	form.addChild(new CanvasElement("orvcanvas", null));
	form.addChild(new BreakElement("orvbreak2"));
	form.addChild(new CheckBoxElement("orvcheck", "Hello", null));

	var sectionObject = new SectionElement("orvsection", "Section", null);
	form.addChild(sectionObject);

	var selectionObject = new SelectionElement("orvselect", "Test Selection", null);
	selectionObject.addSelection('1', "One");
	selectionObject.addSelection('2', "Two");
	selectionObject.addSelection('3', "Three");	
	sectionObject.addChild(selectionObject);

	var secondSectionObject = new SectionElement("orvsection2", "Section2", null);

	secondSectionObject.addChild(new BreakElement("orvbreak3"));
	secondSectionObject.addChild(new InputElement("orvin", "InputText", "text", null));
	secondSectionObject.addChild(new FileInputElement("orvfile", "File", 'orville', null));

	sectionObject.addChild(secondSectionObject);

	dialogObj.addIconButton("ok_btn", "Ok", 'check.png', function() { dialogObj.hide(); });

	dialogObj.populate(targetId);

	dialogObj.show();
}

/**
 * Editor
 * 
 * for testing out the module aspects related to this module
 */
function Editor(targetId)
{
	// does it already exist? we may need to eradicate it
	// unless we have a reset method
	var dialogObj = new Dialog(targetId, "Editor");

	dialogObj.setSavePosition(true);

	var form = new FormElement(targetId + "_form", null);

	dialogObj.addChild(form);

	var buttonDeck = new ToolbarElement("editor_toolbar", true, null);

	buttonDeck.addIconButton("ok_btn", "Ok", 'check.png', function() { dialogObj.hide(); });

	dialogObj.setButtonDeck(buttonDeck);

	dialogObj.populate(targetId);

	dialogObj.show();
}