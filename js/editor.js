function TextEditor(contentId, themeSelection)
{
    this.changeAction = null;
    this.themeSelection = themeSelection;
    this.contentId = contentId;

    this.editorContent = new DivElement();

    var editorToolbar = new DivElement();

    editorToolbar.setId(contentId + '_editor_toolbar');

    var button = new ButtonElement();
    button.addClass('ql-bold');
    editorToolbar.appendChild(button);

    button = new ButtonElement();
    button.addClass('ql-italic');
    editorToolbar.appendChild(button);

    this.editorContent.appendChild(editorToolbar);

    var editor = new DivElement();
    editor.setId(contentId + '_editor');

    this.editorContent.appendChild(editor);

    this.setText = function(text)
    {
        this.quillEditor.setText(text);
    };
    this.getText = function()
    {
        return this.quillEditor.getText();
    };
    this.getEditorContent = function()
    {
        return this.editorContent;
    };
    this.populate = function()
    {
        var thisObj = this;

        this.quillEditor = new Quill('#' + this.contentId + '_editor',
        {
            modules: { toolbar: '#' + this.contentId + '_editor_toolbar' },
            theme: this.themeSelection
        });

        if (this.changeAction != null)
        {
            this.quillEditor.on('text-change', function(delta, oldDelta, source)
            {
                if (source == 'user')
                {
                    if (thisObj.changeAction != null)
                    {
                        thisObj.changeAction(thisObj.quillEditor);
                    }
                }
            });
        }
    };
    this.setAction = function(action)
    {
        this.changeAction = action;
    };
}