B_HTML
INCLUDE_PHP_LIB(<*..*>)dnl
B_DATABASE

CHECK_BASIC_ACCESS
<? if ($What != 0) { ?>dnl
CHECK_ACCESS(<*ManageTempl*>)dnl
<? } ?>dnl

<?	
	todef('query');
	todef('Path');
	todef('Name');
	todef('cField');
 ?>

B_HEAD
	X_EXPIRES
	X_TITLE(<*Templates management*>)

<?
    if ($access == 0) {
	if ($What) { ?>dnl
	X_AD(<*You do not have the right to change default templates.*>)
<? } else { ?>dnl
	X_LOGOUT
<? }
    }
?>dnl
E_HEAD

<? if ($access) {

SET_ACCESS(<*mta*>, <*ManageTempl*>)
SET_ACCESS(<*dta*>, <*DeleteTempl*>)
?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Edit template*>)
B_HEADER_BUTTONS

X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

B_CURRENT
X_CURRENT(<*Path:*>, <*<B><? pencHTML(decURL($Path)); ?></B>*>)
X_CURRENT(<*Template:*>, <*<B><? pencHTML(decURL($Name)); ?></B>*>)
E_CURRENT

<P>

B_MSGBOX(<*Edit template*>)
<?
	$filename = "$DOCUMENT_ROOT".decURL($Path)."$Name";
	$fd = fopen ($filename, "w");
	$res=fwrite ($fd, decS($cField));
	if($res >  0){ ?>dnl
		X_MSGBOX_TEXT(<* <LI><?putGS('The template has been saved.'); ?></LI> *>)
	<? }
	else { ?>dnl
		X_MSGBOX_TEXT(<* <LI><? putGS('The template could not be saved'); ?></LI> *>)
	<? }
	fclose ($fd);
?>dnl
	
	B_MSGBOX_BUTTONS
	<? if ($res > 0) { ?>dnl
		<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/done.gif" BORDER="0" ALT="Done"></A>
	<? } else { ?>dnl
		<A HREF="<? pencHTML(decS($Path)); ?>"><IMG SRC="X_ROOT/img/button/ok.gif" BORDER="0" ALT="OK"></A>
	<? } ?>dnl
	E_MSGBOX_BUTTONS
	
E_MSGBOX

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML


