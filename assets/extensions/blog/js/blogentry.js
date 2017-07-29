/**
 *  blog entry
 */
function BlogEntry(blogId)
{
    this.blogId = blogId;
    this.title = new LabelElement();
    this.content = new LabelElement();
    this.comments = new Array();
    this.toolbar = new ToolbarElement();

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

    this.toolbar.removeClass('toolbar');
    this.toolbar.addClass('blog_toolbar');
    this.appendChild(this.toolbar);

    var hr = new HorizontalRuleElement();

    hr.addClass('simple_blog_hr');
    this.appendChild(hr);
}
BlogEntry.inheritsFrom(DivElement);
BlogEntry.prototype.setTitle = function(title)
{
    this.title.setText(title);
};
BlogEntry.prototype.getId = function()
{
    return this.blogId;
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
};
BlogEntry.prototype.allowEditing = function(url, extOption, parentContainerId)
{
    var thisObj = this;

    this.toolbar.addButton("Edit", function() { showBlogEditor(thisObj, parentContainerId, url, extOption); });
    this.toolbar.addButton("Delete", function() { deleteBlogEntry(thisObj, thisObj.blogId, url, extOption); });
//    toolbar.addButton("Approve", function() { showBlogEditor(parentContainerId, thisObj.blogId, url, extOption); });
};
BlogEntry.prototype.allowComments = function(url, extOption, parentContainerId)
{
    var thisObj = this;

    this.toolbar.addButton("Comment", function() { showCommentEditor(null, parentContainerId, 0, thisObj.blogId, 0, url, extOption); });
};
