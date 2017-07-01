<?php
/**
 * @module BlogElement
 *
 * @brief manages the look of blog entries
 */

/**
  * Blog entries will look like this:
  *
  * TITLE
  * ------------------
  * "Hover summary"
  * Content
  */

$systemObj = afm\System::getInstance();

include_once($systemObj->getBaseSystemDir() . 'page/HtmlElements.php');

class BlogElement extends afm\DivElement
{
    private $m_blogData;

    public function __construct($id)
    {
        parent::__construct($id);

        $this->m_blogData = null;
    }

    static public function withIdAndObject($id, $object, $path)
    {
        $blogElement = new BlogElement($id);

        $blogElement->setBlogDataObject($object, $path);

        return $blogElement;
    }

    public function setBlogDataObject($dataObject, $path)
    {
		$systemObj = afm\System::getInstance();

        $this->setToolTip($dataObject->getSummary());

        $this->m_blogData = $dataObject;

        $id = 'blog_entry_' . $this->m_blogData->getId() . '_title';

        $titleElement = afm\DivElement::withParent($this, $id);

        $idLabel = $id . "_label";
        $label = afm\LabelElement::withParent($titleElement, $idLabel, $dataObject->getTitle());

        $label->addClass('simple_blog_title');

        $idHR = $id . "_hr";
        $hr = afm\HorizontalRuleElement::withParent($titleElement, $idHR);
        $hr->addClass('simple_blog_hr');

        $content = $dataObject->getContent();
        $idText = $id . "_content";
        $text = afm\LabelElement::withParent($titleElement, $idText, $content);
        $text->addClass('simple_blog_content');

        $toolbarId = $id . "_toolbar";
        $toolbar = afm\DivElement::withParent($this, $toolbarId);

        $userSession = & $systemObj->getUserSession();
        if (($userSession->isLoggedIn() == true) && ($userSession->getUserId() == $dataObject->getAuthorId()))
        {
            $editButton = afm\ButtonElement::withParent($toolbar, $id . '_edit');
            $editButton->setData("Edit");
            $editButton->addClickHandler("javascript:showEditor('simple_blog_edit_dialog', false, " . $dataObject->getId() . ", '" . $systemObj->getScriptURL(true) . "', '" . $path . "')");
        }

        $idHR = $id . "_cmmt_hr";
        $hr = afm\HorizontalRuleElement::withParent($this, $idHR);
        $hr->addClass('simple_blog_hr');

        $commentArray = array();

        foreach ($dataObject->getComments() as $comment)
        {
            $parentSection = $this;
            $commentDivId = $id . "_comment_" . $comment->getId();

            if ($comment->getParentCommentId() != 0)
            {
                $parentSection = $commentArray[$comment->getParentCommentId()];
            }
            $commentSection = afm\DivElement::withParent($parentSection, $commentDivId);
            $commentSection->addClass('simple_blog_comment_section');
            $commentEntryId = $commentDivId . "_label";

            $commentEntry = afm\LabelElement::withParent($commentSection, $commentEntryId, $comment->getComment());
            $commentEntry->addClass('simple_blog_comment');

            $commentArray[$comment->getId()] = $commentSection;
        }
    }

    public function getBlogDataObject()
    {
        return $this->m_blogData;
    }
}
?>