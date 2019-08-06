<?php
/**
 * @module HtmlPage
 *
 * @brief class for all html pages for rendering
 */
 namespace afm
 {
    // Includes
    include_once('Page.php');
    include_once('HtmlObjects.php');
    include_once('HtmlInputs.php');

    // module defines
    define('CSS_FILE', "css_file");
    define('JS_FILE', "js_file");
    define('META_DATA', "meta_data");

    class HtmlPage extends Page
    {
        private $m_title = null;
        private $m_inlineCSS = null;
        private $m_inlineJS = null;
        private $m_windowOnLoad = null;

        function __construct()
        {
            parent::__construct();

			$systemObj = &System::getInstance();
            
            $systemObj->setPageObject($this);
            
            $this->setElementName("body");
        }

        public function getTitle()
        {
            return $this->m_title;
        }

        public function setTitle($title)
        {
            $this->m_title = $title;
        }

		public function requireJS($jsId)
		{
			$systemObj = &System::getInstance();
			
			$scriptManager = &$systemObj->getScriptManager();

			$scriptManager->requireScript($jsId);
		}
		
		public function requireCSS($cssId)
		{
			$systemObj = &System::getInstance();
			
			$scriptManager = &$systemObj->getScriptManager();

			$scriptManager->requireStyle($cssId);			
		}
		
        public function addCSSFile($cssFile, $type)
        {
            $addIt = true;

            $currentCSSFiles = $this->getDisplayElements(CSS_FILE . $type);
            if ($currentCSSFiles != null)
            {
                foreach ($currentCSSFiles as $currentFile)
                {
                    if ($currentFile->getAttribute(HREF) == $cssFile)
                    {
                        $addIt = false;
                        break;
                    }
                }
            }

            if ($addIt == true)
            {
                $displayElement = new CssLinkObject($cssFile);

                $this->addDisplayElement(CSS_FILE . $type, $displayElement);
            }
        }

        public function addJSFile($jsFile, $type)
        {
            $addIt = true;

            $currentJSFiles = $this->getDisplayElements(JS_FILE . $type);
            if ($currentJSFiles != null)
            {
                foreach ($currentJSFiles as $currentFile)
                {
                    if ($currentFile->getAttribute(SOURCE) == $jsFile)
                    {
                        $addIt = false;
                        break;
                    }
                }
            }

            if ($addIt == true)
            {
                $displayElement = new JSFileObject($jsFile);

                $this->addDisplayElement(JS_FILE . $type, $displayElement);
            }
        }

        public function addInlineCSS($cssData)
        {
            if ($this->m_inlineCSS == null)
            {
                $this->m_inlineCSS = new CssInlineObject($cssData);
            }
            else
            {
                $this->m_inlineCSS->addChildElement($cssData);
            }
        }

        public function addInlineJS($jsData)
        {
            if ($this->m_inlineJS == null)
            {
                $this->m_inlineJS = new JSInlineObject($jsData);
            }
            else
            {
                $this->m_inlineJS->addChildElement($jsData);
            }
        }

        // need to create a js function element
        public function executeOnWindowLoad($code)
        {
            if ($this->m_windowOnLoad == null)
            {
                $this->m_windowOnLoad = array();
            }
            $this->m_windowOnLoad[] = $code;
        }

        public function addMetaData($name, $value)
        {
            $displayElement = new MetaDataObject($name, $value);

            $this->addDisplayElement(META_DATA, $$displayElement);
        }
        
        public function addForm($formId, $parent = null)
        {
	        $displayData = new FormElement($formId);
	        
	        if ($parent != null)
	        {
		        $parent->addChildElement($displayData);
		    }
		    else
		    {
		        $this->addChildElement($displayData);
		    }
		    
		    return $displayData;
        }

		public function addHeader($headerId, $parent = null)
		{
			$childElement = new HeaderElement($headerId);
			
			if ($parent != null)
			{
				$parent->addChildElement($childElement);
			}
			else
			{
				$this->addChildElement($childElement);
			}
			
			return $childElement;
		}
		
		public function addFooter($footerId, $parent = null)
		{
			$childElement = new FooterElement($footerId);
			
			if ($parent != null)
			{
				$parent->addChildElement($childElement);
			}
			else
			{
				$this->addChildElement($childElement);
			}
			
			return $childElement;
		}

		public function addSection($sectionId, $parent = null)
		{
			$childElement = new SectionElement($sectionId);
			
			if ($parent != null)
			{
				$parent->addChildElement($childElement);
			}
			else
			{
				$this->addChildElement($childElement);
			}
			
			return $childElement;
		}
		
		public function addAside($asideId, $parent = null)
		{
			$childElement = new AsideElement($asideId);
			
			if ($parent != null)
			{
				$parent->addChildElement($childElement);
			}
			else
			{
				$this->addChildElement($childElement);
			}			
			
			return $childElement;
		}

		public function addDiv($divId, $parent = null)
		{
			$childElement = new DivElement($divId);
			
			if ($parent != null)
			{
				$parent->addChildElement($childElement);
			}
			else
			{
				$this->addChildElement($childElement);
			}			
			
			return $childElement;
		}

		public function addLabel($labelId, $labelData, $parent = null)
		{
			$childElement = new LabelElement($labelId);
			$childElement->setData($labelData);
			
			if ($parent != null)
			{
				$parent->addChildElement($childElement);
			}
			else
			{
				$this->addChildElement($childElement);
			}			
			
			return $childElement;
		}
		
		public function addAnchor($anchorId, $link, $caption, $parent = null)
		{
			$childElement = new AnchorElement($anchorId);
			$childElement->setData($caption);
			$childElement->setLink($link);
			
			if ($parent != null)
			{
				$parent->addChildElement($childElement);
			}
			else
			{
				$this->addChildElement($childElement);
			}			
			
			return $childElement;
		}
		
        public function render()
        {
		    $g_iconFiles = [
		        "ico" => "favicon.ico",
		        "png" => "favicon.png",
		    ];

            if ($this->m_windowOnLoad != null)
            {
                $jsCode = "function onLoad(){";
                foreach ($this->m_windowOnLoad as $code)
                {
                    $jsCode .= $code . ';';
                }
                $jsCode .= "}";
                $this->addInlineJS($jsCode);
                $this->addAttribute('onload', "onLoad()");
            }
            $renderedPage = "<!DOCTYPE html>" . PHP_EOL . '<html lang="en-us">' . PHP_EOL . "<head>" . PHP_EOL;
//            $renderedPage = '<html lang="en-us">' . PHP_EOL . "<head>" . PHP_EOL;

            // for testing
            $renderedPage .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
            $renderedPage .= '<meta charset="UTF-8">';

            /*--------------------------------------------------------------------
                Dump title, meta data, css links, js links etc.
            ------------------------------------------------------------------*/
            // Favorite icon?
            foreach ($g_iconFiles as $type => $fileName)
            {
                if (file_exists($fileName))
                {
                    $renderedPage .= "<link rel=\"shortcut icon\" href=\"$fileName\" />" . PHP_EOL;
                    break;
                }
            }

            // Add title
            $titleText = "My Site";

            if ($this->m_title != null)
            {
                $titleText = $this->m_title;
            }
            else
            {
                $titleText = $_SERVER['PHP_SELF'];
            }

            $renderedPage .= "<title>" . $titleText . "</title>" . PHP_EOL;

            // add meta data 
            $renderedPage .= $this->renderContent(META_DATA);

            // Links?
            foreach (array(SITE_FILE, THEME_FILE, EXT_FILE) as $fileType)
            {
                $renderedPage .= $this->renderContent(CSS_FILE . $fileType);

                $renderedPage .= $this->renderContent(JS_FILE . $fileType);
            }

            // Inline css/js?
            if ($this->m_inlineCSS != null)
            {
                $renderedPage .= $this->m_inlineCSS->render();
            }

            if ($this->m_inlineJS != null)
            {
                $renderedPage .= $this->m_inlineJS->render();
            }

            /// testing
#            $renderedPage .= "<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>";

            // add all header based data 
            $renderedPage .= "</head>" . PHP_EOL;

            // render all of the page content
            $renderedPage .= parent::render() . PHP_EOL;
            
            // finally finish it off
            $renderedPage .= "</html>" . PHP_EOL;

        	header('Content-Type: text/html; charset=ISO-8859-4');

            if ($this->isDirectDisplay() == true)
            {
                echo $renderedPage;
            }
            return $renderedPage;
        }

        protected function renderContent($contentId)
        {
            $displayData = "";

            $displayElements = $this->getDisplayElements($contentId);

            if ($displayElements != null)
            {
                foreach ($displayElements as $displayElement)
                {
                    $displayData .= $displayElement->render();
                }
            }
            return $displayData;
        }
    }
}
?>