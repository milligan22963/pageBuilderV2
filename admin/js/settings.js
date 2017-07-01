
/**
 * Object to track extensions changes
 * @param {*} id 
 * @param {*} path 
 * @param {*} section 
 * @param {*} isActive 
 */
function SettingChange(name, value)
{
    this.name = name;
    this.value = value;
    this.originalValue = value;

    this.hasChanged = function()
    {
        if (this.originalValue != this.value)
        {
            return true;
        }
        return false;
    };

    this.setValue = function(value)
    {
        this.value = value;
    };
}

var g_settingChanges = new Array();

/**
 * Check for changes in the settings
 */
function checkForSettingChanges()
{
    var changesDetected = false;

    g_settingChanges.forEach(function(element)
    {
        if (element.hasChanged() == true)
        {
            changesDetected = true;
        }
    }, this);

    var applyButton = document.getElementById('apply_setting_changes');
    if (changesDetected == true)
    {
        applyButton.removeAttribute('disabled');
    }
    else
    {
        applyButton.setAttribute('disabled', 'disabled');
    }
}

// add it to the array with the original values
function onSettingFocusIn(settingName, settingValue)
{
    var settingChange = null;

    // see if this one has already been modified
    g_settingChanges.forEach(function(element)
    {
        if (element.name == settingName)
        {
            settingChange = element;
        }
    }, this);

    /* should only add it in once */
    if (settingChange == null)
    {
        settingChange = new SettingChange(settingName, settingValue);

        g_settingChanges.push(settingChange);
    }

}

function onSettingChange(settingName, settingValue)
{
    var settingChange = null;

    // see if this one has already been modified
    g_settingChanges.forEach(function(element)
    {
        if (element.name == settingName)
        {
            settingChange = element;
        }
    }, this);

    /* It should already exist */
    if (settingChange != null)
    {
        settingChange.setValue(settingValue);
        checkForSettingChanges();
    }
}

/**
 * @brief called to activate a theme in the sytem
 * 
 * @param[in] siteUrl - the url to query
 */
function updateSettings(siteUrl)
{
	var thisObj = this;

	this.handleResponse = function(responseData)
	{
		if (responseData.success == 'true')
		{
			// refresh the page
			location.reload();
		}
		else
		{
			alert('It appears that something went wrong on the other end...');
		}
	};

	// send the changes over to the server
	var request = new transferJSON(true, siteUrl, thisObj.handleResponse);

	request.addValue(new nameValuePair('adminOption', 'settings')); // could extract this and pass in as part of the url
	request.addValue(new nameValuePair('func', 'updateSettings'));

    var settingsArray = new Array();

    g_settingChanges.forEach(function(element)
    {
        if (element.hasChanged() == true)
        {
            // add this one (name/value)
            settingsArray.push({name: element.name, value: element.value});
        }
    }, this);
    
    request.addValue(new nameValuePair('changes', settingsArray));

	request.send();
}