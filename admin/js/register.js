/**
 * @brief called to check a username to see if it is already in use or not
 * 
 * @param[in] siteUrl - the url to query
 * @param[in] targetName - the user name to check
 * @param[in] errorField - the field to show hide when an error is detected
 */
function checkUserName(siteUrl, targetNameField, errorField)
{
	this.targetNameField = targetNameField;
	this.errorField = errorField;

	var thisObj = this;

	var userNameElement = document.getElementById(this.targetNameField);

	this.handleResponse = function(responseData)
	{
		if (responseData.success == 'false')
		{
			userNameElement.setCustomValidity('User name is already in use.');
			setVisibility(thisObj.errorField, true);
		}
		else
		{
			userNameElement.setCustomValidity('');
			setVisibility(thisObj.errorField, false);
		}
	};

	// ping the server to validate the user name
	var request = new transferJSON(false, siteUrl + '&new_user_name=' + userNameElement.value, this.handleResponse);
	
	request.addValue(new nameValuePair('func', 'checkUserName'));
	request.send();
}

/**
 * @brief compares the email addresses to check if they are equal or not
 * 
 * @param[in] emailAddrFieldOne - the first email addr field to compare
 * @param[in] emailAddrFieldTwo - the second email addr field to compare
 * @param[in] errorField - the field to show / hide when an error is detected
 */
function compareEmailAddresses(emailAddrFieldOne, emailAddrFieldTwo, errorField)
{
	var addr1 = document.getElementById(emailAddrFieldOne);
	var addr2 = document.getElementById(emailAddrFieldTwo);

	setVisibility(errorField, false);

	if ((addr1 != null) && (addr2 != null))
	{
		if ((addr1.value.length > 0) && (addr2.value.length > 0))
		{
			var notEqual = false;

			if (addr1.value.length != addr2.value.length)
			{
				notEqual = true;
			}
			else if (addr1.value != addr2.value)
			{
				notEqual = true;
			}

			setVisibility(errorField, notEqual);
		}
	}
}

function registerUser(siteUrl, userNameField, userPasswordField, emailAddressOne, emailAddressTwo)
{
	this.userNameField = userNameField;

	var thisObj = this;

	var userNameElement = document.getElementById(this.userNameField);
	var userPasswordElement = document.getElementById(userPasswordField);
	var emailAddrOneElement = document.getElementById(emailAddressOne);
	var emailAddrTwoElement = document.getElementById(emailAddressTwo);

	this.handleResponse = function(responseData)
	{
		if (responseData.success == 'false')
		{
		}
	};

	// ping the server to validate the user name
	var request = new transferJSON(true, siteUrl, thisObj.handleResponse);

	request.addValue(new nameValuePair('adminOption', 'register')); // could extract this and pass in as part of the url
	request.addValue(new nameValuePair('func', 'registerNewUser'));
	request.addValue(new nameValuePair(userNameField, userNameElement.value));
	request.addValue(new nameValuePair(userPasswordField, userPasswordElement.value));
	request.addValue(new nameValuePair(emailAddressOne, emailAddrOneElement.value));
	request.addValue(new nameValuePair(emailAddressTwo, emailAddrTwoElement.value));

	request.send();
}