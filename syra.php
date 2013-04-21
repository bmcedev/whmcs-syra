<?php

$dir = realpath(dirname(__FILE__));
include_once "{$dir}/syra/lib/base.php";
include_once "{$dir}/syra/lib/contact.php";
include_once "{$dir}/syra/lib/domain.php";
include_once "{$dir}/syra/lib/host.php";
include_once "{$dir}/syra/lib/transfer.php";
include_once "{$dir}/syra/lib/reseller.php";

// SETUP
function syra_getConfigArray() {
	$configarray = array(
	 "ResellerID" => array( "Type" => "text", "Size" => "20", "Description" => "Enter your reseller id here", ),
	 "APIKey" => array( "Type" => "password", "Size" => "20", "Description" => "Enter your api key here", ),
	 "TestMode" => array( "Type" => "yesno", )
	);
	return $configarray;
}


// GENERIC
function syra_isNullOrEmptyString($string){
  return (!isset($string) || trim($string)==='');
}

function syra_isTestMode($test_mode) {
  if(syra_isNullOrEmptyString($testmode)) {
    if($test_mode=="on") { return true; } else { return false; }
  } else { return false; }    
}

function syra_AuthSettings($params) {
	return array("ResellerID" => $params["ResellerID"], "APIKey" => $params["APIKey"], 
	      "TestMode" => syra_isTestMode($params["TestMode"]));
}

function syra_ProcessAPIErrors($response) {
  $i = 0;
  if (isset($response->Errors)) {	
    $error_message = "";
    foreach ($response->Errors as $error) {  
      if($i==0) { $breaktag = ""; } else { $breaktag = "<br />"; }
      $error_message = $error_message.$breaktag."<strong>".$error->Item.":</strong> ";
      $error_message = $error_message. str_replace("reseller", "account", $error->Message);
      $i++;
    }
    return $error_message;
  } else {
    return "There was an error processing your request";
  }
}


// NAMESERVERS
function syra_GetNameServerArray($params) {
  $nameservers = array();
  for ($i = 1; $i <= 5; $i++) {
    $nameserver = "ns".$i;
    if(!syra_isNullOrEmptyString($params[$nameserver])) {
      array_push($nameservers, array("Host" => $params[$nameserver],
       "IP" => gethostbyname($params[$nameserver]))); 
    }    
  }
  return $nameservers;
}

function syra_ProcessNameServerResponse($response) {
  if (!isset($response->Errors)) {
    if (isset($response->NameServers)) {	  
  	  $i = 0;
      foreach ($response->NameServers as $nameserver) {
        $i++;
        $values["ns".$i] = $nameserver->Host;
      }
    }	 
  } else {
    $values["error"] = syra_ProcessAPIErrors($response);
  }
  return $values;
}

function syra_GetNameservers($params) {
  $auth = syra_AuthSettings($params);
	$syra_domain = new SyraDomain($auth["ResellerID"], $auth["APIKey"], $auth["TestMode"]);
	
	$domain_name = $params["sld"].".".$params["tld"];
	$request = array("DomainName" => $domain_name);	
	
	$response = $syra_domain->info($request);	
	return syra_ProcessNameServerResponse($response); ;
}

function syra_SaveNameservers($params) {
  $auth = syra_AuthSettings($params);
  $syra_domain = new SyraDomain($auth['ResellerID'], $auth['APIKey'], $auth["TestMode"]);
  
  $domain_name = $params["sld"].".".$params["tld"];
	$request = array("DomainName" => $domain_name);
  $nameservers = syra_GetNameServerArray($params);
  
  $domain_info = $syra_domain->info(array("DomainName" => $domain_name));
  
  $request = array("DomainName" => $domain_name,
             "AdminContactIdentifier" => $domain_info->AdminContactIdentifier,
             "BillingContactIdentifier" => $domain_info->BillingContactIdentifier,
             "TechContactIdentifier" => $domain_info->TechContactIdentifier,
             "NameServers" => $nameservers);
  
  $response = $syra_domain->update($request);
  return syra_ProcessNameServerResponse($response);
}


// CONTACTS
function syra_GetContactDetails($params) {
  $auth = syra_AuthSettings($params);
  $reseller = new SyraReseller($auth['ResellerID'], $auth['APIKey'], $auth["TestMode"]);
  $response = $reseller->get_domain_list();
  var_dump($response);
}

function syra_SaveContactDetails($params) {
  $auth = syra_AuthSettings($params);
  var_dump($params);
}


// DOMAINS
function syra_RegisterDomain($params) {
  $auth = syra_AuthSettings($params);
	$syra_domain = new SyraDomain($auth['ResellerID'], $auth['APIKey'], $auth["TestMode"]);	
	
	$domain_name = $params["sld"].".".$params["tld"];
	
	$request = array(
	  "DomainName" => $sld.".".$tld,
	  "RegistrationPeriod" => $params["regperiod"],
	  "RegistrantContactIdentifier" => "",
	  #"AdminContactIdentifier" => "",
	  #"BillingContactIdentifier" => "",
	  #"TechContactIdentifier" => "",
	  "Eligibility" => "",	  
	  "NameServers" => syra_GetNameServerArray($params)
	);		
			
	//$response = $syra_domain->create($request);	
	
	if (!isset($response->Errors)) {	 
  } else {
    $values["error"] = syra_ProcessAPIErrors($response);
  }
  
  return $values;
}


// DOMAINS LOCK STATUS
// INVESTIGATE IT APPEARS LOCK STATUS MAY NOT BE UPDATABLE VIA API :(
//function syra_GetRegistrarLock($params) {  
//  $auth = syra_AuthSettings($params);
//	$syra_domain = new SyraDomain($auth['ResellerID'], $auth['APIKey'], $auth["TestMode"]);		  
//  $domain_name = $params["sld"].".".$params["tld"];
//  $domain_info = $syra_domain->info(array("DomainName" => $domain_name));
//  if (isset($domain_info->LockStatus)) {
//    return strtolower($domain_info->LockStatus);
//  }  
//}
//
//function syra_SaveRegistrarLock($params) {
//  $auth = syra_AuthSettings($params);
//	$syra_domain = new SyraDomain($auth['ResellerID'], $auth['APIKey'], $auth["TestMode"]);		  
//  
//  $domain_name = $params["sld"].".".$params["tld"];
//  $domain_info = $syra_domain->info(array("DomainName" => $domain_name));
//  
//  if ($params["lockenabled"]) {
//		$lockstatus="Locked";
//	} else {
//		$lockstatus="Unlocked";
//	}
//  
//  $request = array("DomainName" => $domain_name,
//             "AdminContactIdentifier" => $domain_info->AdminContactIdentifier,
//             "BillingContactIdentifier" => $domain_info->BillingContactIdentifier,
//             "TechContactIdentifier" => $domain_info->TechContactIdentifier,
//             "NameServers" => $domain_info->NameServers,
//             "LockStatus" => $lockstatus);
//  
//  $response = $syra_domain->update($request);
//  
//  if (isset($domain_info->LockStatus)) {
//    $values["lockenabled"] = strtolower($domain_info->LockStatus);
//  } else {
//    $values["error"] = syra_ProcessAPIErrors($response);
//  }
//  return $values;
//}