<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/topics/topic_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Topic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_topic_id = Input::Get('f_topic_id', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;	
}

$articleObj =& new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'));
	exit;		
}

if (!$User->hasPermission('AttachTopicToArticle')) {
	camp_html_display_error(getGS("You do not have the right to detach topics from articles."));
	exit;	
}
ArticleTopic::RemoveTopicFromArticle($f_topic_id, $f_article_number);

$url = camp_html_article_url($articleObj, $f_language_id, "edit.php");
header("Location: $url");
exit;

?>