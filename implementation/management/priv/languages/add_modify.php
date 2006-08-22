<?php
camp_load_translation_strings("languages");
require_once($Campsite['HTML_DIR'] . "/$ADMIN_DIR/languages.php");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/localizer/Localizer.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');

$f_language_id = Input::Get('f_language_id', 'int', 0, true);
$editMode = ($f_language_id != 0);

if (!$g_user->hasPermission('ManageLanguages')) {
    if (!$editMode) {
	   camp_html_display_error(getGS("You do not have the right to add languages."));
    }
    else {
       camp_html_display_error(getGS("You do not have the right to edit languages."));
    }
	exit;
}

$q_defaultTimeUnits = $g_ado_db->GetAll("SELECT * FROM TimeUnits WHERE IdLanguage=1");
$numTimeUnits = 0;
$q_timeUnits = array();
if ($editMode) {
    $q_timeUnits = $g_ado_db->GetAll("SELECT * FROM TimeUnits WHERE IdLanguage=$f_language_id");
}

$languageObj =& new Language($f_language_id);

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Languages"), "/$ADMIN/languages");
if ($editMode) {
    $crumbs[] = array(getGS("Edit language"), "");
} else {
    $crumbs[] = array(getGS("Add new language"), "");
}
$breadcrumbs = camp_html_breadcrumbs($crumbs);
echo $breadcrumbs;

include_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs();
?>
<P>
<FORM NAME="language_form" METHOD="POST" ACTION="do_add_modify.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
<?php if ($editMode) { ?>
<input type="hidden" name="f_language_id" value="<?php p($languageObj->getLanguageId()); ?>">
<?php } ?>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">
	   <?php if ($editMode) { ?>
	       <B><?php  putGS("Edit language"); ?></B>
	   <?php } else { ?>
           <B><?php  putGS("Add new language"); ?></B>
       <?php } ?>
		<HR NOSHADE SIZE="1" COLOR="BLACK">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_language_name" SIZE="32" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Name')); ?>" value="<?php p($languageObj->getProperty('Name')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Native name"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_native_name" SIZE="32" alt="blank" emsg="<?php putGS('You must complete the $1 field.', getGS('Native name')); ?>" value="<?php p($languageObj->getProperty('OrigName')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Code"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_language_code" SIZE="2" MAXLENGTH="2" alt="length|2|2" emsg="<?php  putGS('You must complete the $1 field.', getGS('Code')); ?>" value="<?php p($languageObj->getProperty('Code')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  putGS('Please enter the translation for month names.'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("January"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_1" SIZE="20" value="<?php p($languageObj->getProperty('Month1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("February"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_2" SIZE="20" value="<?php p($languageObj->getProperty('Month2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("March"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_3" SIZE="20" value="<?php p($languageObj->getProperty('Month3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("April"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_4" SIZE="20" value="<?php p($languageObj->getProperty('Month4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("May"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_5" SIZE="20" value="<?php p($languageObj->getProperty('Month5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("June"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_6" SIZE="20" value="<?php p($languageObj->getProperty('Month6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("July"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_7" SIZE="20" value="<?php p($languageObj->getProperty('Month7')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("August"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_8" SIZE="20" value="<?php p($languageObj->getProperty('Month8')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("September"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_9" SIZE="20" value="<?php p($languageObj->getProperty('Month9')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("October"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_10" SIZE="20" value="<?php p($languageObj->getProperty('Month10')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("November"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_11" SIZE="20" value="<?php p($languageObj->getProperty('Month11')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("December"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_month_12" SIZE="20" value="<?php p($languageObj->getProperty('Month12')); ?>">
	</TD>
</TR>
<TR>
	<TD COLSPAN="2"><?php  putGS('Please enter the translation for week day names.'); ?></TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Sunday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_sunday" SIZE="20" value="<?php p($languageObj->getProperty('WDay1')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Monday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_monday" SIZE="20" value="<?php p($languageObj->getProperty('WDay2')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Tuesday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_tuesday" SIZE="20" value="<?php p($languageObj->getProperty('WDay3')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Wednesday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_wednesday" SIZE="20" value="<?php p($languageObj->getProperty('WDay4')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Thursday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_thursday" SIZE="20" value="<?php p($languageObj->getProperty('WDay5')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Friday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_friday" SIZE="20" value="<?php p($languageObj->getProperty('WDay6')); ?>">
	</TD>
</TR>
<TR>
	<TD ALIGN="RIGHT" ><?php  putGS("Saturday"); ?>:</TD>
	<TD>
	<INPUT TYPE="TEXT" class="input_text" NAME="f_saturday" SIZE="20" value="<?php p($languageObj->getProperty('WDay7')); ?>">
	</TD>
</TR>

<TR>
	<TD COLSPAN="2"><?php  putGS('Please enter the translation for time units.'); ?></TD>
</TR>
	<?php
	for ($i = 0; $i < count($q_defaultTimeUnits); $i++) {
	    if (count($q_timeUnits) > 0) {
            $value = $q_timeUnits[$i]['Name'];
	    }
	    else {
	        $value = $q_defaultTimeUnits[$i]['Name'];
	    }
	   ?>
	   <TR>
		  <TD ALIGN="RIGHT"><?php p(htmlspecialchars($q_defaultTimeUnits[$i]['Name']));?></TD>
		  <TD><INPUT TYPE="TEXT" class="input_text" NAME="<?php p(htmlspecialchars($q_defaultTimeUnits[$i]['Unit']));?>" SIZE="20" VALUE="<?php  p(htmlspecialchars($value)); ?>"></TD>
	   </TR>
	   <?php
	} ?>
	<TR>

	<TD COLSPAN="2" align="center">
		<INPUT TYPE="submit" class="button" NAME="Save" VALUE="<?php  putGS('Save'); ?>">
	</TD>
</TR>
</TABLE>
</FORM>
<P>
<script>
document.forms.language_form.f_language_name.focus();
</script>
<?php camp_html_copyright_notice(); ?>
