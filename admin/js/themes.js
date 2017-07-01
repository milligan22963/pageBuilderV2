/**
 * @brief called to activate a theme in the sytem
 * 
 * @param[in] siteUrl - the url to query
 * @param[in] themeId - the id associated w/ the theme (see info.xml in the theme folder)
 */
function activateTheme(siteUrl, themeId)
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

	// ping the server to validate the user name
	var request = new transferJSON(true, siteUrl, thisObj.handleResponse);

	request.addValue(new nameValuePair('adminOption', 'theme')); // could extract this and pass in as part of the url
	request.addValue(new nameValuePair('func', 'activateTheme'));
	request.addValue(new nameValuePair('themeId', themeId));

	request.send();
}