<?php
/**
 * @module ManageExtensions
 *
 * @brief Manage Extension page to allow the user to select w/ extensions to populate
 *        or de-populate
 */
namespace afm
{
	if (defined('SYSTEM_OBJ') == false)
	{
		die('None shall pass.');
	}

	define('UPDATE_EXTENSIONS', "updateExtensions");
	define('EXTENSION_CHANGES', "changes");
	define('EXTENSION_PATH', "path");
	define('EXTENSION_SECTION', "section");

	include_once('AdminOperation.php');

	class ManageExtensions extends AdminOperation 
	{
		public function __construct()
		{
			parent::__construct();
		}

		/**
		 * @copydoc IAdminOperation::process
		 */
		public function process()
		{
			$resultingPage = null;
			
			if ($this->isJSONCall() == true)
			{
				$systemObj = System::getInstance();
				
				include_once($systemObj->getBaseSystemDir() . 'page/JSONPage.php');

				// create jsonPage and return based on sub option such as 'check_user_name'
				$resultingPage = new JSONPage();

				$params = $this->getParameters();
				if (isset($params[FUNCTION_PARAMETER]) == true)
				{
					switch ($params[FUNCTION_PARAMETER])
					{
						case UPDATE_EXTENSIONS:
						{
							$success = false;

							// should be a set of extensions that have changed
							// either with new sectionId i.e. activated
							// or null so de-activated
							// each extension should be path=>section
//							error_log('Params: ' . print_r($params, true));
							if (isset($params[EXTENSION_CHANGES]) == true)
							{
								$extensionManager = & $systemObj->getExtensionManager();
								$extData = new ExtensionData();

								foreach ($params[EXTENSION_CHANGES] as $extension)
								{
									$path = cleanseData($extension->path);
									
									$sectionId = null;
									if ($extension->section != null)
									{
										$sectionId = cleanseData($extension->section);
									}

									if ($sectionId == null)
									{
										error_log('Disabling extension: ' . $path);
										$extensionManager->disableExtension($path);
									}
									else
									{
										error_log('Enabling/Moving extension: ' . $path);
										$extensionManager->enableExtension($path, $sectionId);
									}
								}
								$success = true;
							}
							$resultingPage->addObject(JSON_SUCCESS, $success == true ? JSON_TRUE : JSON_FALSE);
						}
						break;

						default:
						{
							error_log('Unknown command: ' . $params[FUNCTION_PARAMETER]);
							$resultingPage = null;
						}
					}
				}
			}
			return $resultingPage;
		}

		/**
		 * @copydoc IAdminOperation::populate
		 */
		public function populate(& $parent)
		{
			$systemObj = & System::getInstance();

            $userSession = & $systemObj->getUserSession();
			
            if (($userSession->isLoggedIn() == true) && ($userSession->getUserType(USER_TYPE_ADMIN)))
            {
                $parentElement = DivElement::withParent($parent, 'manage_extension_div');

                $themeManager = & $systemObj->getThemeManager();

                $previewElements = $themeManager->preview($parentElement);

				if ($previewElements != null)
				{
					foreach ($previewElements as $previewElement)
					{
						$previewElement->addAttribute('draggable', 'true');
						$previewElement->addAttribute('ondragstart', "startExtensionDrag(event)");
					}
				}

				$extensionManager = & $systemObj->getExtensionManager();

				foreach ($extensionManager->getThemeSections() as $sectionId=>$sectionElement)
				{
					$sectionElement->addClass('theme_drop_target');
					$sectionElement->addAttribute('sectionid', $sectionId);
					$sectionElement->addAttribute('ondrop', "addExtension(event)");
					$sectionElement->addAttribute('ondragover', "checkDropTarget(event)");
				}

				$toolWindow = DivElement::withParent($parent, 'manage_extension_tools');

				$holdingBox = SectionElement::withParent($toolWindow, 'manage_extension_holding_box');
				$holdingBox->addAttribute('ondrop', "dropExtension(event)");
				$holdingBox->addAttribute('ondragover', "checkDropTarget(event)");

				foreach ($extensionManager->processOtherExtensions($parent, $holdingBox) as $otherExt)
				{
					$otherExt->addAttribute('draggable', 'true');
					$otherExt->addAttribute('ondragstart', "startExtensionDrag(event)");					
				}

				$applyButton = ButtonElement::withParent($toolWindow, 'apply_extension_changes');
				$applyButton->setData('Apply');
				$applyButton->addAttribute('disabled', 'disabled');
				$applyButton->addClickHandler("javascript:applyExtensionChanges('" . $systemObj->getScriptURL(true) . "')");
            }
		}
	}
}
?>
