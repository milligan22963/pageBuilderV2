function Activity(name)
{
	this.name = name;
	this.subscribers = new Array();

	this.subscribe = function(callback)
	{
		this.subscribers.push(callback);
	};

	this.publish = function(params)
	{
		this.subscribers.forEach(function(callback)
		{
			callback(params);
		}, this);
	};

	this.getName = function()
	{
		return this.name;
	}
}

function Broker()
{
	this.activities = new Array();

	this.subscribe = function(activity, callback)
	{
		var found = false;

		this.activities.forEach(function(element)
		{
			if (element.getName() == activity)
			{
				element.addSubscriber(callback);
				found = true;
			}
		}, this);

		if (found == false)
		{
			var newActivity = new Activity(activity);

			newActivity.subscribe(callback);

			this.activities.push(newActivity);
		}
	};

	this.publish = function(activity, params)
	{
		this.activities.forEach(function(element)
		{
			if (element.getName() == activity)
			{
				element.publish(params);
			}
		}, this);
	};
}

var System = (function ()
{
    var instance;
 
    function createInstance()
	{
        var broker = new Broker();

        return broker;
    }
 
    return {
        getInstance: function ()
		{
            if (!instance)
			{
                instance = createInstance();
            }
            return instance;
        }
    };
})();

function enableElement(elementId)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		element.disabled = false;
	}
}

function disableElement(elementId)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		element.disabled = true;
	}
}

/**
 * @brief set the visibility of a field
 */
function setVisibility(fieldId, isVisible)
{
	var element = document.getElementById(fieldId);

	if (element != null)
	{
		element.style.visibility = isVisible == true ? 'visible' : 'hidden';
	}
}

function setText(fieldId, text)
{
	var element = document.getElementById(fieldId);
	if (element != null)
	{
		element.textContent = text;
	}
}
function setTitle(fieldId, title)
{
	var element = document.getElementById(fieldId);

	if (element != null)
	{
		element.title = title;
	}
}
function setValue(fieldId, value)
{
	var element = document.getElementById(fieldId);
	if (element != null)
	{
		element.value = value;
	}
}

function getValue(fieldId)
{
	var element = document.getElementById(fieldId);
	var value = null;

	if (element != null)
	{
		value = element.value;
	}

	return value;
}

function isObjEmpty(objToCheck)
{
	var isEmpty = false;

	// if it is an object and it has keys then it isn't empty
	if ((objToCheck.constructor === Object) && (Object.keys(objToCheck).length === 0))
	{
		isEmpty = true;
	}

	return isEmpty;
}

function addAttributes(elementId, attributeObject)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		Object.keys(attributeObject).forEach(function(key,index)
		{
			element.setAttribute(key, attributeObject[key]);
		});
	}
}

function addEventHandler(elementId, eventName, method)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		element.addEventListener(eventName, method);
	}
}

function appendHtml(elementId, addedText, before)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		var placement = 'beforeend';

		if (before == true)
		{
			placement = 'afterbegin';
		}

		element.insertAdjacentHTML(placement, addedText);
	}
}

function setStyle(elementId, styleOption, styleValue)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		var newStyle = styleOption + ':' + styleValue;
		var currentStyle = element.getAttribute('style');

		if (currentStyle != null)
		{
			currentStyle =  currentStyle + ';' + newStyle;
		}
		else
		{
			currentStyle = newStyle;
		}

		element.setAttribute('style', currentStyle);
	}
}

function setStyles(elementId, styleOptions)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		var newStyle = null;

		Object.keys(styleOptions).forEach(function(key,index)
		{
			var nextStyle = key + ':' + styleOptions[key];
			if (newStyle != null)
			{
				newStyle = newStyle + ';' + nextStyle;
			}
			else
			{
				newStyle = nextStyle;
			}
		});

		if (newStyle != null)
		{
			var currentStyle = element.getAttribute('style');
			if (currentStyle != null)
			{
				currentStyle =  currentStyle + ';' + newStyle;
			}
			else
			{
				currentStyle = newStyle;
			}

			element.setAttribute('style', currentStyle);
		}
	}	
}

function removeChildren(elementId)
{
	var element = document.getElementById(elementId);

	if (element != null)
	{
		while (element.lastChild)
		{
			element.removeChild(element.lastChild);
		}
	}
}

/**
 * @brief checks to see if we can access local storage and use it opposed to cookies
 * 
 * @return true of available false otherwise
 */
function localStorageAvailable()
{
	var retValue = false;
	try
	{
	    if ('localStorage' in window && window['localStorage'] !== null)
	    {
	    	retValue = true;
	    }
	}
	catch (e)
	{
		retValue = false;
	}
	
	return retValue;
}

/**
 * @brief Used to set a cookie via javascript
 * 
 * @param[in] cookieId
 * @param[in] cookieValue
 * @param[in] expiration (can be null) otherwise in numDays
 * @param[in] path - default is '/'
 */
function setCookie (cookieId, cookieValue, expiration, path)
{
	var expirationDate = "";
	var pathValue = '; path=';
	
    if (expiration != null)
    {
        var date = new Date();
        
        date.setTime(date.getTime() + (expiration * 24 * 3600000));
        
        expirationDate = "; expires=" + date.toUTCString();
    }

    if (path == null)
    {
    	path = '/';
    }
	pathValue += path;
    document.cookie = cookieId + "=" + cookieValue + expirationDate + pathValue;
}

/**
 * @brief Used to get a cookie via javascript
 * 
 * @param[in] cookieId
 * 
 * @return cookieValue
 */
function getCookie(cookieId)
{
    var cookieName = cookieId + "=";
    var retValue = null;
    var cookie = document.cookie.split(';');
    
    // split on ';' however extra params could be used to define a cookie
    for (var index = 0; index < cookie.length; index++)
    {
        var c = cookie[index];
        
        // trim white space
        while (c.charAt(0) == ' ')
        {
        	c = c.substring(1, c.length);
        }
        
        // if we have the name= at the beginning, its our cookie
        if (c.indexOf(cookieName) == 0)
        {
        	retValue = c.substring(cookieName.length, c.length);
        }
    }
    return retValue;
}

/**
 * @brief Used to remove a cookie
 * 
 * @param[in] cookieId - id for the cookie i.e. name
 */
function deleteCookie(cookieId)
{
	// set the cookie to a blank value and expire it i.e. -1 is before now
    setCookie(cookieId, "", -1);
}

/**
 * @brief Used to save data via javascript
 * 
 * @param[in] dataId
 * @param[in] dataValue
 * @param[in] expiration (can be null) otherwise in numDays
 */
function saveData(dataId, dataValue, expiration)
{
	if (localStorageAvailable() == true)
	{
		var expirationDate = "";
		
	    if (expiration != null)
	    {
	        var date = new Date();
	        
	        date.setTime(date.getTime() + (expiration * 24 * 3600000));
	        
	        expirationDate = date.toUTCString();
	    }
	    localStorage[dataId] = JSON.stringify(dataValue);
	    localStorage[dataId + 'date'] = expirationDate;
	}
	else
	{
		setCookie(dataId, dataValue, expiration, '/');
	}
}

/**
 * @brief Used to get a data value via javascript
 * 
 * @param[in] dataId
 * 
 * @return dataValue, null if not found our expired
 */
function restoreData(dataId)
{
    var dataValue = null;
    
	if (localStorageAvailable() == true)
	{
		dataValue = localStorage[dataId];
		if (dataValue != null)
		{
			expiration = localStorage[dataId + 'date'];
			var date = new Date();
			var expirationTime = Date.parse(expiration);
			if (expirationTime <= date.getTime())
			{
				dataValue = null;
			}
			else
			{
				dataValue = JSON.parse(dataValue);
			}
		}
	}
	else
	{
		dataValue = getCookie(dataId);
	}
    return dataValue;
}

/**
 * @brief Used to delete a data value via javascript
 * 
 * @param[in] dataId
 */
function deleteData(dataId)
{
	if (localStorageAvailable() == true)
	{
		dataValue = localStorage[dataId];
		if (dataValue != null)
		{
			localStorage.removeItem(dataId);
			localStorage.removeItem(dataId + 'date');
		}
	}
	else
	{
		deleteCookie(dataId);
	}
}

/**
 * @brief creates a name value pair object to store json
 * 			parameters to pass to the end point
 * 
 * @param[in] name - the name of the value
 * @param[in] value - the value for the given parameter
 */
function nameValuePair(name, value)
{
	this.name = name;
	this.value = value;
	
	this.getName = function()
	{
		return this.name;
	};
	
	this.getValue = function()
	{
		return this.value;
	};
}

/**
 * @brief transfer json data between the client and the server
 * 
 * @param[in] isPost indicates a post opposed to a get, post if true, false for get
 * @param[in] targetUrl - the url to get/post
 * @param[in] callback - a function to call when the post/get has been processed
 */
function transferJSON(isPost, targetUrl, callback)
{
	this.url = targetUrl;
	this.callback = callback;
	this.serverRequest  = null;
	this.values = new Array();
	this.isPost = isPost;

	var thisObj = this;
	
	this.addValue = function(nvp)
	{
		thisObj.values.push(nvp);
	};

	this.send = function()
	{
		thisObj.serverRequest = new XMLHttpRequest();
		
		thisObj.serverRequest.onreadystatechange = function()
		{
			if (this.readyState == 4)
			{
				if (this.status == 200)
				{
					console.log('Response: ' + this.responseText);
					var data = JSON.parse(this.responseText);
					
					if (thisObj.callback != null)
					{
						thisObj.callback(data);
					}
				}
			}
		};
		
		if (thisObj.isPost == true)
		{
			thisObj.serverRequest.open("POST", thisObj.url, true);
			thisObj.serverRequest.setRequestHeader('Content-Type', 'application/json');
			thisObj.serverRequest.send(JSON.stringify(thisObj.values));	
		}
		else
		{
			var target = thisObj.url;
			var additionalData = null;
			thisObj.values.forEach(function(nvp)
			{
				if (additionalData == null)
				{
					// first one will be a '?'
					additionalData = '?';
				}
				else
				{
					additionalData += '&';
				}
				additionalData += nvp.getName() + "=" + nvp.getValue();
			}, thisObj);

			if (additionalData != null)
			{
				target += additionalData;
			}
			
			thisObj.serverRequest.open("GET", target, true);			
			thisObj.serverRequest.setRequestHeader('Content-Type', 'application/json');
			thisObj.serverRequest.send();	
		}
	};
}
