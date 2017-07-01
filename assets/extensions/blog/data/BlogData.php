<?php
/**
 * @module BlogData
 *
 * @brief manages the blog data for this extension
 *      blogs will have a main blog data entry
 *      which will be cross referenced to content
 *      and to authors (users), and comments
 */

define('BLOG_TABLE', "simple_blog");

$systemObj = afm\System::getInstance();

include_once($systemObj->getBaseSystemDir() . 'configuration/data/Data.php');
	
class BlogData extends afm\Data
{
    private $m_title;
    private $m_summary;
    private $m_contentArray;
    private $m_commentArray;
    private $m_approved;
    private $m_authorId;

    public function __construct()
    {
        parent::__construct();
        
        $this->m_title = "none";
        $this->m_summary = "none";
        $this->m_contentArray = null;
        $this->m_commentArray = null;
        $this->m_approved = false;
        $this->m_authorId = 0;

        $systemObj = &afm\System::getInstance();
        $dbInstance = &$systemObj->getDatabase();

        $this->setTable($dbInstance->getTable(BLOG_TABLE));
    }

    public function setTitle($title)
    {
        $this->m_title = $title;
    }

    public function getTitle()
    {
        return $this->m_title;
    }
    
    public function setSummary($summary)
    {
        $this->m_summary = $summary;
    }

    public function getSummary()
    {
        return $this->m_summary;
    }

    public function setApproved($approved)
    {
        $this->m_approved = $approved;
    }

    public function getApproved()
    {
        return $this->m_approved;
    }

    public function setAuthorId($authorId)
    {
        $this->m_authorId = $authorId;
    }

    public function getAuthorId()
    {
        return $this->m_authorId;
    }

    public function setContent($contentArray)
    {
        $this->loadContent();

        // if we already have content, overwrite it, we just deactivate which would allow a fall back
        foreach ($this->m_contentArray as $content)
        {
            $content->setActive(false);
            $content->save();
        }

        include_once('ContentData.php');

        $contentData = new ContentData();
        
        foreach ($contentArray as $content)
        {
            $contentData->setContent($content);
            $contentData->setBlogId($this->getId());
            $contentData->setActive(true);
            $contentData->save();

            $contentData->reset();  // reset for the next one
        }
    }

    public function getContent()
    {
        $contentString = "";
        
        if ($this->m_contentArray != null)
        {
            foreach ($this->m_contentArray as $content)
            {
                $contentString .= $content->getContent();
            }
        }
        return $contentString;
    }

    // return an array of comments so they can be properly displayed
    public function getComments()
    {
        return $this->m_commentArray;
    }

    public function getBlogEntries($startRow, $numRows)
    {
        $table = $this->getTable();

        $query = $table->createQuery();

        // not using a join since there is a one to many mapping
        // for each entry
        $query->setRowOffset($startRow);
        $query->setLimit($numRows);
        $query->addWhereClause("active='true'");
        $query->addWhereClause("approved='true'");

        $blogEntries = array();
        $entries = $query->execute();

        if ($entries != null)
        {
            foreach ($entries as $entry)
            {
                $blogData = new BlogData();
                $blogData->fromSQL($entry);
                $blogData->loadContent();
                $blogData->loadComments();
                $blogEntries[] = $blogData;
            }
        }
        return $blogEntries;
    }

    /**
     * @brief JsonSerializable
     */		
    public function jsonSerialize()
    {
        $jsonData = parent::jsonSerialize();

        $this->loadContent();

        $jsonData = array_merge($jsonData,
        [
            'title' => $this->getTitle(),
            'summary' => $this->getSummary(),
            'content'=> $this->getContent(),
            'comments'=>$this->getComments(),
            'authorId'=>$this->getAuthorId(),
            'id'=>$this->getId()
        ]);

        return $jsonData;
    }

    // internal methods
    protected function fromSQL($dbObject)
    {
        parent::fromSQL($dbObject);

//        error_log('Sql: ' . print_r($dbObject, true));

        $this->setAuthorId($dbObject->author_id);
        $this->setTitle(trim($dbObject->title));
        $this->setSummary(trim($dbObject->summary));
        $this->setApproved($dbObject->approved == "true" ? true : false);
    }

    protected function toArray()
    {
        $arrayRepresentation = parent::toArray();

        $arrayRepresentation['author_id'] = $this->getAuthorId();
        $arrayRepresentation['title'] = $this->getTitle();
        $arrayRepresentation['summary'] = $this->getSummary();
        $arrayRepresentation['approved'] = $this->getApproved() == true ? "true" : "false";

        return $arrayRepresentation;
    }

    protected function loadContent()
    {
        // load all of the related content
        include_once('ContentData.php');

        $contentData = new ContentData();

        $this->m_contentArray = $contentData->loadAll($this->getId());
    }

    protected function loadComments()
    {
        // load all of the related content
        include_once('CommentData.php');

        $commentData = new CommentData();

        $this->m_commentArray = $commentData->loadAll($this->getId());
    }
}
?>