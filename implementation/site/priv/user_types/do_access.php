<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/user_types/utypes_common.php");

$canManage = $g_user->hasPermission('ManageUserTypes');
if (!$canManage) {
	$error = getGS("You do not have the right to change user type permissions.");
	camp_html_display_error($error);
	exit;
}

$uTypeId = Input::Get('UType', 'string', '');
if (is_numeric($uTypeId) && $uTypeId > 0) {
	$userType = new UserType($uTypeId);
	if ($userType->getName() == '') {
		camp_html_display_error(getGS('No such user type.'));
		exit;
	}
} else {
	camp_html_display_error(getGS('No such user type.'));
	exit;
}

$rightsFields = User::GetDefaultConfig();
foreach ($rightsFields as $field=>$value) {
	$val = Input::Get($field, 'string', 'off');
	$userType->setPermission($field, ($val == 'on'));
}
$logtext = getGS('User type $1 changed permissions', $userType->getName());
Log::Message($logtext, $userType->getName(), 123);

$msg = getGS("Permissions successfully modified");
camp_html_add_msg($msg);
camp_html_goto_page("/$ADMIN/user_types/access.php?UType=$uTypeId");

?>