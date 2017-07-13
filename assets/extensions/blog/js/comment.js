/**
 *  comment entry
 */
function CommentEntry(commentId, parentId, authorId)
{
    this.initialize("div");

    this.parentId = parentId;
    this.authorId = authorId;
    this.commentId = commentId;
    
    this.textNode = new LabelElement();
    this.textNode.addClass('simple_blog_comment');

    this.appendChild(this.textNode);
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

