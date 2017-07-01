
/**
 * Object to track extensions changes
 * @param {*} id 
 * @param {*} path 
 * @param {*} section 
 * @param {*} isActive 
 */
function ExtensionChange(id, path, section, isActive)
{
    this.id = id;
    this.path = path;
    this.section = section;
    this.isActive = isActive;
    this.originalSection = section;
    this.originalState = isActive;

    this.hasChanged = function()
    {
        if (this.originalSection != this.section)
        {
            return true;
        }

        if (this.originalState != this.isActive)
        {
            return true;
        }

        return false;
    };

    this.setSection = function(section)
    {
        this.section = section;
    };

    this.setActive = function(activeState)
    {
        this.isActive = activeState;
    }
}

/**
 * drop an extension
 * @param {*} eventObj 
 */
function dropExtension(eventObj)
{
    eventObj.preventDefault();

    var data = eventObj.dataTransfer.getData("text");

    // remove data (object id) from its parent
    // and add it to the holding box;
    var sourceElement = document.getElementById(data);

    sourceElement.parentNode.removeChild(sourceElement);

    eventObj.target.appendChild(sourceElement);

    var sectionId = eventObj.target.getAttribute('sectionid');

    g_extensionChanges.forEach(function(element)
    {
        if (element.id == data)
        {
            element.setSection(sectionId);
            element.setActive(sectionId != null);
        }
    }, this);

    checkForChanges();
}

/**
 * Add an extension
 * @param {*} eventObj 
 */
function addExtension(eventObj)
{
    eventObj.preventDefault();

    var data = eventObj.dataTransfer.getData("text");

    // remove object from where it currently is and place
    // place it in the proper location.
    var sourceElement = document.getElementById(data);

    sourceElement.parentNode.removeChild(sourceElement);

    eventObj.target.appendChild(sourceElement);

    var sectionId = eventObj.target.getAttribute('sectionid');

    if (sectionId == null)
    {
        sectionId = eventObj.target.parentElement.getAttribute('sectionId');
    }
    g_extensionChanges.forEach(function(element)
    {
        if (element.id == data)
        {
            console.log('Section: ' + sectionId);
            element.setSection(sectionId);
            element.setActive(sectionId != null);
        }
    }, this);

    checkForChanges();
}

/**
 * Check drop target to prevent the default
 * @param {*} eventObj 
 */
function checkDropTarget(eventObj)
{
    eventObj.preventDefault();
}

var g_extensionChanges = new Array();

/**
 * start the drag process and create the entry in the array if needed
 * @param {*} eventObj 
 */
function startExtensionDrag(eventObj)
{
    var id = eventObj.target.id;

    eventObj.dataTransfer.setData("text", id);
    var dragObject = document.getElementById(id);

    var parentElement = dragObject.parentElement;
    var parentSectionId = parentElement.getAttribute('sectionid');

//    console.log(parentSectionId);

    var found = false;

    // see if this one has already been modified
    g_extensionChanges.forEach(function(element)
    {
        if (element.id == id)
        {
            // this one
            found = true;
        }
    }, this);

    if (found == false)
    {
        var change = new ExtensionChange(id, dragObject.getAttribute('extpath'), parentSectionId, parentSectionId != null);

        g_extensionChanges.push(change);
    }
}

/**
 * Check for changes in the extensions
 */
function checkForChanges()
{
    var changesDetected = false;

    g_extensionChanges.forEach(function(element)
    {
        if (element.hasChanged() == true)
        {
            changesDetected = true;
        }
    }, this);

    var applyButton = document.getElementById('apply_extension_changes');
    if (changesDetected == true)
    {
        applyButton.removeAttribute('disabled');
    }
    else
    {
        applyButton.setAttribute('disabled', 'disabled');
    }
}

/**
 * Apply the changes to the web server
 * @param {*} siteUrl 
 */
function applyExtensionChanges(siteUrl)
{
    // post the changes
    // if we are here then the button was enabled and we have data in our array to be posted
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

	// ping the server to validate the user name
	var request = new transferJSON(true, siteUrl, thisObj.handleResponse);

	request.addValue(new nameValuePair('adminOption', 'extension')); // could extract this and pass in as part of the url
	request.addValue(new nameValuePair('func', 'updateExtensions'));

    var extensionsArray = new Array();

    g_extensionChanges.forEach(function(element)
    {
        if (element.hasChanged() == true)
        {
            // add this one (path/section)
            extensionsArray.push({path: element.path, section: element.section});
//            request.addValue(new nameValuePair(element.path, element.section)); // login=2.1
        }
    }, this);
    
    request.addValue(new nameValuePair('changes', extensionsArray));
	request.send();
}
