/**
 * Dialog object for a dialog window
 * 
 * @param title - the title of the dialog
 * @param buttons - array of buttons for the toolbar if nay
 */
function Dialog(title, buttons)
{
	this.initialize("div"); // based on a div

	this.autoClose = 0;
	this.move = true; // default to moveable
	this.saveDialogPosition = false;
	this.title = null;
	this.titlebar = null;
	this.dialogContent = null;
	this.toolbar = null;
	this.timerId = null;
	this.position = {left:0, top: 0, width: 400, height: 400};
	this.buttons = new Array();

	var thisObj = this;

	this.windowMouseMoveAction = function(evt) { thisObj.mouseMove(evt);};
	this.windowMouseUpAction = function(evt) { thisObj.mouseUp(evt);};
	
	this.addClass('dlg_hidden');
	this.addClass('dlg_background');

	var visualDialog = new DivElement();

	visualDialog.addClass('modal_box');

	this.appendChild(visualDialog);

	this.titlebar = new DivElement();

	this.titlebar.addClass('dlg_title_bar');

	var titleClose = new SpanElement();
	titleClose.setClickHandler(function() {thisObj.hide(); return false; });
	titleClose.addClass('close');

	this.titlebar.appendChild(titleClose);

	this.title = new HeadingElement(3);

	this.title.addClass('dlg_title_label');
	this.title.setText(title);

	this.titlebar.appendChild(this.title);
	visualDialog.appendChild(this.titlebar);

	if (title != null)
	{
		this.setTitle(title);
	}

	var dialogContent = new DivElement();

	dialogContent.addClass('modal_content');

	visualDialog.appendChild(dialogContent);
	this.dialogContent = dialogContent;

	if (buttons != null)
	{
		if (buttons.length > 0)
		{
			var toolbar = new ToolbarElement();

			toolbar.removeClass('toolbar');
			toolbar.addClass('dlg_button_deck'); // add our specific items

			buttons.forEach(function(button)
			{
				var newButton = null;
				if (button.hasOwnProperty('text') == true)
				{
					newButton = toolbar.addButton(button.text, button.action);
				}
				else
				{
					newButton = toolbar.addIconButton(button.active, button.pressed, button.action);
				}
				newButton.setId(button.id);
				thisObj.buttons.push(newButton);
			}, this);
			visualDialog.appendChild(toolbar);
			this.toolbar = toolbar;
		}
	}
	// squirrel it away
	this.visualDialog = visualDialog;

	// callbacks
	this.onInit = function () { };
	this.onClose = function () { };
}
Dialog.inheritsFrom(BaseElement);
Dialog.prototype.setTitle = function(title)
{
	if (this.title != null)
	{
		this.title.setText(title);
	}
};
Dialog.prototype.setWidth = function(width)
{
	this.position.width = width;
};
Dialog.prototype.setHeight = function(height)
{
	this.position.height = height;
};
Dialog.prototype.addContent = function(content)
{
	if (this.dialogContent != null)
	{
		this.dialogContent.appendChild(content);
	}
}
Dialog.prototype.mouseDown = function(evt)
{
	var thisObj = this;
	window.addEventListener("mousemove", thisObj.windowMouseMoveAction, true);
};
Dialog.prototype.mouseUp = function(evt)
{
	var thisObj = this;
	window.removeEventListener("mousemove", thisObj.windowMouseMoveAction, true);
};
Dialog.prototype.mouseMove = function(evt)
{
	var x = parseInt(this.visualDialog.getLeft(), 10);
	var y = parseInt(this.visualDialog.getTop(), 10);

	this.position.left = x + evt.movementX;
	this.position.top = y + evt.movementY;

	this.visualDialog.setTop(this.position.top + 'px');
	this.visualDialog.setLeft(this.position.left + 'px');
};
Dialog.prototype.hide = function()
{
	if (this.move == true)
	{
		if (this.titlebar != null)
		{
			this.titlebar.onRemoveMouseDown();
		}
		else
		{
			this.visualDialog.onRemoveMouseDown(); // if no title
		}
		window.removeEventListener("mouseup", this.windowMouseUpAction, false);
	}

	// save it
	if (this.saveDialogPosition == true)
	{
		saveData(this.getId(), this.position, null);
	}
	this.addClass('dlg_hidden');
	if (this.timerId != null)
	{
		window.clearTimeout(this.timerId);
		this.timerId = null;
	}
};
Dialog.prototype.show = function()
{
	var thisObj = this;

	if (this.move == true)
	{
		if (this.titlebar != null)
		{
			this.titlebar.onMouseDown(function(evt) { thisObj.mouseDown(evt);}, false);
		}
		else
		{
			this.visualDialog.onMouseDown(function(evt) { thisObj.mouseDown(evt);}, false);
		}
		window.addEventListener("mouseup", this.windowMouseUpAction, false);		
	}

	this.removeClass('dlg_hidden');

	// Position after the hidden class is removed so we have data to work with...
	if (this.visualDialog.getLeft() == "")
	{
		var useSaved = false;

		// restore its position/size if needed
		if (this.saveDialogPosition == true)
		{
			var position = restoreData(this.getId());

			if (position != null)
			{
				this.position = position;
				useSaved = true;
			}
		}
		this.visualDialog.setWidth(this.position.width + 'px');
		this.visualDialog.setHeight(this.position.height + 'px');

		// we need to do this after the width height settings so the box will center properly
		if (useSaved == false)
		{
			this.position.left = (window.innerWidth - this.visualDialog.getOffsetWidth()) / 2;
			this.position.top = (window.innerHeight - this.visualDialog.getOffsetHeight()) / 2;
		}

		this.visualDialog.setTop(this.position.top + 'px');
		this.visualDialog.setLeft(this.position.left + 'px');
	}
	
	this.onInit();

	if (this.autoClose != 0)
	{
		// really shouldn't be possible, but hey we can
		if (this.timerId == null)
		{
			this.timerId = window.setTimeout(function() {thisObj.hide();}, thisObj.autoClose);
		}
	}
};
Dialog.prototype.savePosition = function(saveDialogPosition)
{
	this.saveDialogPosition = saveDialogPosition;
};
Dialog.prototype.autoClose = function(timeDelay)
{
	this.autoClose = timeDelay;
};
Dialog.prototype.canMove = function(move)
{
	this.move = move;
};
Dialog.prototype.setButtonState = function(id, enabled)
{
	this.buttons.forEach(function(button)
	{
		if (button.getId() == id)
		{
			console.log('foundit');
			if (enabled == false)
			{
				button.disable();
			}
			else
			{
				button.enable();
			}
		}
	}, this);
};