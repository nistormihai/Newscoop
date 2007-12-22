<?php
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/issues/issue_common.php");

// Check permissions
if (!$g_user->hasPermission('ManageIssue')) {
	camp_html_display_error(getGS('You do not have the right to add issues.'));
	exit;
}

$Pub = Input::Get('Pub', 'int');
if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid Input: $1', Input::GetErrorString()));
	exit;
}
$publicationObj = new Publication($Pub);
$allLanguages = Language::GetLanguages();
$newIssueId = Issue::GetUnusedIssueId($Pub);

camp_html_content_top(getGS('Add new issue'), array('Pub' => $publicationObj), true, false, array(getGS("Issues") => "/$ADMIN/issues/?Pub=$Pub"));

?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="2" ALIGN="CENTER" class="table_input">
<TR>
	<TD VALIGN="TOP"><A HREF="add_prev.php?Pub=<?php p($Pub); ?>"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/tol.gif" BORDER="0"></A></TD>
	<TD><B><A HREF="add_prev.php?Pub=<?php p($Pub); ?>"><?php  putGS('Use the structure of the previous issue'); ?></A></B></TD>
</TR>
<TR>
	<TD></TD>
	<TD VALIGN="TOP">
		<LI><?php  putGS('Copy the entire structure in all languages from the previous issue except for content.'); ?><LI><?php  putGS('You may modify it later if you wish.'); ?></LI>
	</TD>
<TR>
<TR>
	<TD VALIGN="TOP"><A HREF="add_new.php?Pub=<?php  p($Pub); ?>"><IMG SRC="<?php echo $Campsite['ADMIN_IMAGE_BASE_URL']; ?>/tol.gif" BORDER="0"></A></TD>
	<TD><B><A HREF="add_new.php?Pub=<?php  p($Pub); ?>"><?php  putGS('Create a new structure'); ?></A></B></TD>
</TR>
<TR>
	<TD></TD>
	<TD VALIGN="TOP">
		<LI><?php  putGS('Create a complete new structure.'); ?><LI><?php  putGS('You must define an issue type for each language and then sections for them.'); ?></LI>
	</TD>
<TR>
</TABLE>
<P>
<?php camp_html_copyright_notice(); ?>