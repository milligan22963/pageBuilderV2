/**
 *  comment entry
 */
function CommentEntry(blogId, commentId, parentId, authorId)
{
    this.initialize("div");

    this.parentId = parentId;
    this.blogId = blogId;
    this.authorId = authorId;
    this.commentId = commentId;
    this.toolbar = new ToolbarElement();
    
    this.textNode = new LabelElement();
    this.textNode.addClass('simple_blog_comment');

    this.appendChild(this.textNode);

    this.toolbar.removeClass('toolbar');
    this.toolbar.addClass('blog_toolbar');
    this.appendChild(this.toolbar);
}
CommentEntry.inheritsFrom(BaseElement);
CommentEntry.prototype.getParentId = function()
{
    return this.parentId;
};
CommentEntry.prototype.getCommentId = function()
{
    return this.commentId;
};
CommentEntry.prototype.getAuthorId = function()
{
    return this.authorId;
};
CommentEntry.prototype.setText = function(text)
{
    if (this.textNode != null)
    {
        this.textNode.setText(text);
    }
};
CommentEntry.prototype.allowEditing = function(url, extOption, parentContainerId)
{
    var thisObj = this;

    this.toolbar.addButton("Edit", function() { showCommentEditor(thisObj, parentContainerId, thisObj.commentId, thisObj.blogId, thisObj.parentId, url, extOption); });
    this.toolbar.addButton("Delete", function() { deleteBlogComment(thisObj.object, thisObj.commentId, url, extOption); });
};
CommentEntry.prototype.allowComments = function(url, extOption, parentContainerId)
{
    var thisObj = this;

    // set this comment as the parent
    this.toolbar.addButton("Comment", function() { showCommentEditor(thisObj, parentContainerId, 0, thisObj.blogId, thisObj.commentId, url, extOption); });
};
