<?php
camp_load_translation_strings("article_type_fields");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');

// Check permissions
if (!$g_user->hasPermission('DeleteArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to delete article type fields."));
	exit;
}

$articleTypeName = Input::Get('f_article_type');
$fieldName = Input::Get('f_field_name');


$field =& new ArticleTypeField($articleTypeName, $fieldName);
if ($field->exists()) {
	$field->delete();
}
camp_html_goto_page("/$ADMIN/article_types/fields/?f_article_type=".urlencode($articleTypeName));
?>