<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_images");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_section_number = Input::Get('f_section_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_image_id = Input::Get('f_image_id', 'int', 0);
$f_image_template_id = Input::Get('f_image_template_id', 'int', 0, true);

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI'], true);
	exit;
}

$publicationObj =& new Publication($f_publication_id);
$issueObj =& new Issue($f_publication_id, $f_language_id, $f_issue_number);
$sectionObj =& new Section($f_publication_id, $f_issue_number, $f_language_id, $f_section_number);
$articleObj =& new Article($f_language_selected, $f_article_number);
$imageObj =& new Image($f_image_id);

if (!$User->hasPermission('ChangeImage')) {
	$title = getGS('Image information');
} else {
	$title = getGS('Change image information');
}

// Add extra breadcrumb for image list.
$extraCrumbs = array(getGS("Images") => "");
$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj,
				  'Section' => $sectionObj, 'Article'=>$articleObj);
camp_html_content_top($title, $topArray, true, true, $extraCrumbs);
?>
<P>
<div class="indent">
<IMG SRC="<?php echo $imageObj->getImageUrl(); ?>" BORDER="0" ALT="<?php echo htmlspecialchars($imageObj->getDescription()); ?>">
</div>
<p>
<FORM NAME="dialog" METHOD="POST" ACTION="do_edit.php" >
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" ALIGN="CENTER" class="table_input">
<TR>
	<TD COLSPAN="2">
		<B><?php  p($title); ?></B>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Number'); ?>:</TD>
	<TD>
		<?php if ($User->hasPermission('AttachImageToArticle')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_template_id" VALUE="<?php echo $f_image_template_id; ?>" class="input_text" SIZE="32" MAXLENGTH="10">
		<?php } else {
			echo $f_image_template_id;
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Description'); ?>:</TD>
	<TD>
		<?php if ($User->hasPermission('ChangeImage')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_description" VALUE="<?php echo htmlspecialchars($imageObj->getDescription()); ?>" class="input_text" SIZE="32" MAXLENGTH="128">
		<?php } else {
			echo htmlspecialchars($imageObj->getDescription());
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Photographer'); ?>:</TD>
	<TD>
		<?php if ($User->hasPermission('ChangeImage')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_photographer" VALUE="<?php echo htmlspecialchars($imageObj->getPhotographer());?>" class="input_text" SIZE="32" MAXLENGTH="64">
		<?php } else {
			echo htmlspecialchars($imageObj->getPhotographer());
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Place'); ?>:</TD>
	<TD>
		<?php if ($User->hasPermission('ChangeImage')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_place" VALUE="<?php echo htmlspecialchars($imageObj->getPlace()); ?>" class="input_text" SIZE="32" MAXLENGTH="64">
		<?php } else {
			echo htmlspecialchars($imageObj->getPlace());
		} ?>
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS('Date'); ?>:</TD>
	<TD>
		<?php if ($User->hasPermission('ChangeImage')) { ?>
		<INPUT TYPE="TEXT" NAME="f_image_date" VALUE="<?php echo htmlspecialchars($imageObj->getDate()); ?>" class="input_text" SIZE="11" MAXLENGTH="10">
		<?php } else {
			echo htmlspecialchars($imageObj->getDate());
		} ?>
		<?php putGS('YYYY-MM-DD'); ?>
	</TD>
</TR>
<?php if ($User->hasPermission('ChangeImage') || $User->hasPermission('AttachImageToArticle')) { ?>
<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <INPUT TYPE="HIDDEN" NAME="f_publication_id" VALUE="<?php  p($f_publication_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_issue_number" VALUE="<?php  p($f_issue_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_section_number" VALUE="<?php  p($f_section_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_article_number" VALUE="<?php  p($f_article_number); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_id" VALUE="<?php  p($f_language_id); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_language_selected" VALUE="<?php  p($f_language_selected); ?>">
    <INPUT TYPE="HIDDEN" NAME="f_image_id" VALUE="<?php  p($f_image_id); ?>">
	<INPUT TYPE="submit" NAME="Save" VALUE="<?php  putGS('Save'); ?>" class="button">
	</DIV>
	</TD>
</TR>
<?php } ?>
</TABLE>
</FORM>
<P>
<?php

camp_html_copyright_notice(); ?>