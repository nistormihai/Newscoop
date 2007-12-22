<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/IssuePublish.php');

// Check permissions
if (!$g_user->hasPermission('Publish')) {
	camp_html_display_error(getGS("You do not have the right to schedule issues or articles for automatic publishing."));
}

// Get input
$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$event_id = Input::Get('event_id', 'int', null, true);
$publish_date = trim(Input::Get('publish_date', 'string', ''));
$action = trim(Input::Get('action', 'string', ''));
$publish_articles = trim(Input::Get('publish_articles', 'string', ''));
$publish_hour = trim(Input::Get('publish_hour', 'string', ''));
$publish_min = trim(Input::Get('publish_min', 'string', ''));

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()));
	exit;
}

$correct = ($publish_date != "") && ($publish_hour != "")
	&& ($publish_min != "") && ($action == "P" || $action == "U");

if ($publish_articles != "Y" && $publish_articles != "N") {
	$publish_articles = "N";
}

$created = 0;
if ($correct) {
	$publish_time = $publish_date . " " . $publish_hour . ":" . $publish_min . ":00";
    $issuePublishObj =& new IssuePublish($event_id);
	if (!$issuePublishObj->exists()) {
	    $issuePublishObj->create();
	    $issuePublishObj->setPublicationId($Pub);
	    $issuePublishObj->setIssueNumber($Issue);
	    $issuePublishObj->setLanguageId($Language);
	}
    $issuePublishObj->setPublishAction($action);
    $issuePublishObj->setPublishArticlesAction($publish_articles);
    $issuePublishObj->setActionTime($publish_time);
	$created = 1;
}
if ($created) {
	camp_html_goto_page("/$ADMIN/issues/edit.php?Pub=$Pub&Issue=$Issue&Language=$Language");
}

$issueObj =& new Issue($Pub, $Language, $Issue);
$publicationObj =& new Publication($Pub);
$crumbs = array("Pub" => $publicationObj, "Issue" => $issueObj);
camp_html_content_top(getGS("Scheduling a new publish action"), $crumbs);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" ALIGN="CENTER" class="table_input">
	<TR>
		<TD COLSPAN="2">
			<B> <?php  putGS("Scheduling a new publish action"); ?> </B>
			<HR NOSHADE SIZE="1" COLOR="BLACK">
		</TD>
	</TR>
	<TR>
		<TD COLSPAN="2"><BLOCKQUOTE>
<?php
	if ( $publish_date == "" ) {
	$correct= 0; ?>	<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Date').'</B>' ); ?></LI>
	<?php }

    if ( ($publish_hour == "") || ($publish_min == "") ) {
	$correct= 0; ?>	<LI><?php putGS('You must complete the $1 field.','<B>'.getGS('Time').'</B>' ); ?></LI>
    <?php }

	if ( ($action != "P") && ($action != "U") ) {
	$correct= 0; ?>	<LI><?php putGS('You must select an action.'); ?></LI>
    <?php }

	if ($correct) {
		if (!$created) { ?>
			<LI><?php putGS('There was an error scheduling the $1 action on $2', getGS($action_str), $publish_time); ?></LI>
	       <?php
		}
    }
?>	</BLOCKQUOTE></TD>
	</TR>
	<TR>
		<TD COLSPAN="2">
		<DIV ALIGN="CENTER">
	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php echo $ADMIN; ?>/issues/edit.php?Pub=<?php p($Pub); ?>&Issue=<?php p($Issue); ?>&Language=<?php p($Language); ?>'">
		</DIV>
		</TD>
	</TR>
</TABLE>
<P>

<?php camp_html_copyright_notice(); ?>
