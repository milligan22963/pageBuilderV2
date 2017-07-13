function RequestBlogData(start, recordCount, url, extOption, parentContainerId)
{
    this.url = url;
    this.extOption = extOption;

    var thisObj = this;

    this.parentContainerId = parentContainerId;

    this.processResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            // populate each blog entry
            decodedData.data.forEach(function(entry)
            {
                var blogEntry = new BlogEntry(entry.id, entry.authorId, decodedData.userId);

                blogEntry.setId('blog_entry_' + entry.id);
                blogEntry.setTitle(entry.title);
                blogEntry.setContent(entry.content);
                blogEntry.setExtOption(thisObj.extOption);
                blogEntry.setUrl(thisObj.url);
                blogEntry.setTooltip(entry.summary);

                entry.comments.forEach(function(comment)
                {
                    var commentEntry = new CommentEntry(comment.id, comment.parentComment, comment.authorId);

                    commentEntry.setId('blog_entry_comment_' + comment.id);
                    commentEntry.setText(comment.comment);

                    commentEntry.addClass('simple_blog_comment_section');

                    blogEntry.addComment(commentEntry);
                 }, this);

                var parentContainer = document.getElementById(thisObj.parentContainerId);

                blogEntry.populate(parentContainer);
            }, this);
        }
    };

    var request = new transferJSON(true, url, thisObj.processResponse);

    request.addValue(new nameValuePair('userOption', 'getBlogData'));
    request.addValue(new nameValuePair('extOption', extOption));
    request.addValue(new nameValuePair('startRecord', start));
    request.addValue(new nameValuePair('recordCount', recordCount));

    request.send();    
}
function addBlogComment(commentEditorObj)
{
    this.url = commentEditorObj.url;
    this.extOption = commentEditorObj.extOption;
    this.blogId = commentEditorObj.blogId;
    this.parentId = commentEditorObj.parentId;
    this.textEditor = commentEditorObj.textEditor;
    this.commentEditorObj = commentEditorObj;

    var thisObj = this;

    this.processResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            // clear it
            thisObj.textEditor.setContent("");

            // don't hide until we get back a good repsonse
            thisObj.commentEditorObj.dialogObj.hide(); 
        }
        else
        {
            alert(decodedData.reason);
        }
    };

    var request = new transferJSON(true, url, thisObj.processResponse);

    request.addValue(new nameValuePair('userOption', 'addBlogComment'));
    request.addValue(new nameValuePair('extOption', thisObj.extOption));
    request.addValue(new nameValuePair('blogId', thisObj.blogId));
    request.addValue(new nameValuePair('parentId', thisObj.parentId));
    request.addValue(new nameValuePair('blogComment', thisObj.textEditor.getContent()));

    request.send();
}
function deleteBlogComment(url, extPath, commentId, objectId)
{
    this.objectId = objectId;
    var thisObj = this;

    this.processResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            // remove the dialog entry
            removeChildren(thisObj.objectId);

            var element = document.getElementById(thisObj.objectId);
            var parent = element.parentElement;

            parent.removeChild(element);
        }
    };

    var request = new transferJSON(true, url, thisObj.processResponse);

    request.addValue(new nameValuePair('userOption', 'deleteBlogComment'));
    request.addValue(new nameValuePair('extOption', extPath));
    request.addValue(new nameValuePair('commentId', commentId));

    request.send();
}
function cancelBlogComment(commentEditorObj)
{
    commentEditorObj.textEditor.setContent("");
}
function saveBlogEntry(showFuncObj)
{
    this.smdeTextEditor = showFuncObj.textEditor;
    this.showFuncObj = showFuncObj;

    // grab title
    var title = getValue(showFuncObj.titleId);
    var summary = getValue(showFuncObj.summaryId);

    // grab content
    var content = smdeTextEditor.getContent();

    var thisObj = this;

    this.handleResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            // clear it
            thisObj.smdeTextEditor.setContent("");

            // don't hide until we get back a good repsonse
            thisObj.showFuncObj.dialogObj.hide(); 

            setText(thisObj.showFuncObj.objectId + '_title_label', title);
            setTitle(thisObj.showFuncObj.objectId + '_title_label', summary);
            setText(thisObj.showFuncObj.objectId + '_content', content);
        }
        else
        {
            alert(decodedData.reason);
        }
    };

    // save
    var request = new transferJSON(true, thisObj.showFuncObj.url, thisObj.handleResponse);

    request.addValue(new nameValuePair('userOption', 'saveBlogData'));
    request.addValue(new nameValuePair('extOption', thisObj.showFuncObj.path));
    request.addValue(new nameValuePair('blogId', thisObj.showFuncObj.blogId));
    request.addValue(new nameValuePair('blogTitle', title));
    request.addValue(new nameValuePair('blogSummary', summary));
    request.addValue(new nameValuePair('blogContent', content));
    // userId is based on the current user logged in and what is in the db already

    request.send();
}

function deleteBlogEntry(url, extPath, blogId, objectId)
{
    this.objectId = objectId;
    var thisObj = this;

    this.processResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            // remove the dialog entry
            removeChildren(thisObj.objectId);

            var element = document.getElementById(thisObj.objectId);
            var parent = element.parentElement;

            parent.removeChild(element);
        }
    };

    var request = new transferJSON(true, url, thisObj.processResponse);

    request.addValue(new nameValuePair('userOption', 'deleteBlogData'));
    request.addValue(new nameValuePair('extOption', extPath));
    request.addValue(new nameValuePair('blogId', blogId));

    request.send();
}
function cancelBlogEntry(showFuncObj)
{
    setValue(showFuncObj.titleId, "");
    setValue(showFuncObj.summaryId, "");
    showFuncObj.textEditor.setContent("");
}

function showAddCommentEditor(targetId, commentId, blogId, parentId, siteUrl, path)
{
    this.dialogObj = null;
    this.blogId = blogId;
    this.parentId = parentId;
    this.commentData = "";
    this.url = siteUrl;
    this.extOption = path;

    var thisObj = this;

    var dialogTitle = "Edit Comment";
    if (commentId == 0)
    {
        dialogTitle = "New Comment";
    }
    this.dialogObj = new Dialog(targetId, dialogTitle);

    this.handleResponse = function(decodedData)
    {
        thisObj.commentData = decodedData.data.comment;

        thisObj.textEditor.setContent(decodedData.data.comment);
    };

    this.processChange = function(element)
    {
        var change = false;

        if (thisObj.textEditor.getContent() != thisObj.commentData)
        {
            change = true;
        }

        change == true ? enableElement('comment_ok_btn') : disableElement('comment_ok_btn');
    };

    this.dialogObj.setSavePosition(true);

    if (commentId != 0)
    {
        // set up init script
        this.dialogObj.initCall = function()
        {
            var request = new transferJSON(false, siteUrl, thisObj.handleResponse);

            request.addValue(new nameValuePair('userOption', 'getBlogComment')); // could extract this and pass in as part of the url
            request.addValue(new nameValuePair('extOption', path));
            request.addValue(new nameValuePair('commentId', commentId));

            request.send();
        };
    }

	var form = new FormElement(targetId + "_form", null);

	this.dialogObj.addChild(form);

    this.textEditor = new SMDEElement(targetId + '_editor', blogId, thisObj.processChange);

    form.addChild(textEditor);

    var toolbar = new ToolbarElement(targetId + '_toolbar', false, null);

	toolbar.addButton("comment_ok_btn", "Ok", function() { addBlogComment(thisObj);});
	toolbar.addButton("comment_cancel_btn", "Cancel", function() { cancelBlogComment(thisObj); thisObj.dialogObj.hide(); });

    this.dialogObj.setButtonDeck(toolbar);

	this.dialogObj.populate(targetId);

    disableElement("comment_ok_btn");

    this.dialogObj.show();
}

function showEditor(targetId, blogId, objectId, siteUrl, path)
{
	this.dialogObj = null;
    this.titleText = null;
    this.summaryText = null;
    this.blogContent = null;
    this.blogId = blogId;
    this.path = path;
    this.url = siteUrl;
    this.objectId = objectId;

    var thisObj = this;

    this.processChange = function(element)
    {
        var change = false;

        if (thisObj.titleText != getValue(thisObj.titleId))
        {
            change = true;
        }

        if (thisObj.textEditor.getContent() != thisObj.blogContent)
        {
            change = true;
        }

        if (thisObj.summaryText != getValue(thisObj.summaryId))
        {
            change = true;
        }

        change == true ? enableElement('blog_ok_btn') : disableElement('blog_ok_btn');
    };

    this.handleResponse = function(decodedData)
    {
        thisObj.titleText = decodedData.data.title;
        thisObj.summaryText = decodedData.data.summary;
        thisObj.blogContent = decodedData.data.content;

        setValue(thisObj.titleId, decodedData.data.title);
        setValue(thisObj.summaryId, decodedData.data.summary);
        thisObj.textEditor.setContent(decodedData.data.content);
    };

    var buttons = new Array(
        { id: "blog_ok_btn", text: "Ok", action: function() { saveBlogEntry(thisObj);}},
        { id: "blog_cancel_btn", text: "Cancel", action: function() { cancelBlogEntry(thisObj); thisObj.dialogObj.hide(); }}
    );

    if (blogId == 0)
    {
         this.dialogObj = new Dialog("New Entry", buttons);
    }
    else
    {
        this.dialogObj = new Dialog("Edit Entry", buttons);

        // set up init script
        this.dialogObj.initCall = function()
        {
            var request = new transferJSON(true, siteUrl, thisObj.handleResponse);

            request.addValue(new nameValuePair('userOption', 'getBlogData')); // could extract this and pass in as part of the url
            request.addValue(new nameValuePair('extOption', path));
            request.addValue(new nameValuePair('blogId', blogId));

            request.send();
        };
    }

    this.dialogObj.populate(document.getElementById(targetId));
    this.dialogObj.show();
    return;
    this.dialogObj.setSavePosition(true);

	var form = new FormElement(targetId + "_form", null);

	this.dialogObj.addChild(form);

    var divContainer = new DivElement(targetId + '_titlediv');

    form.addChild(divContainer);

    this.titleId = targetId + '_title';
    var titleEntry = new InputElement(this.titleId, "Title", "text", thisObj.processChange);

    divContainer.addChild(titleEntry);

    divContainer = new DivElement(targetId + '_summarydiv');

    form.addChild(divContainer);

    this.summaryId = targetId + '_summary';
    var summary = new InputElement(this.summaryId, "Summary", "text", thisObj.processChange);

    divContainer.addChild(summary);

    this.textEditor = new SMDEElement(targetId + '_editor', blogId, thisObj.processChange);

    form.addChild(textEditor);

    var toolbar = new ToolbarElement(targetId + '_toolbar', false, null);

	toolbar.addButton("blog_ok_btn", "Ok", function() { saveBlogEntry(thisObj);});
	toolbar.addButton("blog_cancel_btn", "Cancel", function() { cancelBlogEntry(thisObj); thisObj.dialogObj.hide(); });

    this.dialogObj.setButtonDeck(toolbar);

	this.dialogObj.populate(targetId);

    disableElement("blog_ok_btn");

	this.dialogObj.show();
}