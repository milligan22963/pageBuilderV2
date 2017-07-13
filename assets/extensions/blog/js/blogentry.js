/**
 *  blog entry
 */
function BlogEntry(blogId, authorId, currentUserId)
{
    this.blogId = blogId;
    this.authorId = authorId;
    this.currentUserId = currentUserId;
    this.title = new LabelElement();
    this.content = new LabelElement();
    this.comments = new Array();
    this.url = null;
    this.extPath = null;

    this.addClass('blog_entry');
    this.title.addClass('simple_blog_title');
    this.appendChild(this.title);

    // hr
    var hr = new HorizontalRuleElement();

    hr.addClass('simple_blog_hr');
    this.appendChild(hr);

    // content
    this.content.addClass('simple_blog_content');
    this.appendChild(this.content);

    // add in tool bar if ok
    if (authorId == currentUserId)
    {

    }
    var hr = new HorizontalRuleElement();

    hr.addClass('simple_blog_hr');
    this.appendChild(hr);
}
BlogEntry.inheritsFrom(DivElement);
BlogEntry.prototype.setTitle = function(title)
{
    this.title.setText(title);
};
BlogEntry.prototype.setContent = function(content)
{
    this.content.setText(content);
};
BlogEntry.prototype.addComment = function(comment)
{
    // add it directly to this entry or a comment?
    var parentId = comment.getParentId();
    if (parentId == 0)
    {
        this.appendChild(comment);
    }
    else
    {
        var added = false;
        this.comments.forEach(function(currentComment)
        {
            if (added == false)
            {
                if (currentComment.getCommentId() == parentId)
                {
                    currentComment.appendChild(comment);
                    added = true;
                }
            }
        });
    }
    this.comments.push(comment);
    // add toolbar for comment
/*    if (this.currentUserId == comment.getAuthorId())
    {
        var toolbar = new ToolbarElement(this.objectId + '_comment_toolbar', false, null);

        var button = new ButtonElement(this.objectId + '_cmt_edit', "Edit", null);

        toolbar.addChild(button)

        button = new ButtonElement(this.objectId + '_cmt_delete', "Delete", null);
        toolbar.addChild(button)

        button = new ButtonElement(this.objectId + '_cmt_comment', "Comment", null);

        toolbar.addChild(button);

        this.addChild(toolbar);
    }*/
};
BlogEntry.prototype.setUrl = function(url)
{
    this.url = url;
};
BlogEntry.prototype.setExtOption = function(extPath)
{
    this.extPath = extPath;
};
