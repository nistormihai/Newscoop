<?php 
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

// Optional input parameters
$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_article_code = Input::Get('f_article_code', 'string', 0);
$BackLink = Input::Get('Back', 'string', "/$ADMIN/articles/", true);

list($articleNumber, $languageId) = split("_", $f_article_code);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;	
}

$articleObj =& new Article($languageId, $articleNumber);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('Article does not exist.'), $BackLink);
	exit;
}

$f_publication_id = ($f_publication_id > 0) ? $f_publication_id : $articleObj->getPublicationId();
$f_issue_number = ($f_issue_number > 0) ? $f_issue_number : $articleObj->getIssueNumber();
$f_section_number = ($f_section_number > 0) ? $f_section_number : $articleObj->getSectionNumber();

if ($f_publication_id > 0) {
	$publicationObj =& new Publication($f_publication_id);
	if (!$publicationObj->exists()) {
		camp_html_display_error(getGS('Publication does not exist.'), $BackLink);
		exit;	
	}
	
	$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
	if (!$issueObj->exists()) {
		camp_html_display_error(getGS('No such issue.'), $BackLink);
		exit;	
	}
	
	$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
	if (!$sectionObj->exists()) {
		camp_html_display_error(getGS('No such section.'), $BackLink);
		exit;		
	}
}

if (!$User->hasPermission("TranslateArticle")) {
	$errorStr = getGS('You do not have the right to translate articles.');
	camp_html_display_error($errorStr, $BackLink);
	exit;	
}

// When the user selects a language the form is submitted to the same page (translation.php).
// Read article translation form input values for the case when the page has been reloaded
// because of language select.
$f_translation_title = Input::Get('f_translation_title', 'string', '', true);
$f_language_selected = Input::Get('f_translation_language', 'int', 0, true);
$f_translation_language = Input::Get('f_translation_language', 'int', 0, true);

if ($f_publication_id > 0) {
	$f_translation_issue_name = Input::Get('f_issue_name', 'string', $issueObj->getName(), true);
	$f_translation_issue_urlname = Input::Get('f_issue_urlname', 'string', $issueObj->getUrlName(), true);
	$f_translation_section_name = Input::Get('f_section_name', 'string', $sectionObj->getName(), true);
	$f_translation_section_urlname = Input::Get('f_section_urlname', 'string', $sectionObj->getUrlName(), true);
}

$allLanguages = Language::GetLanguages();
$articleLanguages = $articleObj->getLanguages();
$articleLanguages = DbObjectArray::GetColumn($articleLanguages, "Id");

if ($f_publication_id > 0) {
	$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
					  'Section' => $sectionObj, 'Article'=>$articleObj);
	camp_html_content_top(getGS('Translate article'), $topArray, true, true);
} else {
	$crumbs = array();
	$crumbs[] = array(getGS("Actions"), "");
	$crumbs[] = array(getGS('Translate article'), "");
	echo camp_html_breadcrumbs($crumbs);		
}
?>
<table cellpadding="1" cellspacing="0" class="action_buttons" style="padding-top: 10px;">
<tr>
	<td><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/left_arrow.png" BORDER="0"></td>
	<td><a href="<?php echo camp_html_article_url($articleObj, $f_language_id, "edit.php"); ?>"><b><?php putGS("Back to Edit Article"); ?></b></a></td>
</table>

<P>
<FORM NAME="dialog" METHOD="POST" ACTION="do_translate.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<INPUT TYPE="HIDDEN" NAME="f_article_code" VALUE="<?php  p($f_article_code); ?>">
<?php if ($f_publication_id > 0) { ?>
<INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
<input type='hidden' name='f_publication_id' value="<?php p($f_publication_id); ?>">
<input type='hidden' name='f_issue_number' value="<?php p($f_issue_number); ?>">
<input type='hidden' name='f_section_number' value="<?php p($f_section_number); ?>">
<?php } ?>

<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" class="table_input" width="600px">
<TR>
	<TD COLSPAN="2">
		<B><?php  putGS("Translate article"); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" valign="top"><?php  putGS("Article name ($1)", $articleObj->getLanguageName()); ?>:</TD>
	<TD><?PHP p(htmlspecialchars($articleObj->getTitle())); ?></td>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New article name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_translation_title" SIZE="32" MAXLENGTH="64" value="<?php echo htmlspecialchars($f_translation_title); ?>" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Language"); ?>:</TD>
	<TD>
	<SELECT NAME="f_translation_language" class="input_select" alt="select" <?php if ($f_publication_id > 0) { ?>ONCHANGE="this.form.action = 'translate.php'; this.form.submit();"<?php } ?> emsg="<?php putGS("You must choose a language"); ?>">
	<option></option>
	<?php 
	// Show all the languages that have not yet been translated.
	$displayLanguages = array();
	foreach ($allLanguages as $language) {
		if (!in_array($language->getLanguageId(), $articleLanguages)) {
			$displayLanguages[$language->getLanguageId()] = $language->getNativeName(); 
		}
	}
	asort($displayLanguages);
	foreach ($displayLanguages as $tmpLangId => $nativeName) {
		camp_html_select_option($tmpLangId, $f_language_selected, $nativeName);
	}
	?>
	</SELECT>
	</TD>
</TR>
<?php
	$canCreate = true;
	if ( ($f_language_selected > 0) && ($f_issue_number > 0) ) {
		$translationIssueObj =& new Issue($f_publication_id, $f_language_selected, $f_issue_number);
		if (!$translationIssueObj->exists()) {
			if ($User->hasPermission("ManageIssue")) {
?>
<TR>
	<TD colspan="2" align="left" style="padding-left: 40px; padding-right: 40px; padding-top: 20px;"><strong><?php putGS("An issue must be created for the selected language.  Please enter the issue name and URL name."); ?></strong></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New issue name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_issue_name" SIZE="32" maxlength="140" value="<?php echo htmlspecialchars($f_translation_issue_name) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Issue Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New issue URL name"); ?>:</TD>
	<TD valign="bottom">
	<INPUT TYPE="TEXT" NAME="f_issue_urlname" SIZE="20" maxlength="32" value="<?php echo htmlspecialchars($f_translation_issue_urlname) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Issue URL Name')); ?>">
	</TD>
</TR>
<?php
			} else {
				$canCreate = false;
?>
<TR>
	<TD colspan="2" align="left" class="error_message" style="padding-left: 40px; padding-right: 40px; padding-top: 20px;"><?php putGS('An issue must be created for the selected language but you do not have the right to create an issue.'); ?></TD>
</TR>
<?php
			}
		}
		$translationSectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_selected, $f_section_number);
		if (!$translationSectionObj->exists()) {
			if ($User->hasPermission("ManageSection")) {
?>
<TR>
	<TD colspan="2" align="left" style="padding-left: 40px; padding-right: 40px; padding-top: 20px;"><strong><?php putGS("A section must be created for the selected language.  Please enter the section name and URL name."); ?></strong></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New section name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_section_name" SIZE="32" maxlength="255" value="<?php echo htmlspecialchars($f_translation_section_name) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Section Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php putGS("New section URL name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" NAME="f_section_urlname" SIZE="20" maxlength="32" value="<?php echo htmlspecialchars($f_translation_section_urlname) ?>" class="input_text" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Section URL Name')); ?>">
	</TD>
</TR>
<?php
			} else {
				$canCreate = false;
?>
<TR>
	<TD colspan="2" align="left" class="error_message" style="padding-left: 40px; padding-right: 40px; padding-top: 20px;"><?php putGS('A section must be created for the selected language but you do not have the right to create a section.'); ?></TD>
</TR>
<?php
			}
		}
?>
<?php
	}
	if ($canCreate) {
?>
<TR>
	<TD COLSPAN="2" align="center">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	</TD>
</TR>
<?php
	}
?>
</TABLE>
</FORM>
<P>

<?php camp_html_copyright_notice(); ?>