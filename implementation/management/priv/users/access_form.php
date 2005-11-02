<?php

require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/users_common.php");

list($access, $User) = check_basic_access($_REQUEST);
$uType = 'Staff';
compute_user_rights($User, $canManage, $canDelete);
if (!$canManage) {
	$error = getGS("You do not have the right to change user account permissions.");
	camp_html_display_error($error);
	exit;
}

$userId = Input::Get('User', 'int', 0);
if ($userId > 0) {
	$editUser = new User($userId);
	if ($editUser->getUserName() == '') {
		camp_html_display_error(getGS('No such user account.'));
		exit;
	}
} else {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}

$content_group = array('ManagePub'=>'User may add/change publications',
	'DeletePub'=>'User may delete publications',
	'ManageIssue'=>'User may add/change issues',
	'DeleteIssue'=>'User may delete issues',
	'ManageSection'=>'User may add/change sections',
	'DeleteSection'=>'User may delete sections',
	'AddArticle'=>'User may add articles',
	'ChangeArticle'=>'User may change articles',
	'Publish'=>'User may publish articles',
	'DeleteArticle'=>'User may delete articles',
	'AddImage'=>'User may add images',
	'ChangeImage'=>'User may change images',
	'DeleteImage'=>'User may delete images',
	'ManageTopics'=>'User may manage topics');

$templates_group = array('ManageTempl'=>'User may manage templates',
	'DeleteTempl'=>'User may delete templates');

$administrative_group = array('InitializeTemplateEngine'=>'User may restart the template engine');

$users_group = array('ManageUsers'=>'User may add/change staff accounts and passwords',
	'DeleteUsers'=>'User may delete staff accounts',
	'ManageReaders'=>'User may add/change subscribers accounts and passwords',
	'ManageSubscriptions'=>'User may manage user subscriptions',
	'ManageUserTypes'=>'User may manage account types');

$article_types_group = array('ManageArticleTypes'=>'User may add/change article types',
	'DeleteArticleTypes'=>'User may delete article types');

$languages_group = array('ManageLanguages'=>'User may add languages and manage language information',
	'DeleteLanguages'=>'User may delete languages');

$countries_group = array('ManageCountries'=>'User may add/change country entries',
	'DeleteCountries'=>'User may delete country entries');

$misc_group = array('ViewLogs'=>'User may view audit logs',
	'MailNotify'=>'User will be notified on several events');

$localizer_group = array('ManageLocalizer'=>'User may manage localizer');

$editor_group_1 = array('EditorBold'=>'User may use bold',
	'EditorItalic'=>'User may use italic',
	'EditorUnderline'=>'User may use underline',
	'EditorStrikethrough'=>'User may use strikethrough',
	'EditorTextAlignment'=>'User may change text alignment',
	'EditorCopyCutPaste'=>'User may copy, cut, and paste',
	'EditorUndoRedo'=>'User may undo/redo',
	'EditorTextDirection'=>'User may change text direction',
	'EditorIndent'=>'User may set indents',
	'EditorLink'=>'User may add links',
	'EditorSubhead'=>'User may add subheads',
	'EditorImage'=>'User may insert images',
	'EditorSourceView'=>'User may view the HTML source',
	'EditorEnlarge'=>'User may enlarge the editor');

$editor_group_2 = array('EditorFontFace'=>'User may change the font face',
	'EditorFontSize'=>'User may change the font size',
	'EditorListBullet'=>'User may create bulleted lists',
	'EditorListNumber'=>'User may create numbered lists');

$editor_group_3 = array('EditorTable'=>'User may insert tables');

$editor_group_4 = array('EditorHorizontalRule'=>'User may insert horizontal rules',
	'EditorFontColor'=>'User may change the font color',
	'EditorSuperscript'=>'User may use superscripts',
	'EditorSubscript'=>'User may use subscripts');

$rights = array('Content'=>$content_group, 'Templates'=>$templates_group,
	'Staff/Subscribers Management'=>$users_group, 'Administrative tasks'=>$administrative_group,
	'Article Types'=>$article_types_group, 'Languages'=>$languages_group,
	'Countries'=>$countries_group, 'Miscellaneous'=>$misc_group, 'Localizer'=>$localizer_group,
	'Editor Basic Settings'=>$editor_group_1, 'Editor Advanced Font Settings'=>$editor_group_2,
	'Editor Table Settings'=>$editor_group_3, 'Editor Miscellaneous Settings'=>$editor_group_4);

?>
<table border="0" cellspacing="0" cellpadding="3" align="left">
<?php
foreach ($rights as $group_name=>$group) {
?>
	<tr>
		<td colspan="2" align="left" style="padding-top: 10px; padding-left: 10px;">
			--- <?php putGS($group_name); ?> ---
		</td>
	</tr>
<?php
	foreach ($group as $right_name=>$right_text) {
?>
	<tr>
		<td align="right" style="padding-left: 10px;">
			<input type="checkbox" name="<?php echo $right_name; ?>" class="input_checkbox" <?php  if ($editUser->hasPermission($right_name)) { p("CHECKED"); } ?>>
		</td>
		<td style="padding-right: 10px;">
			<?php putGS($right_text); ?>
		</td>
	</tr>
<?php
	}
}
?>
	<tr>
		<td>
<?php
	if (function_exists ("incModFile"))
		incModFile ($User);
?>
		</td>
	</tr>
</table>
