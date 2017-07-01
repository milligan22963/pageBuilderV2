/**
 *  comment entry
 */
function CommentEntry(objectId, text)
{
    this.initialize(objectId, null, null);
    this.commentText = text;
    this.parentId = 0;
    this.authorId = 0;
    this.commentId = 0;
}
CommentEntry.inheritsFrom(DivElement);
CommentEntry.prototype.setParentId = function(parentId)
{
    this.parentId = parentId;
};
CommentEntry.prototype.setAuthorId = function(authorId)
{
    this.authorId = authorId;
};
CommentEntry.prototype.getAuthorId = function()
{
    return this.authorId;
};
CommentEntry.prototype.setId = function(id)
{
    this.commentId = id;
};
CommentEntry.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
        this.addClass('simple_blog_comment_section');

        // create myself
        this.parent[this['CommentEntry']].populate.call(this, parentId);

        var commentLabel = new LabelElement(this.objectId + '_lbl', this.commentText, null);

        commentLabel.addClass('simple_blog_comment');
        commentLabel.populate(this.objectId);
    }
};

/**
 *  blog entry
 */
function BlogEntry(objectId, toolTip)
{
    this.initialize(objectId, null, null);

    this.entryId = 0;
    this.authorId = 0;
    this.currentUserId = 0;
    this.tooltip = null;
    this.title = null;
    this.content = null;
    this.comments = new Array();
    this.url = null;
    this.extPath = null;
}
BlogEntry.inheritsFrom(DivElement);
BlogEntry.prototype.setToolTip = function(tooltip)
{
    this.tooltip = tooltip;
};
BlogEntry.prototype.setTitle = function(title)
{
    this.title = title;
};
BlogEntry.prototype.setContent = function(content)
{
    this.content = content;
};
BlogEntry.prototype.addComment = function(comment)
{
    this.comments.push(comment);
};
BlogEntry.prototype.setBlogEntryId = function(id)
{
    this.entryId = id;
};
BlogEntry.prototype.setAuthorId = function(id)
{
    this.authorId = id;
};
BlogEntry.prototype.setCurrentUserId = function(id)
{
    this.currentUserId = id;
};
BlogEntry.prototype.setUrl = function(url)
{
    this.url = url;
};
BlogEntry.prototype.setExtOption = function(extPath)
{
    this.extPath = extPath;
};
BlogEntry.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
        this.addClass('blog_entry');

        if (this.tooltip != null)
        {
            addAttributes(this.objectId, {'title':this.tooltip});
        }

        var blogContentId = this.objectId + '_title';

        var blogContent = new DivElement(blogContentId);

        this.addChild(blogContent);

        // now add in the children such as title, summary, etc
        var headingElement = new LabelElement(this.objectId + '_title_label', this.title, null);

        headingElement.addClass('simple_blog_title');

        blogContent.addChild(headingElement);

        var hRule = new HorizontalRuleElement(this.objectId + '_hr');

        hRule.addClass('simple_blog_hr');

        this.addChild(hRule);

        var contentLabel = new LabelElement(this.objectId + '_content', this.content, null);

        contentLabel.addClass('simple_blog_content');

        this.addChild(contentLabel);

        // add in edit toolbar if required
        if (this.authorId == this.currentUserId)
        {
            var thisObj = this;

            var toolBar = new ToolbarElement(this.objectId + '_toolbar', false, null);

            var button = new ButtonElement(this.objectId + '_edit', "Edit", function () {showEditor('simple_blog_edit_dialog', false, thisObj.entryId, thisObj.objectId, thisObj.url, thisObj.extPath);});

            toolBar.addChild(button);

            button = new ButtonElement(this.objectId + '_delete', "Delete", function () {deleteBlogData(thisObj.url, thisObj.extPath, thisObj.entryId, thisObj.objectId);});

            toolBar.addChild(button);

            button = new ButtonElement(this.objectId + '_comment', "Comment", null);

            toolBar.addChild(button);
            
            this.addChild(toolBar);
        }

        var commentRule = new HorizontalRuleElement(this.objectId + '_cmmt_hr');
        commentRule.addClass('simple_blog_hr');

        this.addChild(commentRule);

        this.comments.forEach(function(comment)
        {
            this.addChild(comment);

            // add toolbar for comment
            if (this.currentUserId == comment.getAuthorId())
            {
                var toolbar = new ToolbarElement(this.objectId + '_comment_toolbar', false, null);

                var button = new ButtonElement(this.objectId + '_cmt_edit', "Edit", null);

                toolbar.addChild(button)

                button = new ButtonElement(this.objectId + '_cmt_delete', "Delete", null);
                toolbar.addChild(button)

                button = new ButtonElement(this.objectId + '_cmt_comment', "Comment", null);

                toolbar.addChild(button);

                this.addChild(toolbar);
            }
        }, this);

        // create myself
        this.parent[this['BlogEntry']].populate.call(this, parentId);
    }
}

function addBlogComment(url, extOption, blogId, parentBlogId, objectId)
{
    
}
function deleteBlogData(url, extOption, blogId, objectId)
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
    request.addValue(new nameValuePair('extOption', extOption));
    request.addValue(new nameValuePair('blogId', blogId));

    request.send();

}

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
                var blogEntry = new BlogEntry('blog_entry_' + entry.id, entry.summary);

                blogEntry.setTitle(entry.title);
                blogEntry.setContent(entry.content);
                blogEntry.setBlogEntryId(entry.id);
                blogEntry.setCurrentUserId(decodedData.userId);
                blogEntry.setAuthorId(entry.authorId);
                blogEntry.setExtOption(thisObj.extOption);
                blogEntry.setUrl(thisObj.url);

                entry.comments.forEach(function(comment)
                {
                    var commentEntry = new CommentEntry('blog_entry_comment_' + comment.id, comment.comment);

                    commentEntry.setParentId(comment.parentComment);
                    commentEntry.setId(comment.id);
                    commentEntry.setAuthorId(comment.authorId);

                    commentEntry.addClass('simple_blog_comment');

                    blogEntry.addComment(commentEntry);
                });

                blogEntry.populate(thisObj.parentContainerId);
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

/**
 * SMDEElement
 */
function SMDEElement(objectId, text, action)
{
	this.initialize(objectId, text, action);

    this.simplemde = null;

    // already populated?
    var element = document.getElementById(this.objectId);
    if (element != null)
    {
        this.simplemde = element.data;
    }
}
SMDEElement.inheritsFrom(DialogObject);
SMDEElement.prototype.populate = function(parentId)
{
	if (this.isPopulated == false)
	{
		// create myself first
		var childEntry = '<textarea id="' + this.objectId + '" class="editor"></textarea>';

		appendHtml(parentId, childEntry, false);

        this.simplemde = new SimpleMDE(
        {
            element: document.getElementById(this.objectId),
            spellChecker: true,
            lineWrapping: true,
            placeholder: "Enter text here...",
            autoDownloadFontAwesome: true // for now
        });

        var element = document.getElementById(this.objectId);
        element.data = this.simplemde;

        if (this.action != null)
        {
            this.simplemde.codemirror.on("change", this.action);
        }

		// add my children to myself
		this.parent[this['SMDEElement']].populate.call(this, this.objectId);
	}
};

function saveBlogEntry(smdeTextEditor, showFuncObj)
{
    this.smdeTextEditor = smdeTextEditor;
    this.showFuncObj = showFuncObj;

    // grab title
    var title = getValue(showFuncObj.titleId);
    var summary = getValue(showFuncObj.summaryId);

    // grab content
    var content = smdeTextEditor.value();

    var thisObj = this;

    this.handleResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            // clear it
            thisObj.smdeTextEditor.toTextArea();

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
    request.addValue(new nameValuePair('blogId', thisObj.showFuncObj.entryId));
    request.addValue(new nameValuePair('blogTitle', title));
    request.addValue(new nameValuePair('blogSummary', summary));
    request.addValue(new nameValuePair('blogContent', content));
    // userId is based on the current user logged in and what is in the db already

    request.send();
}

function cancelBlogEntry(showFuncObj, smdeTextEditor)
{
    setValue(showFuncObj.titleId, "");
    setValue(showFuncObj.summaryId, "");
    smdeTextEditor.value("");
}

function showEditor(targetId, newEntry, entryId, objectId, siteUrl, path)
{
	this.dialogObj = null;
    this.titleText = null;
    this.summaryText = null;
    this.blogContent = null;
    this.entryId = entryId;
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

        if (thisObj.textEditor.simplemde.value() != thisObj.blogContent)
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
        thisObj.textEditor.simplemde.value(decodedData.data.content);
    };

    if (newEntry == true)
    {
         this.dialogObj = new Dialog(targetId, "New Entry");
    }
    else
    {
        this.dialogObj = new Dialog(targetId, "Edit Entry");

        // set up init script
        this.dialogObj.initCall = function()
        {
            var request = new transferJSON(true, siteUrl, thisObj.handleResponse);

            request.addValue(new nameValuePair('userOption', 'getBlogData')); // could extract this and pass in as part of the url
            request.addValue(new nameValuePair('extOption', path));
            request.addValue(new nameValuePair('blogId', entryId));

            request.send();
        };
    }

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

    this.textEditor = new SMDEElement(targetId + '_editor', entryId, thisObj.processChange);

    form.addChild(textEditor);

    var toolbar = new ToolbarElement(targetId + '_toolbar', false, null);

	toolbar.addButton("blog_ok_btn", "Ok", function() { saveBlogEntry(thisObj.textEditor.simplemde, thisObj);});
	toolbar.addButton("blog_cancel_btn", "Cancel", function() { cancelBlogEntry(thisObj, thisObj.textEditor.simplemde); thisObj.dialogObj.hide(); });

    this.dialogObj.setButtonDeck(toolbar);

	this.dialogObj.populate(targetId);

    disableElement("blog_ok_btn");

	this.dialogObj.show();
}