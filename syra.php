<?php

$dir = realpath(dirname(__FILE__));

include_once "{$dir}/syra/syra.php";

function syra_getConfigArray() {
	$configarray = array(
	 "ResellerID" => array( "Type" => "text", "Size" => "20", "Description" => "Enter your reseller id here", ),
	 "APIKey" => array( "Type" => "password", "Size" => "20", "Description" => "Enter your api key here", ),
	 "TestMode" => array( "Type" => "yesno", )
	);
	return $configarray;
}

