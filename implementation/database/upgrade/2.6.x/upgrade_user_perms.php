<?php

require_once("database_conf.php");
require_once("install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];

if (!mysql_connect($db_host, $db_user, $db_passwd)) {
	die("Unable to connect to the database.\n");
}

if (!mysql_select_db($db_name)) {
	die("Unable to use the database " . $db_name . ".\n");
}


//
// Add new permissions for each user.
//
if (!($res = mysql_query("SELECT DISTINCT(fk_user_id) FROM UserConfig WHERE fk_user_id!=0"))) {
	die("Unable to read from the database.\n");
}
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	// Add the "AddAudioclip" permission
	$sql = "INSERT INTO UserConfig (fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'AddAudioclip', 'Y')";
	mysql_query($sql)
	
	// Add the "AttachAudioclipToArticle" permission
	$sql = "INSERT INTO UserConfig (fk_user_id, varname, value) "
			." VALUES (".$row['fk_user_id'].", 'AttachAudioclipToArticle', 'Y')";
	mysql_query($sql);
}


// 
// Add new permissions to the UserType table.
//
if (!($res = mysql_query("SELECT DISTINCT(user_type_name) FROM UserTypes"))) {
	die("Unable to read from the database.\n");
}
while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
	$userTypeName = $row['user_type_name'];
	
	// Add new permissions

	// Add the "AddAudioclip" permission
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'AddAudioclip', 'Y')";
	mysql_query($sql);

	// Add the "AttachAudioclipToArticle" permission.
	$sql = "INSERT INTO UserTypes(user_type_name, varname, value) "
			." VALUES ('$userTypeName', 'AttachAudioclipToArticle', 'Y')";
	mysql_query($sql);
}

?>
