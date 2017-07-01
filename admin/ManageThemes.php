<?php
/**
 * @module ManageThemes
 *
 * @brief used to allow the user to select a theme for the site
 */
namespace afm
{
	if (defined('SYSTEM_OBJ') == false)
	{
		die('None shall pass.');
	}

	define('ACTIVATE_THEME', "activateTheme");
	define('ACTIVATE_THEME_ID', "themeId");
	
	include_once('AdminOperation.php');

	class ManageThemes extends AdminOperation 
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
						case ACTIVATE_THEME:
						{
							$success = false;
							
							if (array_key_exists(ACTIVATE_THEME_ID, $params) == true)
							{
								$themeManager = & $systemObj->getThemeManager();
								$settingsManager = & $systemObj->getSettingsManager();

								// themeId
								$themeId = cleanseData($params[ACTIVATE_THEME_ID]);

								$availableThemes = $themeManager->getAllThemes();
								foreach ($availableThemes as $theme)
								{
									if ($theme->getId() == $themeId)
									{
										error_log('Making this one active: ' . $theme->getId());
										// make this the active one
										$activeTheme = $themeManager->getActiveTheme();
										if ($activeTheme != null)
										{
											$activeTheme->deactivate();
										}
										$settingsManager->setSetting(SITE_THEME, $theme->getThemeDirectory());
										$theme->activate();
										$success = true;
										break;
									}
								}

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
                $themeManager = $systemObj->getThemeManager();

                $availableThemes = $themeManager->getAllThemes();

                $activeTheme = &$themeManager->getActiveTheme();

                $parentElement = new DivElement('theme_preview_div');
                $parent->addChildElement($parentElement);

                foreach ($availableThemes as $availableTheme)
                {
                    $childElement = new DivElement('theme_preview_div' . $availableTheme->getId());
                    $childElement->addClass('theme_preview_div_element');

                    $availableTheme->preview($childElement);

                    $activeLabel = new LabelElement('theme_preview_label' . $availableTheme->getId());
					$activeLabel->addClass('theme_preview_label');
                    $activeLabel->setData($availableTheme->getName());
                    $childElement->addChildElement($activeLabel);

                    if ($availableTheme->getActive() == true)
                    {
                        $childElement->addClass('theme_preview_div_active');
                        $activeLabel = new LabelElement('theme_preview_label_active' . $availableTheme->getId());
                        $activeLabel->setData('Currently active');

                        $childElement->addChildElement($activeLabel);
                    }
                    else
                    {
                        $childElement->addClass('theme_preview_div_inactive');
                        $activateButton = new ButtonElement('theme_preview_activate_button' . $availableTheme->getId());
                        $activateButton->setData('Activate');
						$activateButton->addClickHandler("javascript:activateTheme('" . $systemObj->getScriptURL(true) . "', '" . $availableTheme->getId() . "')");

                        $childElement->addChildElement($activateButton);
                    }
                    $parentElement->addChildElement($childElement);
                }
            }
		}
	}
}
?>
