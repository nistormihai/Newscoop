B_HTML
INCLUDE_PHP_LIB(<*..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Add new user account*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to create user accounts.*>)
<? }
    query ("SELECT Name, Code FROM Countries WHERE 1=0", 'countries');
    query ("SELECT Name FROM UserTypes WHERE 1=0", 'q');
?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Add new user account*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
todef('cName');
todef('cTitle');
todef('cGender');
todef('cAge');
todef('cUName');
todef('cPass1');
todef('cPass2');
todef('cEMail');
todef('cCity');
todef('cStrAddress');
todef('cState');
todef('cCountryCode');
todef('cPhone');
todef('cFax');
todef('cContact');
todef('cPhone2');
todef('cPostalCode');
todef('cEmployer');
todef('cEmployerType');
todef('cPosition');
todef('cType');

?>

define(<*X_SLCTD*>, <*<? if ("$1" == $2) { ?>SELECTED<? } ?>*>)dnl
define(<*X_CHKD*>, <*<? if ("$1" == $2) { ?>CHECKED<? } ?>*>)dnl

<P>
B_DIALOG(<*Add new user account*>, <*POST*>, <*do_add.php*>)
	B_DIALOG_INPUT(<*Full Name*>)
		<INPUT TYPE="TEXT" NAME="cName" SIZE="32" MAXLENGTH="64" VALUE="<? pencHTML($cName); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Title*>)
		<SELECT NAME="cTitle">
		<OPTION VALUE="Mr." X_SLCTD(<*$cTitle*>, <*'Mr.'*>)><? putGS('Mr.'); ?></OPTION>
		<OPTION VALUE="Mrs." X_SLCTD(<*$cTitle*>, <*'Mrs.'*>)><? putGS('Mrs.'); ?></OPTION>
		<OPTION VALUE="Ms." X_SLCTD(<*$cTitle*>, <*'Ms.'*>)><? putGS('Ms.'); ?></OPTION>
		<OPTION VALUE="Dr." X_SLCTD(<*$cTitle*>, <*'Dr.'*>)><? putGS('Dr.'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Gender*>)
		<INPUT TYPE=RADIO NAME=cGender VALUE="M" X_CHKD(<*$cGender*>, <*'M'*>)><? putGS('Male'); ?>
		<INPUT TYPE=RADIO NAME=cGender VALUE="F" X_CHKD(<*$cGender*>, <*'F'*>)><? putGS('Female'); ?>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Age*>)
		<SELECT NAME="cAge">
		<OPTION VALUE="0-17"  X_SLCTD(<*x$cAge*>, <*'x0-17*>')><? putGS('under 18'); ?></OPTION>
		<OPTION VALUE="18-24" X_SLCTD(<*x$cAge*>, <*'x18-24'*>)>18-24</OPTION>
		<OPTION VALUE="25-39" X_SLCTD(<*x$cAge*>, <*'x25-39'*>)>25-39</OPTION>
		<OPTION VALUE="40-49" X_SLCTD(<*x$cAge*>, <*'x40-49'*>)>40-49</OPTION>
		<OPTION VALUE="50-65" X_SLCTD(<*x$cAge*>, <*'x50-65'*>)>50-65</OPTION>
		<OPTION VALUE="65-"   X_SLCTD(<*x$cAge*>, <*'x65-'*>)><? putGS('65 or over'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*User name*>)
		<INPUT TYPE="TEXT" NAME="cUName" SIZE="32" MAXLENGTH="32" VALUE="<? pencHTML($cUName); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Password*>)
		<INPUT TYPE="PASSWORD" NAME="cPass1" SIZE="32" MAXLENGTH="32" VALUE="<? pencHTML($cPass1); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Confirm password*>)
		<INPUT TYPE="PASSWORD" NAME="cPass2" SIZE="32" MAXLENGTH="32" VALUE="<? pencHTML($cPass2); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*E-Mail*>)
		<INPUT TYPE="TEXT" NAME="cEMail" SIZE="32" MAXLENGTH="128" VALUE="<? pencHTML($cEMail); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*City*>)
		<INPUT TYPE="TEXT" NAME="cCity" SIZE="32" MAXLENGTH="60" VALUE="<? pencHTML($cCity); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Street Address*>)
		<INPUT TYPE="TEXT" NAME="cStrAddress" SIZE="50" MAXLENGTH="255" VALUE="<? pencHTML($cStrAddress); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Postal Code*>)
		<INPUT TYPE="TEXT" NAME="cPostalCode" SIZE="10" MAXLENGTH="10" VALUE="<? pencHTML($cPostalCode); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*State*>)
		<INPUT TYPE="TEXT" NAME="cState" SIZE="32" MAXLENGTH="32" VALUE="<? pencHTML($cState); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Country*>)
		<SELECT NAME="cCountryCode">
<? 
    query ("SELECT Name, Code FROM Countries WHERE IdLanguage = 1", 'countries');
    $nr=$NUM_ROWS;
    for($loop=0;$loop<$nr;$loop++) {
	fetchRow($countries);
	pComboVar(getHVar($countries,'Code'),$cCountryCode,getHVar($countries,'Name'));
    } ?>dnl
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Phone*>)
		<INPUT TYPE="TEXT" NAME="cPhone" SIZE="20" MAXLENGTH="20" VALUE="<? pencHTML($cPhone); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Fax*>)
		<INPUT TYPE="TEXT" NAME="cFax" SIZE="20" MAXLENGTH="20" VALUE="<? pencHTML($cFax); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Contact Person*>)
		<INPUT TYPE="TEXT" NAME="cContact" SIZE="32" MAXLENGTH="64" VALUE="<? pencHTML($cContact); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Second Phone*>)
		<INPUT TYPE="TEXT" NAME="cPhone2" SIZE="20" MAXLENGTH="20" VALUE="<? pencHTML($cPhone2); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Employer*>)
		<INPUT TYPE="TEXT" NAME="cEmployer" SIZE="30" MAXLENGTH="30" VALUE="<? pencHTML($cEmployer); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Employer Type*>)
		<SELECT NAME="cEmployerType">
		<OPTION></OPTION>
		<OPTION VALUE="Corporate" X_SLCTD(<*$cEmployerType*>, <*'Corporate'*>)><? putGS('Corporate'); ?></OPTION>
		<OPTION VALUE="NGO" X_SLCTD(<*$cEmployerType*>, <*'NGO'*>)><? putGS('Non-Governmental Organisation'); ?></OPTION>
		<OPTION VALUE="Government Agency" X_SLCTD(<*$cEmployerType*>, <*'Government Agency'*>)><? putGS('Government Agency'); ?></OPTION>
		<OPTION VALUE="Academic" X_SLCTD(<*$cEmployerType*>, <*'Academic'*>)><? putGS('Academic'); ?></OPTION>
		<OPTION VALUE="Media" X_SLCTD(<*$cEmployerType*>, <*'Media'*>)><? putGS('Media'); ?></OPTION>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Position*>)
		<INPUT TYPE="TEXT" NAME="cPosition" SIZE="30" MAXLENGTH="30" VALUE="<? pencHTML($cPosition); ?>">
	E_DIALOG_INPUT
	B_DIALOG_INPUT(<*Type*>)
		<? 
		    query ("SELECT Name FROM UserTypes ORDER BY Name", 'q');
		?>
		<SELECT NAME="cType">
			<?
			    $nr=$NUM_ROWS;
			    for($loop=0;$loop<$nr;$loop++) {
				fetchRow($q); ?>
			    <OPTION X_SLCTD(<*$cType*>, <*getHVar($q,'Name')*>)><? pgetHVar($q,'Name'); ?>
			<? } ?>
		</SELECT>
	E_DIALOG_INPUT
	B_DIALOG_BUTTONS
		<INPUT TYPE="IMAGE" NAME="OK" SRC="X_ROOT/img/button/save.gif" BORDER="0">
<? todef('Back'); ?>dnl
		<INPUT TYPE="HIDDEN" NAME="Back" VALUE="<? pencHTML($Back); ?>">
<? if ($Back != "") { ?>dnl
		<A HREF="<? p($Back); ?>"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } else { ?>dnl
		<A HREF="X_ROOT/users/"><IMG SRC="X_ROOT/img/button/cancel.gif" BORDER="0" ALT="Cancel"></A>
<? } ?>dnl
	E_DIALOG_BUTTONS
E_DIALOG
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


