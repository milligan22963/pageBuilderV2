<?php
/**
 * @module ManageSettings
 *
 * @brief used to allow the user to change various settings for the site
 */
namespace afm
{
	if (defined('SYSTEM_OBJ') == false)
	{
		die('None shall pass.');
	}

	define('UPDATE_SETTINGS', "updateSettings");
	define('SETTING_CHANGES', "changes");
	define('SETTING_ID', "name");
	define('SETTING_VALUE', "value");

	include_once('AdminOperation.php');

	class ManageSettings extends AdminOperation 
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
						case UPDATE_SETTINGS:
						{
							$success = false;
							
							// should be a set of extensions that have changed
							// either with new sectionId i.e. activated
							// or null so de-activated
							// each extension should be path=>section
//							error_log('Params: ' . print_r($params, true));
							if (isset($params[SETTING_CHANGES]) == true)
							{
								$settingsManager = & $systemObj->getSettingsManager();

								foreach ($params[SETTING_CHANGES] as $settingObj)
								{
									$name = cleanseData($settingObj->name);									
                                    $value = cleanseData($settingObj->value);

//                                    error_log('New Value: ' . $value);

                                    $settingsManager->setSetting($name, $value);
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
                $settingsManager = & $systemObj->getSettingsManager();

                $systemObj->includeLocalFile('Toolbox.php');

                $pageObject = & $systemObj->getPageObject();

                $settingForm = $pageObject->addForm('settings_update', $parent);

                $groupArray = array();
                $groupObjects = array();

                // organize by group
                $groups = $settingsManager->getAllGroups(true); // get all of the groups editable by admins
                foreach ($groups as $group)
                {
                    $groupArray[$group->getId()] = array();
                    $groupObjects[$group->getId()] = $group; // save for later
                }

                $jsFocusCommand = "onSettingFocusIn(this.id, this.value)";
                $jsChangeCommand = "onSettingChange(this.id, this.value)";

                // Create each of the setting sections
                $settings = $settingsManager->getAllSettings(false, true);
                foreach ($settings as $setting)
                {
                    $inputElement = null;

                    switch ($setting->getType())
                    {
                        case BOOLEAN:
                        {
                            $inputElement = new CheckboxInput($setting->getName());
                            $inputElement->setData($setting->getDescription());
                            if ($setting->getValue() == 'true')
                            {
                                $inputElement->setChecked(true);
                            }
                            $inputElement->addAttribute('onfocusin', $jsFocusCommand);
                            $inputElement->addAttribute('onchange', $jsChangeCommand);
                        }
                        break;
                        case INTEGER:
                        {
                        }
                        break;
                        case FLOAT:
                        {
                        }
                        break;
                        case STRING:
                        {
                            $inputElement = new LabeledInput($setting->getName());
                            $inputElement->setLabelText($setting->getDescription());
                            $inputElement->setInputType(TEXT);

                            $textInput = $inputElement->getInputElement();
                            $textInput->setValue($setting->getValue());
                            $textInput->addAttribute('onfocusin', $jsFocusCommand);
                            $textInput->addAttribute('onchange', $jsChangeCommand);
                        }
                        break;
                        case EMAIL:
                        {
                            $inputElement = new LabeledInput($setting->getName());
                            $inputElement->setLabelText($setting->getDescription());
                            $inputElement->setInputType(EMAIL_TYPE);

                            $email = $inputElement->getInputElement();
                            $email->setEmail($setting->getValue());
                            $email->setRequired(true);
                            $email->addAttribute('onfocusin', $jsFocusCommand);
                            $email->addAttribute('onchange', $jsChangeCommand);
                        }
                        break;
                        case RELATIVE_PATH:
                        {
                        }
                        break;
                        case ABSOLUTE_PATH:
                        {
                        }
                        break;
                        case PASSWORD:
                        {
                        }
                        break;
                    }

                    if ($inputElement != null)
                    {
                        if (array_key_exists($setting->getGroupId(), $groupArray) == true)
                        {
                            $inputElement->addClass('setting');

                            // this one is a valid, add the element to the end
                            $groupArray[$setting->getGroupId()][] = $inputElement;
                        }
                        else
                        {
                            error_log('Non-editable group:' . $setting->getGroupId());
                        }
                    }
                }

                foreach ($groupObjects as $groupId=>$group)
                {
                    if (array_key_exists($groupId, $groupArray) == true)
                    {
                        if (count($groupArray[$groupId]) > 0)
                        {
                            $groupFieldSet = $settingForm->addFieldSet('group_' . $group->getTag());

                            $groupFieldSet->addClass('manage_settings');
                            $groupFieldSet->addLegend($group->getName());

                            foreach ($groupArray[$groupId] as $groupElement)
                            {
                                $groupFieldSet->addChildElement($groupElement);
                            }
                        }
                    }
                }

				$applyButton = ButtonElement::withParent($settingForm, 'apply_setting_changes');
				$applyButton->setData('Apply');
				$applyButton->addAttribute('disabled', 'disabled');
				$applyButton->addClickHandler("javascript:updateSettings('" . $systemObj->getScriptURL(true) . "')");
            }
		}
	}
}
?>