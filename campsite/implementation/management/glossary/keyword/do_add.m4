B_HTML
INCLUDE_PHP_LIB(<*../..*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageDictionary*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Adding new keyword infotype*>)
<? if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to add keyword infotypes.*>)
<? } ?>dnl
E_HEAD

<? if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

<?
    todefnum('Keyword');
    todefnum('Language');
    todefnum('cClass');
?>dnl
B_HEADER(<*Adding new keyword infotype*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Keyword infotypes*>, <*glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>*>)
X_HBUTTON(<*Glossary*>, <*glossary/*>)
X_HBUTTON(<*Home*>, <*home.php*>)
X_HBUTTON(<*Logout*>, <*logout.php*>)
E_HEADER_BUTTONS
E_HEADER

<?
    query ("SELECT Keyword FROM Dictionary WHERE Id=$Keyword AND IdLanguage=$Language", 'q_dict');
    query ("SELECT Name FROM Languages WHERE Id=$Language", 'q_lang');
    fetchRow($q_dict);
    fetchRow($q_lang);
?>dnl
B_CURRENT
X_CURRENT(<*Keyword*>, <*<B><? pgetHVar($q_dict,'Keyword'); ?></B>*>)
X_CURRENT(<*Language*>, <*<B><? pgetHVar($q_lang,'Name') ;?></B>*>)
E_CURRENT

<?
    $created= 0;
?>dnl
<P>
B_MSGBOX(<*Adding new keyword infotype*>)
	X_MSGBOX_TEXT(<*
<?
    $AFFECTED_ROWS = 0;
    if ($cClass != 0)
	query ("INSERT IGNORE INTO KeywordClasses SET IdDictionary=$Keyword, IdClasses=$cClass, IdLanguage=$Language");
    if ($AFFECTED_ROWS > 0) { ?>dnl
		<LI><? putGS('The keyword infotype has been added.'); ?></LI>
<? } else { ?>dnl
		<LI><? putGS('The keyword infotype could not be added.'); ?><LI></LI><? putGS('Please check if the keyword infotype does not already exist.'); ?></LI>
<? } ?>dnl
		*>)
<? if ($AFFECTED_ROWS > 0) { ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*New*>, <*Add another*>, <*X_ROOT/glossary/keyword/add.php?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>*>)
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/glossary/keyword/?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>*>)
	E_MSGBOX_BUTTONS
<? } else { ?>
	B_MSGBOX_BUTTONS
		REDIRECT(<*OK*>, <*OK*>, <*X_ROOT/glossary/keyword/add.php?Keyword=<? pencURL($Keyword); ?>&Language=<? pencURL($Language); ?>*>)
	E_MSGBOX_BUTTONS
<? } ?>dnl
E_MSGBOX
<P>

X_HR
X_COPYRIGHT
E_BODY
<? } ?>dnl

E_DATABASE
E_HTML
