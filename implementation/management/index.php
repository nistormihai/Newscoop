<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');

// read server parameters
$env_vars["HTTP_HOST"] = getenv("HTTP_HOST");
$env_vars["DOCUMENT_ROOT"] = getenv("DOCUMENT_ROOT");
$env_vars["REMOTE_ADDR"] = getenv("REMOTE_ADDR");
$env_vars["PATH_TRANSLATED"] = getenv("PATH_TRANSLATED");
$env_vars["REQUEST_METHOD"] = getenv("REQUEST_METHOD");
$env_vars["REQUEST_URI"] = getenv("REQUEST_URI");

// read parameters
$parameters = camp_read_parameters($query_string);
$cookies = camp_read_cookies($cookies_string);

camp_debug_msg("request method: " . getenv("REQUEST_METHOD"));
camp_debug_msg("query string: $query_string");
camp_debug_msg("cookies string: $cookies_string");

$msg = camp_create_url_request_message($env_vars, $parameters, $cookies);
for ($i = 1; $i <= 10; $i++) {
	$size_read = camp_read_parser_output(camp_send_message_to_parser($msg));
	if ($size_read > 0) {
		break;
	}
	usleep(200000);
}

?>