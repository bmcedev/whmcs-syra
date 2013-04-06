# WHMCS Syra Module

## Developer Notes

WHMCS Documentation: http://docs.whmcs.com/Registrar_Module_Developer_Docs
Syra API Documentation: doc/ResellerAPIGude.pdf

### Logging

The following function can be used for logging in the WHMCS Module log

logModuleCall($module,$action,$requeststring,$responsedata,$processeddata,$replacevars);

http://docs.whmcs.com/Provisioning_Module_Developer_Docs#Module_Logging

$module - This should be the name of your module, for example "cpanel", "plesk", "resellerclub", etc...

$action - This should be the action being performed, ie. create, suspend, register, etc...

$requeststring - This should be the variables being posted to the remote API. This can be in either string or array format.

$responsedata - This is where you return the raw API response. This can be in either string or array format also.

$processeddata - Similar to the above, but this can be used for a post processing API response, such as after conversion into a friendly format, an array for example

$replacevars - This accepts an array of strings to replace, so for example you might want to pass the username and password for the API into this function, which would then be replaced with *'s wherever they appear in either the request or response strings for extra security
