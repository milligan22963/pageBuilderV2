<?php
/**
 * @module ContentData
 *
 * @brief manages the blog data for this extension
 *      blogs will have a main blog data entry
 *      which will be cross referenced to content
 *      and to authors (users), and comments
 */

define('BLOG_CONTENT_TABLE', "simple_blog_content");

$systemObj = afm\System::getInstance();

include_once($systemObj->getBaseSystemDir() . 'configuration/data/Data.php');
	
class ContentData extends afm\Data
{
    private $m_content;
    private $m_blogId;

    public function __construct()
    {
        parent::__construct();
        
        $this->m_content = null;
        $this->m_blogId = 0;

        $systemObj = &afm\System::getInstance();
        $dbInstance = &$systemObj->getDatabase();

        $this->setTable($dbInstance->getTable(BLOG_CONTENT_TABLE));
    }

    public function setContent($content)
    {
        $this->m_content = $content;
    }

    public function getContent()
    {
        return $this->m_content;
    }
    
    public function setBlogId($blogId)
    {
        $this->m_blogId = $blogId;
    }

    public function getBlogId()
    {
        return $this->m_blogId;
    }

    public function loadAll($blogId)
    {
        $table = $this->getTable();

        $query = $table->createQuery();

        // not using a join since there is a one to many mapping
        // for each entry
        $query->addWhereClause("blog_id='" . $blogId . "'");
        $query->addWhereClause("active='true'");
        $query->addOrderClause("time_stamp asc");

        $blogContent = array();
        $content = $query->execute();

        if ($content != null)
        {
            foreach ($content as $entry)
            {
                $contentData = new ContentData();
                $contentData->fromSQL($entry);
                $blogContent[] = $contentData;
            }
        }
        return $blogContent;
    }

    // internal methods
    protected function fromSQL($dbObject)
    {
        parent::fromSQL($dbObject);

        $this->setContent(trim($dbObject->content));
        $this->setBlogId($dbObject->blog_id);
    }

    protected function toArray()
    {
        $arrayRepresentation = parent::toArray();

        $arrayRepresentation['content'] = $this->getContent();
        $arrayRepresentation['blog_id'] = $this->getBlogId();

        return $arrayRepresentation;
    }
}
?>