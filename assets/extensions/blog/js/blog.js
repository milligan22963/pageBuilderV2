function requestBlogData(start, recordCount, url, extOption, parentContainerId)
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
                var blogEntry = new BlogEntry(entry.id);

                blogEntry.setId('blog_entry_' + entry.id);
                blogEntry.setTitle(entry.title);
                blogEntry.setContent(entry.content);
                blogEntry.setTooltip(entry.summary);

                if (entry.authorId == decodedData.userId)
                {
                    blogEntry.allowEditing(thisObj.url, thisObj.extOption, parentContainerId);
                }

                if (decodedData.allowComments == "on")
                {
                    blogEntry.allowComments(thisObj.url, thisObj.extOption, parentContainerId);
                }

                entry.comments.forEach(function(comment)
                {
                    var commentEntry = new CommentEntry(entry.id, comment.id, comment.parentComment, comment.authorId);

                    commentEntry.setId('blog_entry_comment_' + comment.id);
                    commentEntry.setText(comment.comment);

                    commentEntry.addClass('simple_blog_comment_section');

                    if (comment.authorId == decodedData.userId)
                    {
                        commentEntry.allowEditing(thisObj.url, thisObj.extOption, parentContainerId);
                    }

                    if (decodedData.allowComments == "on")
                    {
                        commentEntry.allowComments(thisObj.url, thisObj.extOption, parentContainerId);
                    }

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
function saveBlogEntry(showFuncObj)
{
    this.editorInstance = showFuncObj.editorInstance;
    this.showFuncObj = showFuncObj;

    // grab title
    var title = showFuncObj.titleElement.getText();
    var summary = showFuncObj.summaryElement.getText();

    // grab content
    var content = this.editorInstance.getText();

    var thisObj = this;

    this.handleResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            // clear it
            thisObj.editorInstance.setText("");

            // don't hide until we get back a good response
            thisObj.showFuncObj.dialogObj.hide(); 

            thisObj.showFuncObj.blogEntry.setTitle(title);
            thisObj.showFuncObj.blogEntry.setContent(content);
            thisObj.showFuncObj.blogEntry.setTooltip(summary);
        }
        else
        {
            alert(decodedData.reason);
        }
    };

    // save
    var request = new transferJSON(true, thisObj.showFuncObj.siteUrl, thisObj.handleResponse);

    request.addValue(new nameValuePair('userOption', 'saveBlogData'));
    request.addValue(new nameValuePair('extOption', thisObj.showFuncObj.path));
    request.addValue(new nameValuePair('blogId', thisObj.showFuncObj.blogEntry.getId()));
    request.addValue(new nameValuePair('blogTitle', title));
    request.addValue(new nameValuePair('blogSummary', summary));
    request.addValue(new nameValuePair('blogContent', content));
    // userId is based on the current user logged in and what is in the db already

    request.send();
}

function deleteBlogEntry(blogEntry, blogId, url, extPath, objectId)
{
    this.blogEntry = blogEntry;
    var thisObj = this;

    this.processResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            var replacement = thisObj.blogEntry.cloneNode(false);

            thisObj.blogEntry.parentNode.replaceChild(replacement, thisObj.blogEntry);
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

// keep it around for reuse
function BlogEntryEditorInstance(blogEntry, siteUrl, path)
{
    this.objectId = "simple_blog_editor";
    this.blogEntry = blogEntry;
    this.siteUrl = siteUrl;
    this.path = path;
    this.dialogObj = null;
    this.titleElement = null;
    this.summaryElement = null;
    this.editorInstance = null;

    this.currentEntries = {title:null, summary:null, content: null};

    var blogEntryObj = this;

    this.processChange = function(element)
    {
        var change = false;

        if (blogEntryObj.currentEntries.title != blogEntryObj.titleElement.getValue())
        {
            change = true;
        }

        if (blogEntryObj.editorInstance.getText() != blogEntryObj.currentEntries.content)
        {
            change = true;
        }

        if (blogEntryObj.currentEntries.summary != blogEntryObj.summaryElement.getValue())
        {
            change = true;
        }

        change == true ? enableElement('blog_ok_btn') : disableElement('blog_ok_btn');
    };

    this.handleResponse = function(decodedData)
    {
        blogEntryObj.currentEntries.title = decodedData.data.title;
        blogEntryObj.currentEntries.summary = decodedData.data.summary;
        blogEntryObj.currentEntries.content = decodedData.data.content;

        blogEntryObj.titleElement.setText(decodedData.data.title);
        blogEntryObj.summaryElement.setText(decodedData.data.summary);
        blogEntryObj.editorInstance.setText(decodedData.data.content);
    };
    
    this.setBlogEntry = function(blogEntry)
    {
        this.blogEntry = blogEntry;
    };
    this.setTitle = function(title)
    {
        this.dialogObj.setTitle(title);
    };

    this.populate = function(parentElement)
    {
        this.dialogObj.populate(parentElement);
        
        this.editorInstance.populate();
    };

    this.show = function()
    {
        if (this.blogId != 0)
        {
            // set up init script
            this.dialogObj.onInit = function()
            {
                var request = new transferJSON(true, blogEntryObj.siteUrl, blogEntryObj.handleResponse);

                request.addValue(new nameValuePair('userOption', 'getBlogData')); // could extract this and pass in as part of the url
                request.addValue(new nameValuePair('extOption', blogEntryObj.path));
                request.addValue(new nameValuePair('blogId', blogEntryObj.blogEntry.getId()));

                request.send();
            };
        }
        else
        {
            this.dialogObj.onInit = function(){};
            this.titleElement.setText("");
            this.summaryElement.setText("");
            this.editorInstance.setText("");
        }

        // disable ok by default
        disableElement('blog_ok_btn');

        this.dialogObj.show();
    };
    // performed when created...
    var buttons = new Array(
        { id: "blog_ok_btn", text: "Ok", action: function() { saveBlogEntry(blogEntryObj);}},
        { id: "blog_cancel_btn", text: "Cancel", action: function() { cancelBlogEntry(blogEntryObj); blogEntryObj.dialogObj.hide(); }}
    );

    this.dialogObj = new Dialog("Edit", buttons);

    this.dialogObj.setId(this.objectId);

    // title
    this.titleElement = new LabeledInputElement("Title: ");
    this.titleElement.addClass('simple_blog_title');
    this.titleElement.setType("text");
    this.titleElement.setAction(function(element) { blogEntryObj.processChange(element)});

    this.dialogObj.addContent(this.titleElement);

    // summary
    this.summaryElement = new LabeledInputElement('Summary: ');
    this.summaryElement.setType("text");
    this.summaryElement.setAction(function(element) { blogEntryObj.processChange(element)});

    this.dialogObj.addContent(this.summaryElement);

    // the editor
    this.editorInstance = new TextEditor('blog_entry', 'snow');
    this.editorInstance.setAction(function(element) { blogEntryObj.processChange(element)});

    this.dialogObj.addContent(this.editorInstance.getEditorContent());
    this.dialogObj.setWidth(800);
    this.dialogObj.setHeight(400);
}

function showBlogEditor(blogEntry, targetId, siteUrl, path)
{
    var parentElement = document.getElementById(targetId);
    var editorDlgInstance = null;
    var dialogTitle = "Edit Entry";

    if (blogEntry.getId() == 0)
    {
        dialogTitle = "New Entry";
    }

    if (parentElement.data == undefined)
    {
        editorDlgInstance = new BlogEntryEditorInstance(blogEntry, siteUrl, path);

        editorDlgInstance.populate(parentElement);
        parentElement.data = editorDlgInstance;
    }
    else
    {
        editorDlgInstance = parentElement.data;
        editorDlgInstance.setBlogEntry(blogEntry);
    }
    
    editorDlgInstance.setTitle(dialogTitle);
    editorDlgInstance.show();
}

/**
 * @brief comment management
 * 
 * @param {*} targetId 
 * @param {*} commentId 
 * @param {*} blogId 
 * @param {*} parentId 
 * @param {*} siteUrl 
 * @param {*} path 
 */
function showCommentEditor(commentEntry, targetId, commentId, blogId, parentId, siteUrl, path)
{
    this.objectId = "simple_comment_editor";
    this.dialogObj = null;
    this.blogId = blogId;
    this.parentId = parentId;
    this.commentId = commentId;
    this.commentData = "";
    this.url = siteUrl;
    this.extOption = path;
    this.editorInstance = null;
    this.commentEntry = commentEntry;

    var thisObj = this;

    this.handleResponse = function(decodedData)
    {
        thisObj.commentData = decodedData.data.comment;

        thisObj.editorInstance.setText(decodedData.data.comment);
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
 
    var dialogTitle = "Edit Comment";
    if (commentId == 0)
    {
        dialogTitle = "New Comment";
    }

    var currentDlg = document.getElementById(this.objectId);
    if (currentDlg == null)
    {
        var buttons = new Array(
            { id: "comment_ok_btn", text: "Ok", action: function() { saveBlogComment(thisObj);}},
            { id: "comment_cancel_btn", text: "Cancel", action: function() { cancelBlogComment(thisObj); thisObj.dialogObj.hide(); }}
        );

        this.dialogObj = new Dialog(dialogTitle, buttons);

        this.dialogObj.setId(this.objectId);

        this.editorInstance = new TextEditor('blog_comment', 'snow');

        this.dialogObj.addContent(this.editorInstance.getEditorContent());

        this.dialogObj.editorInstance = this.editorInstance;
        this.dialogObj.setData(this.dialogObj);
        this.dialogObj.setWidth(800);
        this.dialogObj.setHeight(400);
        this.dialogObj.populate(document.getElementById(targetId));
        
        this.editorInstance.populate();
    }
    else
    {
        thisObj.dialogObj = currentDlg.data;
        thisObj.dialogObj.setTitle(dialogTitle);
        thisObj.editorInstance = currentDlg.data.editorInstance;
    }

    if (commentId != 0)
    {
        // set up init script
        this.dialogObj.onInit = function()
        {
            var request = new transferJSON(false, siteUrl, thisObj.handleResponse);

            request.addValue(new nameValuePair('userOption', 'getBlogComment')); // could extract this and pass in as part of the url
            request.addValue(new nameValuePair('extOption', path));
            request.addValue(new nameValuePair('commentId', commentId));

            request.send();
        };
    }
    else
    {
        thisObj.dialogObj.onInit = function(){};
        thisObj.editorInstance.setText("");
    }
    thisObj.dialogObj.show();
}
function saveBlogComment(commentEditorObj)
{
    this.commentId = commentEditorObj.commentId;
    this.url = commentEditorObj.url;
    this.extOption = commentEditorObj.extOption;
    this.blogId = commentEditorObj.blogId;
    this.parentId = commentEditorObj.parentId;
    this.textEditor = commentEditorObj.editorInstance;
    this.commentEditorObj = commentEditorObj;

    var thisObj = this;

    this.processResponse = function(decodedData)
    {
        if (decodedData.success == "true")
        {
            if (thisObj.commentEntry != null)
            {
                thisObj.commentEntry.setText(thisObj.textEditor.getText());
            }
            else
            {
                // new comment, if it is approved then we can add it now, otherwise show a note saying it will need to be approved
            }
            // clear it
            thisObj.textEditor.setText("");

            // don't hide until we get back a good repsonse
            thisObj.commentEditorObj.dialogObj.hide(); 
        }
        else
        {
            alert(decodedData.reason);
        }
    };

    var request = new transferJSON(true, url, thisObj.processResponse);

    if (this.commentId == 0)
    {
        request.addValue(new nameValuePair('userOption', 'addBlogComment'));
    }
    else
    {
        request.addValue(new nameValuePair('userOption', 'editBlogComment'));        
    }
    request.addValue(new nameValuePair('extOption', thisObj.extOption));
    request.addValue(new nameValuePair('blogId', thisObj.blogId));
    request.addValue(new nameValuePair('parentId', thisObj.parentId));
    request.addValue(new nameValuePair('commentId', thisObj.commentId));
    request.addValue(new nameValuePair('blogComment', thisObj.editorInstance.getText()));

    request.send();
}
function deleteBlogComment(object, commentId, url, extPath)
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
