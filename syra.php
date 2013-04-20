<?php

$dir = realpath(dirname(__FILE__));
include_once "{$dir}/syra/lib/base.php";
include_once "{$dir}/syra/lib/contact.php";
include_once "{$dir}/syra/lib/domain.php";
include_once "{$dir}/syra/lib/host.php";
include_once "{$dir}/syra/lib/transfer.php";
include_once "{$dir}/syra/lib/reseller.php";

function syra_getConfigArray() {
	$configarray = array(
	 "ResellerID" => array( "Type" => "text", "Size" => "20", "Description" => "Enter your reseller id here", ),
	 "APIKey" => array( "Type" => "password", "Size" => "20", "Description" => "Enter your api key here", ),
	 "TestMode" => array( "Type" => "yesno", )
	);
	return $configarray;
}

function syra_isTestMode($test_mode) {
  if($test_mode=="on") {
    return true;
  } else {
    return false;
  }
}

function syra_processAPIErrors($response) {
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

function syra_GetNameservers($params) {
  $reseller_id = $params["ResellerID"];
	$api_key = $params["APIKey"];
	$test_mode = syra_isTestMode($params["TestMode"]);
	$tld = $params["tld"];
	$sld = $params["sld"];
	
	$request = array("DomainName" => $sld.".".$tld);
	$syra_domain = new SyraDomain($reseller_id, $api_key, $test_mode);
	
	// TODO: MODULE LOG & POSSIBLE CACHING
	$response = $syra_domain->info($request);	
	
	//var_dump($response);
	if (isset($response->NameServers)) {	  
	  $i = 0;
    foreach ($response->NameServers as $nameserver) {
      $i++;
      $values["ns".$i] = $nameserver->Host;
    }
  } else {
    $values["error"] = syra_processAPIErrors($response);
  }	
	return $values;
}
