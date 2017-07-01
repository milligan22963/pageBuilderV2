<?php
/**
 * @module AdminOption
 *
 * @brief base class for all admin options for admin option classes
 */
namespace afm
{
	include_once('IAdminOperation.php');
	
	class AdminOperation implements IAdminOperation
	{
		private $m_ajaxCall;
		private $m_jsonCall;
		private $m_parameters;
		
		public function __construct()
		{
			$this->m_ajaxCall = false;
			$this->m_jsonCall = false;
		}
		
		/**
		 * @copydoc IAdminOperation::initialize
		 */
		public function initialize(& $paramArray)
		{
			$this->m_parameters = $paramArray;
			
			$systemObj = &System::getInstance();
			
			switch ($systemObj->getContentType())
			{
				case JSON_REQUEST:
				{
					$this->m_jsonCall = true;
				}
				break;
				case AJAX_REQUEST:
				{
					$this->m_ajaxCall;
				}
				break;
			}
		}

		/**
		 * @copydoc IAdminOperation::process
		 */
		public function process()
		{
			$resultingPage = null;
			
			return $resultingPage;
		}
		
		/**
		 * @copydoc IAdminOperation::populate
		 */
		public function populate(& $parent)
		{
			
		}

		// internal functions		
		protected function isAjaxCall()
		{
			return $this->m_ajaxCall;
		}
		
		protected function isJSONCall()
		{
			return $this->m_jsonCall;
		}
		
		protected function &getParameters()
		{
			return $this->m_parameters;
		}
	}	
}	
?>