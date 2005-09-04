<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/UserType.php');

list($access, $User) = check_basic_access($_REQUEST);

read_user_common_parameters(); // $uType, $userOffs, $lpp, search parameters
verify_user_type();
compute_user_rights($User, $canManage, $canDelete);

$userId = Input::Get('User', 'int', 0);
$editUser = new User($userId);
if ($editUser->getUserName() == '') {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}

if (!$canManage && $editUser->getId() != $User->getId()) {
	$errMsg = getGS('You do not have the right to change user account information.');
	camp_html_display_error($errMsg);
	exit;
}

$typeParam = 'uType=' . urlencode($uType);
$isReader = $uType == 'Readers' ? 'Y' : 'N';
$setPassword = Input::Get('setPassword', 'string', 'false') == 'true';
$customizeRights = Input::Get('customizeRights', 'string', 'false') == 'true';


if ($setPassword) {
	$password = Input::Get('password', 'string', 0);
	$passwordConf = Input::Get('passwordConf', 'string', 0);
	$backLink = "/$ADMIN/users/edit.php?$typeParam&User=".$editUser->getId();
	
	if ($userId == $User->getId()) {
		$oldPassword = Input::Get('oldPassword');
		if (!$editUser->isValidPassword($oldPassword)) {
			$resMsg = getGS('The password you typed is incorrect.');
			header("Location: $backLink&res=ERROR&resMsg=" . urlencode($resMsg));
			exit;
		}
	}
	if (strlen($password) < 6 || $password != $passwordConf) {
		$resMsg = 'The password must be at least 6 characters long and both passwords should match.';
		header("Location: $backLink&res=ERROR&resMsg=" . urlencode(getGS($resMsg)));
		exit;
	}
	
	$editUser->setPassword($password);
	$logtext = getGS('Password changed for $1', $editUser->getUserName());
	Log::Message($logtext, $User->getUserName(), 54);
}


// save user data
$editUser->setProperty('Name', Input::Get('Name', 'string', ''), false);
$editUser->setProperty('Title', Input::Get('Title', 'string', ''), false);
$editUser->setProperty('Gender', Input::Get('Gender', 'string', ''), false);
$editUser->setProperty('Age', Input::Get('Age', 'string', ''), false);
$editUser->setProperty('EMail', Input::Get('EMail', 'string', ''), false);
$editUser->setProperty('City', Input::Get('City', 'string', ''), false);
$editUser->setProperty('StrAddress', Input::Get('StrAddress', 'string', ''), false);
$editUser->setProperty('State', Input::Get('State', 'string', ''), false);
$editUser->setProperty('CountryCode', Input::Get('CountryCode', 'string', ''), false);
$editUser->setProperty('Phone', Input::Get('Phone', 'string', ''), false);
$editUser->setProperty('Fax', Input::Get('Fax', 'string', ''), false);
$editUser->setProperty('Contact', Input::Get('Contact', 'string', ''), false);
$editUser->setProperty('Phone2', Input::Get('Phone2', 'string', ''), false);
$editUser->setProperty('PostalCode', Input::Get('PostalCode', 'string', ''), false);
$editUser->setProperty('Employer', Input::Get('Employer', 'string', ''), false);
$editUser->setProperty('EmployerType', Input::Get('EmployerType', 'string', ''), false);
$editUser->setProperty('Position', Input::Get('Position', 'string', ''), false);
$editUser->commit();

$logtext = getGS('User account information changed for $1', $editUser->getUserName());
Log::Message($logtext, $User->getUserName(), 56);


if ($editUser->isAdmin() && $customizeRights && $canManage) {
	// save user customized rights
	$rightsFields = array('ManagePub'=>'N', 'DeletePub'=>'N', 'ManageIssue'=>'N',
		'DeleteIssue'=>'N', 'ManageSection'=>'N', 'DeleteSection'=>'N', 'AddArticle'=>'N',
		'ChangeArticle'=>'N', 'DeleteArticle'=>'N', 'AddImage'=>'N', 'ChangeImage'=>'N',
		'DeleteImage'=>'N', 'ManageTempl'=>'N', 'DeleteTempl'=>'N', 'ManageUsers'=>'N',
		'ManageReaders'=>'N', 'ManageSubscriptions'=>'N', 'DeleteUsers'=>'N',
		'ManageUserTypes'=>'N', 'ManageArticleTypes'=>'N', 'DeleteArticleTypes'=>'N',
		'ManageLanguages'=>'N', 'DeleteLanguages'=>'N', 'MailNotify'=>'N',
		'ManageCountries'=>'N', 'DeleteCountries'=>'N', 'ViewLogs'=>'N', 'ManageLocalizer'=>'N',
		'ManageIndexer'=>'N', 'Publish'=>'N', 'ManageTopics'=>'N', 'EditorBold'=>'N',
		'EditorItalic'=>'N', 'EditorUnderline'=>'N', 'EditorUndoRedo'=>'N',
		'EditorCopyCutPaste'=>'N', 'EditorImage'=>'N', 'EditorTextAlignment'=>'N',
		'EditorFontColor'=>'N', 'EditorFontSize'=>'N', 'EditorFontFace'=>'N',
		'EditorTable'=>'N', 'EditorSuperscript'=>'N', 'EditorSubscript'=>'N',
		'EditorStrikethrough'=>'N', 'EditorIndent'=>'N', 'EditorListBullet'=>'N',
		'EditorListNumber'=>'N', 'EditorHorizontalRule'=>'N', 'EditorSourceView'=>'N',
		'EditorEnlarge'=>'N', 'EditorTextDirection'=>'N', 'EditorLink'=>'N', 'EditorSubhead'=>'N');
	foreach ($rightsFields as $field=>$value) {
		$val = Input::Get($field, 'string', 'off');
		if ($val == 'on')
			$rightsFields[$field] = 'Y';
		$queryStr .= ", `$field` = '" . $rightsFields[$field] . "'";
	}
	
	$queryStr = "UPDATE UserPerm SET " . substr($queryStr, 2) ." WHERE IdUser = $userId";
	if ($Campsite['db']->Execute($queryStr)) {
		$logtext = getGS('Permissions for $1 changed',$editUser->getUserName());
		Log::Message($logtext, $User->getUserName(), 55);
	}
}
if ($editUser->isAdmin() && !$customizeRights && $canManage) {
	// save user rights based on existing user type
	$userTypeName = Input::Get('UserType', 'string', '');
	if ($userTypeName != "") {
		$editUser->setUserType($userTypeName);
		$logtext = getGS('Permissions for $1 changed',$editUser->getUserName());
		Log::Message($logtext, $User->getUserName(), 55);
	}
}

$resParams = "res=OK&resMsg=" . getGS("User '$1' information was changed successfully.",
	$editUser->getUserName());
$editUser->fetch();
if ($editUser->getUserName() == $User->getUserName() && !$editUser->hasPermission('ManageUsers')) {
	header("Location: /$ADMIN/");
	exit(0);
}
header("Location: /$ADMIN/users/edit.php?$typeParam&User=" . $editUser->getId() . "&$resParams");

?>
