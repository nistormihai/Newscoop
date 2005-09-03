INCLUDE_PHP_LIB(<*$ADMIN_DIR/users/subscriptions/sections*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageSubscriptions*>)

B_HEAD
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
	<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>
	X_TITLE(<*Change subscription*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change subscriptions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?php 
    todefnum('Subs');
    todefnum('Sect');
    todefnum('Pub');
    todefnum('User');
    query ("SELECT UName FROM Users WHERE Id=$User", 'q_usr');
    if ($NUM_ROWS) {
		fetchRow($q_usr);
	    $UName = getHVar($q_usr,'UName');
?>dnl
B_HEADER(<*Change subscription*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Sections*>, <*users/subscriptions/sections/?User=<?php  p($User); ?>&Pub=<?php  p($Pub); ?>&Subs=<?php  p($Subs); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscriptions*>, <*users/subscriptions/?User=<?php  p($User); ?>*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*User account*>, <*users/edit.php?User=<?php echo $User; ?>&uType=Subscribers*>, <**>, <*'$UName'*>)
<td class="breadcrumb_separator">&nbsp;</td>
X_HBUTTON(<*Subscribers*>, <*users/?uType=Subscribers*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
	query ("SELECT Name FROM Publications WHERE Id=$Pub", 'q_pub');
	if ($NUM_ROWS) {
	    query ("SELECT * FROM Subscriptions WHERE Id = $Subs", 'q_sub');
	    if ($NUM_ROWS) {
		$sectCond = "";
		if ($Sect > 0)
		    $sectCond = "SectionNumber = ".$Sect." AND";
		query ("SELECT DISTINCT Sub.*, Sec.Name FROM SubsSections as Sub, Sections as Sec WHERE $sectCond IdSubscription=$Subs AND Sub.SectionNumber = Sec.Number", 'q_ssub');
		if ($NUM_ROWS) {
		    fetchRow($q_pub);
		    fetchRow($q_sub);
		    fetchRow($q_ssub);
		    $isPaid = 0;
		    if (getHVar($q_sub, 'Type') == 'P')
			$isPaid = 1;
?>dnl

B_CURRENT
X_CURRENT(<*Publication*>, <*<?php  pgetHVar($q_pub,'Name'); ?>*>)
E_CURRENT

<P>
B_DIALOG(<*Change subscription*>, <*POST*>, <*do_change.php*>, <**>, <*onsubmit="return validateForm(this, 0, 1, 0, 1, 8);"*>)

	B_DIALOG_INPUT(<*Section*>)
		<?php  if ($Sect > 0) pgetHVar($q_ssub,'Name'); else putGS("-- ALL SECTIONS --"); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Start*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cStartDate" SIZE="10" VALUE="<?php  pgetHVar($q_ssub,'StartDate'); ?>" MAXLENGTH="10" alt="date|yyyy/mm/dd|-" emsg="<?php putGS("You must input a valid date."); ?>"> <?php  putGS('(YYYY-MM-DD)'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Days*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cDays" SIZE="5" VALUE="<?php  pgetHVar($q_ssub,'Days'); ?>"  MAXLENGTH="5" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "Days"); ?>">
	E_DIALOG_INPUT
<?php  if ($isPaid) { ?>
	B_DIALOG_INPUT(<*Paid Days*>)
		<INPUT TYPE="TEXT" class="input_text" NAME="cPaidDays" SIZE="5" VALUE="<?php  pgetHVar($q_ssub,'PaidDays'); ?>"  MAXLENGTH="5" alt="number|0|1|1000000000" emsg="<?php putGS("You must input a number greater than 0 into the $1 field.", "Paid Days"); ?>">
	E_DIALOG_INPUT
<?php  } ?>
	B_DIALOG_BUTTONS
		<INPUT TYPE="HIDDEN" NAME="User" VALUE="<?php  p($User); ?>">
		<INPUT TYPE="HIDDEN" NAME="Subs" VALUE="<?php  p($Subs); ?>">
		<INPUT TYPE="HIDDEN" NAME="Sect" VALUE="<?php  p($Sect); ?>">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		SUBMIT(<*Save*>, <*Save changes*>)
		REDIRECT(<*Cancel*>, <*Cancel*>, <*X_ROOT/users/subscriptions/sections/?Pub=<?php  p($Pub); ?>&User=<?php  p($User); ?>&Subs=<?php  p($Subs); ?>*>)
	E_DIALOG_BUTTONS
E_DIALOG
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No sections in the current subscription.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such subscription.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('Publication does not exist.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
