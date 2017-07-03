<?php
/**
 * @module CommentData
 *
 * @brief manages the blog data for this extension
 *      blogs will have a main blog data entry
 *      which will be cross referenced to content
 *      and to authors (users), and comments
 */

define('BLOG_COMMENT_TABLE', "simple_blog_comment");

$systemObj = afm\System::getInstance();

include_once($systemObj->getBaseSystemDir() . 'configuration/data/Data.php');
	
class CommentData extends afm\Data
{
    private $m_parentCommentId;
    private $m_comment;
    private $m_blogId;
    private $m_authorId;

    public function __construct()
    {
        parent::__construct();
        
        $this->m_parentCommentId = 0; // default no parent i.e. its the first level
        $this->m_comment = null;
        $this->m_blogId = 0;
        $this->m_authorId = 0;

        $systemObj = &afm\System::getInstance();
        $dbInstance = &$systemObj->getDatabase();

        $this->setTable($dbInstance->getTable(BLOG_COMMENT_TABLE));
    }

    public function setParentCommentId($parentId)
    {
        $this->m_parentCommentId = $parentId;
    }

    public function getParentCommentId()
    {
        return $this->m_parentCommentId;
    }

    public function setAuthorId($authorId)
    {
        $this->m_authorId = $authorId;
    }

    public function getAuthorId()
    {
        return $this->m_authorId;
    }

    public function setComment($comment)
    {
        $this->m_comment = $comment;
    }

    public function getComment()
    {
        return $this->m_comment;
    }

    /**
     * @brief JsonSerializable
     */		
    public function jsonSerialize()
    {
        $jsonData = parent::jsonSerialize();

        $jsonData = array_merge($jsonData,
        [
            'id' => $this->getId(),
            'comment' => $this->getComment(),
            'authorId' => $this->getAuthorId(),
            'parentComment'=> $this->getParentCommentId()
        ]);

        return $jsonData;
    }    

    public function setBlogId($blogId)
    {
        $this->m_blogId = $blogId;
    }

    public function getBlogId()
    {
        return $this->m_blogId;
    }

    /**
     * @copydoc IData::setActive
     */
    public function setActive($isActive)
    {
        parent::setActive($isActive);

        // now update our children accordingly
        // this should be recursive so the children of children will also update
        $childComments = $this->loadChildren($this->getId());
        foreach ($childComments as $child)
        {
            $child->setActive($isActive);
            $child->save();
        }
    }
   
    public function loadAll($blogId)
    {
        $table = $this->getTable();

        $query = $table->createQuery();

        // not using a join since there is a one to many mapping
        // for each entry
        $query->addWhereClause("blog_id='" . $blogId . "'");
        $query->addWhereClause("active=true");
        $query->addOrderClause("time_stamp asc");

        $blogComments = array();
        $comments = $query->execute();

        if ($comments != null)
        {
            foreach ($comments as $entry)
            {
                $commentData = new CommentData();
                $commentData->fromSQL($entry);
                $blogComments[] = $commentData;
            }
        }
        return $blogComments;
    }

    // internal methods
    protected function loadChildren($parentId)
    {
        $table = $this->getTable();

        $query = $table->createQuery();

        // not using a join since there is a one to many mapping
        // for each entry
        $query->addWhereClause("parent_id='" . $parentId . "'");

        $childComments = array();
        $comments = $query->execute();

        if ($comments != null)
        {
            foreach ($comments as $entry)
            {
                $commentData = new CommentData();
                $commentData->fromSQL($entry);
                $childComments[] = $commentData;
            }
        }
        return $childComments;
    }

    protected function fromSQL($dbObject)
    {
        parent::fromSQL($dbObject);

        $this->setComment(trim($dbObject->comment));
        $this->setBlogId($dbObject->blog_id);
        $this->setParentCommentId($dbObject->parent_id);
        $this->setAuthorId($dbObject->author_id);
    }

    protected function toArray()
    {
        $arrayRepresentation = parent::toArray();

        $arrayRepresentation['comment'] = $this->getComment();
        $arrayRepresentation['blog_id'] = $this->getBlogId();
        $arrayRepresentation['parent_id'] = $this->getParentCommentId();
        $arrayRepresentation['author_id'] = $this->getAuthorId();

        return $arrayRepresentation;
    }
}
?>