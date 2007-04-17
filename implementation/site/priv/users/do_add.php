<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
verify_user_type();
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canManage) {
	camp_html_display_error(getGS('You do not have the right to create user accounts.'));
	exit;
}

// define fields arrays
$fields = array('UName', 'Name', 'Title', 'Gender', 'Age', 'EMail', 'City', 'StrAddress',
	'State', 'CountryCode', 'Phone', 'Fax', 'Contact', 'Phone2', 'PostalCode', 'Employer',
	'EmployerType', 'Position');
$notNullFields = array('UName'=>'Account name', 'Name'=>'Full Name', 'EMail'=>'E-Mail', 'Type'=>'Type');

// read fields values
$errorField = "";
$typeParam = 'uType=' . urlencode($uType);
$backLink = "/$ADMIN/users/edit.php?$typeParam";
foreach ($fields as $index=>$field) {
	$fieldValues[$field] = Input::Get($field, 'string', '');
	if ($fieldValues[$field] != '')
		$backLink .= "&" . urlencode($field) . "=" . urlencode($fieldValues[$field]);
	elseif (array_key_exists($field, $notNullFields) && $errorField == "")
		$errorField = $field;
}

// set the Reader field
$Type = Input::Get('Type', 'string', '');
$fieldValues['Reader'] = ($uType == 'Subscribers') ? 'Y' : 'N';
if ( ($uType == 'Staff') && ($Type == '') && ($errorField == '') ) {
	$errorField = 'Type';
}

// display errors if found
if ($errorField != "") {
	$desc = $notNullFields[$errorField];
	if ($errorField == 'Type') {
		$errorMsg = getGS('You must select a $1', $desc);
	} else {
		$errorMsg = getGS('You must complete the $1 field.', $desc);
	}
	camp_html_add_msg($errorMsg);
	camp_html_goto_page($backLink);
}

if (User::UserNameExists($fieldValues['UName'])) {
	$errorMsg = getGS('That user name already exists, please choose a different login name.');
	camp_html_add_msg($errorMsg);
	camp_html_goto_page($backLink);
}

if (User::EmailExists($fieldValues['EMail'])) {
    $errorMsg = getGS('Another user is registered with that e-mail address, please choose a different one.');
    camp_html_add_msg($errorMsg);
    camp_html_goto_page($backLink);
}

// read password
$password = Input::Get('password', 'string', '');
$passwordConf = Input::Get('passwordConf', 'string', '');
if (strlen($password) < 6 || $password != $passwordConf) {
	$errorMsg = getGS('The password must be at least 6 characters long and both passwords should match.');
	camp_html_add_msg($errorMsg);
	camp_html_goto_page($backLink);
}

// create user
$editUser = new User();
$phorumUser = new Phorum_user();
if (!$phorumUser->UserNameExists($fieldValues['UName']) &&
			$editUser->create($fieldValues, $password)) {
    $editUser->fetch();
	$editUser->setPassword($password);
	if ($uType == 'Staff') {
		$editUser->setUserType($Type);
	}
	$phorumUser->create($fieldValues['UName'], $password, $fieldValues['EMail'], $editUser->getUserId());
	camp_html_add_msg(getGS('User account $1 was created successfully.', $editUser->getUserName()), "ok");
	camp_html_goto_page("/$ADMIN/users/edit.php?User=".$editUser->getUserId()."&$typeParam");
} else {
	camp_html_add_msg(getGS('The user account could not be created.'));
	camp_html_goto_page($backLink);
}

?>
