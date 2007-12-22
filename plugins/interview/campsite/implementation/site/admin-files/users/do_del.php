<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/Log.php");

read_user_common_parameters(); // $uType, $userOffs, $ItemsPerPage, search parameters
verify_user_type();
compute_user_rights($g_user, $canManage, $canDelete);
if (!$canDelete) {
	camp_html_display_error(getGS('You do not have the right to delete user accounts.'));
	exit;
}

$userId = Input::Get('User', 'int', 0);
$editUser =& new User($userId);
if (!$editUser->exists()) {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}
$editUser->delete();

$uName = $editUser->getUserName();
if ($phorumUser = Phorum_user::GetByUserName($uName)) {
	$phorumUser->delete();
}

$typeParam = 'uType=' . urlencode($uType);
camp_html_add_msg(getGS('User account $1 was deleted successfully.', $uName), "ok");
camp_html_goto_page("/$ADMIN/users/?$typeParam");

?>
