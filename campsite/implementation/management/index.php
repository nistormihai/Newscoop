<?php

header("Content-type: text/html; charset=UTF-8");

global $_SERVER;
global $Campsite;
global $DEBUG;

// initialize needed global variables
$_SERVER['DOCUMENT_ROOT'] = getenv("DOCUMENT_ROOT");

require_once($_SERVER['DOCUMENT_ROOT'].'/configuration.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/parser_utils.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/db_connect.php');

require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/url_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/wrapper_functions.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/settings.ini.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/phpwrapper/smarty_functions.php';

## define the used url type for phpwrapper
defineURLType();

// read server parameters
$env_vars["HTTP_HOST"] = getenv("HTTP_HOST");
$env_vars["DOCUMENT_ROOT"] = getenv("DOCUMENT_ROOT");
$env_vars["REMOTE_ADDR"] = getenv("REMOTE_ADDR");
$env_vars["PATH_TRANSLATED"] = getenv("PATH_TRANSLATED");
$env_vars["REQUEST_METHOD"] = getenv("REQUEST_METHOD");
$env_vars["REQUEST_URI"] = getenv("REQUEST_URI");
$env_vars["SERVER_PORT"] = trim(getenv("SERVER_PORT"));
if ($env_vars["SERVER_PORT"] == "") {
	$env_vars["SERVER_PORT"] = 80;
}

// read parameters
$parameters = camp_read_parameters($query_string);
if (isset($parameters["ArticleCommentSubmitResult"])) {
	unset($parameters["ArticleCommentSubmitResult"]);
}
$cookies = camp_read_cookies($cookies_string);

camp_debug_msg("request method: " . getenv("REQUEST_METHOD"));
camp_debug_msg("query string: $query_string");
camp_debug_msg("cookies string: $cookies_string");

if (isset($parameters["submitComment"])
		&& trim($parameters["submitComment"]) != "") {
	require_once($_SERVER['DOCUMENT_ROOT'].'/comment_lib.php');
	unset($parameters["submitComment"]);
	camp_submit_comment($env_vars, $parameters, $cookies);
} else {
	camp_send_request_to_parser($env_vars, $parameters, $cookies);
}

?>