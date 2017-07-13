/**
 * Dialog object for a dialog window
 * 
 * @param title - the title of the dialog
 * @param buttons - array of buttons for the toolbar if nay
 */
function Dialog(title, buttons)
{
	this.initialize("div"); // based on a div

	this.title = null;
	this.dialogContent = null;
	this.toolbar = null;

	var thisObj = this;
	
	this.addClass('dlg_hidden');
	this.addClass('dlg_background');

	var visualDialog = new DivElement();

	visualDialog.addClass('modal_box');

	this.appendChild(visualDialog);

	if (title != null)
	{
		var titleDiv = new DivElement();

		titleDiv.addClass('dlg_title_bar');

		var titleClose = new SpanElement();
		titleClose.setClickHandler(function() {thisObj.hide(); return false; });
		titleClose.addClass('close');

		titleDiv.appendChild(titleClose);

		this.title = new LabelElement();

		this.title.addClass('dlg_title_label');
		this.title.setText(title);

		titleDiv.appendChild(this.title);
		visualDialog.appendChild(titleDiv);
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
Dialog.prototype.addContent = function(content)
{
	if (this.dialogContent != null)
	{
		this.dialogContent.appendChild(content);
	}
}
Dialog.prototype.hide = function()
{
	this.addClass('dlg_hidden');
};
Dialog.prototype.show = function()
{
	this.removeClass('dlg_hidden');
};