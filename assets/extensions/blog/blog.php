<?php
/**
 * @module Blog
 *
 * @brief the blog extension to allow a simple blogging system
 */

$systemObj = afm\System::getInstance();

$systemObj->includeLocalFile('Extension.php');

// commands
define('GET_BLOG_DATA', "getBlogData");
define('SAVE_BLOG_DATA', "saveBlogData");
define('DELETE_BLOG_DATA', "deleteBlogData");
define('ADD_BLOG_COMMENT', "addBlogComment");
define('EDIT_BLOG_COMMENT', "editBlogComment");
define('GET_BLOG_COMMENT', "getBlogComment");
define('APPROVE_BLOG_COMMENT', "approveBlogComment");
define('DELETE_BLOG_COMMENT', "deleteBlogComment");

// params
define('BLOG_START_RECORD', "startRecord");
define('BLOG_RECORD_COUNT', "recordCount");
define('BLOG_ID_PARAM', "blogId");
define('BLOG_USER_ID', "userId");
define('BLOG_TITLE_PARAM', "blogTitle");
define('BLOG_SUMMARY_PARAM', "blogSummary");
define('BLOG_CONTENT_PARAM', "blogContent");
define('BLOG_COMMENT_PARAM', "blogComment");
define('BLOG_COMMENT_ID', "commentId");
define('BLOG_COMMENT_PARENT_ID', "parentId");

// module defines
define('MAX_BLOG_CONTENT_LENGTH', 1024);

class BlogWidget extends afm\Extension
{
	public function __construct()
	{
		parent::__construct();

		$this->setType(BLOG_TYPE);
	}
	
	/**
 	 * @copydoc IExtension::initialize
 	 */
	public function initialize($relativeExtensionPath, $extensionSettings)
	{
		parent::initialize($relativeExtensionPath, $extensionSettings);

		$systemObj = afm\System::getInstance();

		$dbInstance = & $systemObj->getDatabase();

		$dbInstance->loadTable($this->getExtensionPath() . 'db/blog.xml', false);
		$dbInstance->loadTable($this->getExtensionPath() . 'db/content.xml', false);
		$dbInstance->loadTable($this->getExtensionPath() . 'db/comment.xml', false);
	}

	/**
	 * @copydoc IExtension::populate
	 */
	public function populate(& $parentElement)
	{
		$systemObj = afm\System::getInstance();

		if ($systemObj->getPageDomain() == PAGE_USER_DOMAIN)
		{
			$blogPath = basename($this->getExtensionPath());

			$systemObj->getPageObject()->executeOnWindowLoad("RequestBlogData(1, 5, '" . $systemObj->getScriptURL(true) . "', '" . $blogPath . "', 'simple_blog_section')");

			include_once('data/BlogData.php');
			include_once('BlogElement.php');

			$this->requireScript('HTML_ELEMENTS');
			$this->requireScript('DIALOG');
			$this->requireStyleSheet('css/blog.css');
			$this->requireScript('js/comment.js');
			$this->requireScript('js/blogentry.js');
			$this->requireScript('js/blog.js');

			$blogData = new BlogData();
			
			// do we want settings to show # of blogs per page
			// do we want to only show to registered users?
			// auto show/hide comments
			$blogSection = afm\SectionElement::withParent($parentElement, 'simple_blog_section');

			// pull X blogs

/*			$entries = $blogData->getBlogEntries(0, 5);
		
			foreach ($entries as $blogEntry)
			{
				$blogElement = BlogElement::withIdAndObject('blog_entry_' . $blogEntry->getId(), $blogEntry, $blogPath);

				$blogElement->addClass('blog_entry');

				$blogSection->addChildElement($blogElement);
			}*/

			// hidden blog/dialogs
			$createBlogDiv = afm\DivElement::withParent($blogSection, 'simple_blog_create_dialog');
			$createBlogDiv->addClass('modal modal_dialog');
			$editBlogDiv = afm\DivElement::withParent($blogSection, 'simple_blog_edit_dialog');
			$editBlogDiv->addClass('modal modal_dialog');
			$addCommentDiv = afm\DivElement::withParent($blogSection, 'simple_blog_comment_dialog');
			$addCommentDiv->addClass('modal modal_dialog');
			$editCommentDiv = afm\DivElement::withParent($blogSection, 'simple_blog_comment_edit_dialog');
			$editCommentDiv->addClass('modal modal_dialog');
		}
	}

	public function postProcess()
	{
		$systemObj = afm\System::getInstance();

		if ($systemObj->getPageDomain() == PAGE_USER_DOMAIN)
		{
			$userSession = & $systemObj->getUserSession();
			if ($userSession->isLoggedIn() == true)
			{
				$extensionManager = & $systemObj->getExtensionManager();
				$menuWidget = & $extensionManager->getExtensionByType(MENU_TYPE);

				$blogPath = basename($this->getExtensionPath());

				if ($menuWidget != null)
				{
					$blogHeader = $menuWidget->addMenuHeader("Blog");
					$blogHeader->addClass("simple_blog_menu_header");

					$command = $systemObj->getScriptURL();
			
					// error_log('ScriptURL: ' . $systemObj->getScriptURL(true));
					$menuWidget->addEntry('new_blog_entry', 'New Entry', 'user_menu', "javascript:showEditor('simple_blog_section', 0, '" . $systemObj->getScriptURL(true) . "', '" . $blogPath . "')");
				}
				else
				{
					error_log('Menu widget doesn\'t exist yet');
				}
			}
		}
	}

	public function &processRequest($option, $paramArray)
	{
		$success = false;
		$systemObj = afm\System::getInstance();

		include_once($systemObj->getBaseSystemDir() . 'page/JSONPage.php');

		$resultingPage = new afm\JSONPage();

		switch ($option)
		{
			case GET_BLOG_DATA:
			{
				if (array_key_exists(BLOG_ID_PARAM, $paramArray) == true)
				{
					include_once('data/BlogData.php');

					$blogId = intval($paramArray[BLOG_ID_PARAM]);

					$blogData = new BlogData();

					$blogData->load($blogId);

					$resultingPage->addObject('data', $blogData);

					$success = true;
				}
				else if ((array_key_exists(BLOG_START_RECORD, $paramArray) == true) && (array_key_exists(BLOG_RECORD_COUNT, $paramArray) == true))
				{
					$startRecord = intval($paramArray[BLOG_START_RECORD]);

					if ($startRecord > 0)
					{
						$startRecord -= 1;  // 0 offset
					}
					$recordCount = intval($paramArray[BLOG_RECORD_COUNT]);

					include_once('data/BlogData.php');

					$blogData = new BlogData();

					$entries = $blogData->getBlogEntries($startRecord, $recordCount);

					$resultingPage->addObject('data', $entries);

					$systemObj = & afm\System::getInstance();
					$userSession = & $systemObj->getUserSession();

					$userId = 0; // no user
					if ($userSession->isLoggedIn() == true)
					{
						$userId = $userSession->getUserId();
					}

					$resultingPage->addObject(BLOG_USER_ID, $userId);

					$success = true;
				}
			}
			break;
			case SAVE_BLOG_DATA:
			{
				$systemObj = & afm\System::getInstance();

				$userSession = & $systemObj->getUserSession();
				if ($userSession->isLoggedIn() == true)
				{
					if (array_key_exists(BLOG_ID_PARAM, $paramArray) == true)
					{
						if ((array_key_exists(BLOG_TITLE_PARAM, $paramArray) == true) && (array_key_exists(BLOG_CONTENT_PARAM, $paramArray)) && (array_key_exists(BLOG_SUMMARY_PARAM, $paramArray)))
						{
							$title = afm\cleanseData($paramArray[BLOG_TITLE_PARAM]);
							$content = afm\cleanseData($paramArray[BLOG_CONTENT_PARAM]);
							$summary = afm\cleanseData($paramArray[BLOG_SUMMARY_PARAM]);

							include_once('data/BlogData.php');

							$blogData = new BlogData();
							$isNewEntry = true;
							$blogId = intval($paramArray[BLOG_ID_PARAM]);
							if ($blogId != 0)
							{
								$isNewEntry = false;

								$blogData->load($blogId);
							}
							else
							{
								$blogData->setAuthorId($userSession->getUserId());
							}

							if (($isNewEntry == true) || ($userSession->getUserId() == $blogData->getAuthorId()))
							{
								$blogData->setTitle($title);
								$blogData->setActive(true); // this will make it active but it will not be approved yet unless auto approve is on
								$blogData->save();

								$contentArray = str_split($content, MAX_BLOG_CONTENT_LENGTH);

								$blogData->setContent($contentArray);
								$success = true;
							}
						}
					}
				}
				else
				{
					$resultingPage->addObject('reason', "Not logged in.");
				}
			}
			break;
			case DELETE_BLOG_DATA:
			{
				$systemObj = & afm\System::getInstance();

				$userSession = & $systemObj->getUserSession();
				if ($userSession->isLoggedIn() == true)
				{
					if (array_key_exists(BLOG_ID_PARAM, $paramArray) == true)
					{
						include_once('data/BlogData.php');

						$blogData = new BlogData();
						$blogId = intval($paramArray[BLOG_ID_PARAM]);
						if ($blogId != 0)
						{
							$blogData->load($blogId);

							if ($userSession->getUserId() == $blogData->getAuthorId())
							{
								$blogData->setActive(false);
								$blogData->save();

								$success = true;
							}
						}
					}
				}
			}
			break;
			case GET_BLOG_COMMENT:
			{
				if (array_key_exists(BLOG_COMMENT_ID, $paramArray) == true)
				{
					include_once('data/CommentData.php');

					$commentId = intval($paramArray[BLOG_COMMENT_ID]);

					if ($commentId != 0)
					{
						$commentData = new CommentData();

						$commentData->load($commentId);

						$resultingPage->addObject('data', $commentData);

						$success = true;
					}
				}
			}
			break;
			case ADD_BLOG_COMMENT:
			{
				$systemObj = & afm\System::getInstance();

				$userSession = & $systemObj->getUserSession();
				if ($userSession->isLoggedIn() == true)
				{
					// create a new comment for the given blog entry
					if (array_key_exists(BLOG_ID_PARAM, $paramArray) == true)
					{
						$blogId = intval($paramArray[BLOG_ID_PARAM]);
						$parentId = intval($paramArray[BLOG_COMMENT_PARENT_ID]); // maybe 0

						if ($blogId != 0)
						{
							$comment = afm\cleanseData($paramArray[BLOG_COMMENT_PARAM]);

							// we have the blog id to attach the comment to
							include_once('data/CommentData.php');

							$commentData = new CommentData();
							$commentData->setBlogId($blogId);
							$commentData->setComment($comment);
							$commentData->setAuthorId($userSession->getUserId());
							$commentData->setParentCommentId($parentId);

							$commentData->save();

							$success = true;
						}
					}
				}
			}
			break;
			case EDIT_BLOG_COMMENT:
			{
				$systemObj = & afm\System::getInstance();

				$userSession = & $systemObj->getUserSession();
				if ($userSession->isLoggedIn() == true)
				{
					if (array_key_exists(BLOG_COMMENT_ID, $paramArray) == true)
					{
						$commentId = intval($paramArray[BLOG_COMMENT_ID]);

						if ($commentId != 0)
						{
							include_once('data/CommentData.php');

							$commentData = new CommentData();
							$commentData->load($commentId);

							if ($userSession->getUserId() == $commentData->getAuthorId())
							{
								$comment = afm\cleanseData($paramArray[BLOG_COMMENT_PARAM]);

								$commentData->setComment($comment);
								$commentData->save();

								$success = true;
							}
						}
					}
				}
			}
			break;
			case DELETE_BLOG_COMMENT:
			{
				// this will delete the child comments too
				$systemObj = & afm\System::getInstance();

				$userSession = & $systemObj->getUserSession();
				if ($userSession->isLoggedIn() == true)
				{
					if (array_key_exists(BLOG_COMMENT_ID, $paramArray) == true)
					{
						$commentId = intval($paramArray[BLOG_COMMENT_ID]);

						if ($commentId != 0)
						{
							include_once('data/CommentData.php');

							$commentData = new CommentData();
							$commentData->load($commentId);

							if ($userSession->getUserId() == $commentData->getAuthorId())
							{
								$commentData->setActive(false);
								$commentData->save();

								$success = true;
							}
						}
					}
				}
			}
			break;
		}

		$resultingPage->addObject(JSON_SUCCESS, $success == true ? JSON_TRUE : JSON_FALSE);

		return $resultingPage;
	}

	/**
	 * @brief overrides for base Extension class
	 */
	protected function installTables()
	{
		$systemObj = afm\System::getInstance();

		$dbInstance = & $systemObj->getDatabase();

		$dbInstance->loadTable($this->getExtensionPath() . 'db/blog.xml', true);
		$dbInstance->loadTable($this->getExtensionPath() . 'db/content.xml', true);
		$dbInstance->loadTable($this->getExtensionPath() . 'db/comment.xml', true);
	}
	
	protected function removeTables()
	{
		$baseDir = dirname(__FILE__) . '/';

		include_once($baseDir . '/data/BlogData.php');
		include_once($baseDir . '/data/CommentData.php');
		include_once($baseDir . '/data/ContentData.php');

		// this will drop the blog table and associated ones but not
		// the entry in the extension table.
        $systemObj = & afm\System::getInstance();
        $dbInstance = &$systemObj->getDatabase();

		foreach (array(BLOG_TABLE, BLOG_CONTENT_TABLE, BLOG_COMMENT_TABLE) as $tableName)
		{
			$table = $dbInstance->getTable($tableName);

			if ($table != null)
			{
				$table->drop();
			}
		}
	}
	
	protected function resetTables()
	{
		
	}
}
?>
