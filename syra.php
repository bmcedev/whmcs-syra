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

