<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/classes/UserType.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Country.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!isset($editUser) || gettype($editUser) != 'object') {
	camp_html_display_error(getGS('No such user account.'));
	exit;
}
$isNewUser = $editUser->getUserName() == '';
compute_user_rights($User, $canManage, $canDelete);
if (!$canManage && $editUser->getUserId() != $User->getUserId()) {
	if ($isNewUser) {
		$error = getGS("You do not have the right to create user accounts.");
	} else {
		$error = getGS('You do not have the right to change user account information.');
	}
	camp_html_display_error($error);
	exit;
}

$fields = array('UName', 'Name', 'Title', 'Gender', 'Age', 'EMail', 'City', 'StrAddress',
	'State', 'CountryCode', 'Phone', 'Fax', 'Contact', 'Phone2', 'PostalCode', 'Employer',
	'EmployerType', 'Position');
if ($isNewUser) {
	$action = 'do_add.php';
	foreach ($fields as $index=>$field) {
		$$field = Input::Get($field, 'string', '');
	}
} else {
	$action = 'do_edit.php';
	foreach ($fields as $index=>$field) {
		$$field = $editUser->getProperty($field);
	}
}
$userTypes = UserType::GetUserTypes();
$countries = Country::GetCountries(1);
$my_user_type = UserType::GetUserTypeFromConfig($editUser->getConfig());

?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>

<form name="dialog" method="POST" action="<?php echo $action; ?>" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<input type="hidden" name="uType" value="<?php echo $uType; ?>">
<?php
if (!$isNewUser) { 
?>
<input type="hidden" name="User" value="<?php echo $editUser->getUserId(); ?>">
<?php
}
?>
<table border="0" cellspacing="0" align="left" class="table_input" width="600px">
<tr>
	<td align="left">
		<table border="0" cellspacing="0" cellpadding="3" align="left">
			<tr>
				<td align="right" nowrap><?php putGS("Account name"); ?>:</td>
<?php
if (!$isNewUser) {
?>
				<td align="left" nowrap><b><?php p(htmlspecialchars($editUser->getUserName())); ?></b></td>
<?php
} else {
?>
				<td><input type="text" class="input_text" name="UName" size="32" maxlength="32" value="<?php p(htmlspecialchars($UName)); ?>" alt="blank" emsg="<?php putGS("You must complete the $1 field.", "Account name"); ?>"></td>
			</tr>
			<tr>
				<td align="right"><?php putGS("Password"); ?>:</td>
				<td>
				<input type="password" class="input_text" name="password" size="16" maxlength="32" alt="length|6" emsg="<?php putGS("The password must be at least 6 characters long and both passwords should match."); ?>">
				</td>
			</tr>
			<tr>
				<td align="right"><?php putGS("Confirm password"); ?>:</td>
				<td>
				<input type="password" class="input_text" name="passwordConf" size="16" maxlength="32" alt="length|6" emsg="<?php putGS("The confirm password must be at least 6 characters long and both passwords should match."); ?>">
				</td>
<?php
}
?>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Full Name"); ?>:</td>
				<td><input type="text" class="input_text" name="Name" VALUE="<?php p(htmlspecialchars($Name)); ?>" size="32" maxlength="128" alt="blank" emsg="<?php putGS("You must complete the $1 field.", "Full Name");?>">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("E-Mail"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="EMail" value="<?php p(htmlspecialchars($EMail)); ?>" size="32" maxlength="128" alt="email" emsg="<?php putGS("You must input a valid EMail address.");?>">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Phone"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Phone" value="<?php p(htmlspecialchars($Phone)); ?>" size="20" maxlength="20">
				</td>
			</tr>

			<?php
			if ($isNewUser && ($uType == "Staff")) {
			?>
			<tr>
				<td align="right"><?php putGS("Type"); ?>:</td>
				<td>
				<select name="Type" class="input_select" alt="select" emsg="<?php putGS("You must select a $1", "Type"); ?>">
				<option value=""><?php putGS("Make a selection"); ?></option>
				<?php
				$Type = Input::Get('Type', 'string', '');
				foreach ($userTypes as $tmpUserType) {
					camp_html_select_option($tmpUserType->getName(), $Type, $tmpUserType->getName());
				}
				?>
				</select>
				</td>
			</tr>
<?php
} else {
	echo "<input type=\"hidden\" name=\"Type\" value=\"$uType\">\n";
}
?>
		</table>
	</td>
</tr>
<?php
if (!$isNewUser) {
?>
<input type="hidden" name="setPassword" id="set_password" value="false">
<tr id="password_show_link">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('password_dialog'); ToggleRowVisibility('password_hide_link'); ToggleRowVisibility('password_show_link'); ToggleBoolValue('set_password');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmag+.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to change password"); ?>
		</a>
	</td>
</tr>
<tr id="password_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('password_dialog'); ToggleRowVisibility('password_hide_link'); ToggleRowVisibility('password_show_link'); ToggleBoolValue('set_password');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmag-.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to leave password unchanged"); ?>
		</a>
	</td>
</tr>
<tr id="password_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
		<?php
		if ($userId == $User->getUserId() && !$isNewUser) {
		?>
		<tr>
			<td align="right" nowrap width="1%"><?php putGS("Old Password"); ?>:</td>
			<td>
			<input type="password" class="input_text" name="oldPassword" size="16" maxlength="32">
			</td>
		</tr>
		<?php
		}
		?>

		<tr>
			<td align="right" nowrap width="1%"><?php putGS("Password"); ?>:</td>
			<td>
			<input type="password" class="input_text" name="password" size="16" maxlength="32">
			</td>
		</tr>
		
		<tr>
			<td align="right" nowrap width="1%"><?php putGS("Confirm password"); ?>:</td>
			<td>
			<input type="password" class="input_text" name="passwordConf" size="16" maxlength="32">
			</td>
		</tr>
		</table>
	</td>
</tr>
<?php
} // if ($isNewUser)
?>
<tr id="user_details_show_link">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('user_details_dialog'); ToggleRowVisibility('user_details_hide_link'); ToggleRowVisibility('user_details_show_link');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmag+.png" id="my_icon" border="0" align="center">
			<?php putGS("Show more user details"); ?>
		</a>
	</td>
</tr>
<tr id="user_details_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('user_details_dialog'); ToggleRowVisibility('user_details_hide_link'); ToggleRowVisibility('user_details_show_link');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmag-.png" id="my_icon" border="0" align="center">
			<?php putGS("Hide user details"); ?>
		</a>
	</td>
</tr>
<tr id="user_details_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="3" align="center" width="100%">
			<tr>
				<td align="right" nowrap><?php putGS("Title"); ?>:</td>
				<td>
				<SELECT class="input_select" name="Title">
				<?php
				camp_html_select_option(getGS("Mr."), $Title, getGS("Mr."));
				camp_html_select_option(getGS("Mrs."), $Title, getGS("Mrs."));
				camp_html_select_option(getGS("Ms."), $Title, getGS("Ms."));
				camp_html_select_option(getGS("Dr."), $Title, getGS("Dr."));
				?>
				</SELECT>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Gender"); ?>:</td>
				<td>
				<input type=radio name=Gender value="M"<?php if($Gender == "M") { ?> CHECKED<?php  } ?>><?php  putGS('Male'); ?>
				<input type=radio name=Gender value="F"<?php if($Gender == "F") { ?> CHECKED<?php  } ?>><?php  putGS('Female'); ?>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Age"); ?>:</td>
				<td>
				<SELECT name="Age" class="input_select">
				<?php
				camp_html_select_option("0-17", $Age, getGS("under 18"));
				camp_html_select_option("18-24", $Age, "18-24");
				camp_html_select_option("25-39", $Age, "25-39");
				camp_html_select_option("40-49", $Age, "40-49");
				camp_html_select_option("50-65", $Age, "50-65");
				camp_html_select_option("65-", $Age, getGS("65 or over"));
				?>				
				</SELECT>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("City"); ?>:</td>
				<td>
				<input type="text" class="input_text" NAME="City" VALUE="<?php p(htmlspecialchars($City)); ?>" size="32" maxlength="60">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Street Address"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="StrAddress" value="<?php  p(htmlspecialchars($StrAddress)); ?>" size="32" maxlength="255">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Postal Code"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="PostalCode" value="<?php p(htmlspecialchars($PostalCode)); ?>" size="10" maxlength="10">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("State"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="State" value="<?php p(htmlspecialchars($State)); ?>" size="32" maxlength="32">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Country"); ?>:</td>
				<td>
				<SELECT name="CountryCode" class="input_select">
				<?php
				foreach ($countries as $country) {
					camp_html_select_option($country->getCode(), $CountryCode, $country->getName());
				}
				?>
				</SELECT>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Fax"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Fax" value="<?php p(htmlspecialchars($Fax)); ?>" size="20" maxlength="20">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Contact Person"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Contact" value="<?php  p(htmlspecialchars($Contact)); ?>" size="32" maxlength="64">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Second Phone"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Phone2" value="<?php  p(htmlspecialchars($Phone2)); ?>" size="20" maxlength="20">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Employer"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Employer" value="<?php  p(htmlspecialchars($Employer)); ?>" size="30" maxlength="30">
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Employer Type"); ?>:</td>
				<td>
					<SELECT name="EmployerType" class="input_select">
					<OPTION></OPTION>
					<?php
					camp_html_select_option('Corporate', $EmployerType, getGS('Corporate'));
					camp_html_select_option('NGO', $EmployerType, getGS('Non-Governmental Organisation'));
					camp_html_select_option('Government Agency', $EmployerType, getGS('Government Agency'));
					camp_html_select_option('Academic', $EmployerType, getGS('Academic'));
					camp_html_select_option('Media', $EmployerType, getGS('Media'));
					?>
					</SELECT>
				</td>
			</tr>
			<tr>
				<td align="right" nowrap><?php putGS("Position"); ?>:</td>
				<td>
				<input type="text" class="input_text" name="Position" value="<?php p(htmlspecialchars($Position)); ?>" size="30" maxlength="30">
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php
if ($editUser->isAdmin() /*&& $canManage*/) {
?>
<input type="hidden" name="customizeRights" id="customize_rights" value="false">
<tr id="user_type_dialog">
	<td style="padding-left: 4px; padding-top: 4px; padding-bottom: 4px;">
		<?PHP
		$my_user_type_name = $my_user_type ? $my_user_type->getName() : "";
		?>
		<?php putGS("User Type"); ?>:
		<select name="UserType">
		<option value="">---</option>
		<?php
		foreach ($userTypes as $user_type) {
			camp_html_select_option($user_type->getName(), $my_user_type_name, $user_type->getName());
		}
		?>
		</select>
	</td>
</tr>
<tr id="rights_show_link">
	<td style="padding-left: 6px; padding-top: 6px; padding-right: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('rights_dialog'); ToggleRowVisibility('user_type_dialog'); ToggleRowVisibility('rights_hide_link'); ToggleRowVisibility('rights_show_link'); ToggleBoolValue('customize_rights');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmag+.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to customize user permissions"); ?>
		</a>
	</td>
</tr>
<tr id="rights_hide_link" style="display: none;">
	<td style="padding-left: 6px; padding-top: 6px; padding-right: 6px;">
		<a href="javascript: void(0);" onclick="ToggleRowVisibility('rights_dialog'); ToggleRowVisibility('user_type_dialog'); ToggleRowVisibility('rights_hide_link'); ToggleRowVisibility('rights_show_link'); ToggleBoolValue('customize_rights');">
			<img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/viewmag-.png" id="my_icon" border="0" align="center">
			<?php putGS("Click here to use existing user type permissions (discard customization)"); ?>
		</a>
	</td>
</tr>
<tr id="rights_dialog" style="display: none;">
	<td>
		<table border="0" cellspacing="0" cellpadding="6" align="center" width="100%">
			<tr>
				<td>
<?php require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/users/access_form.php"); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php
} // $editUser->isAdmin()
?>
<tr>
	<td>
		<table border="0" cellspacing="0" cellpadding="6" align="center" width="100%">
		<tr>
			<td colspan="2">
			<div align="center">
			<input type="submit" class="button" name="Save" value="<?php  putGS('Save'); ?>">
<!--				<input type="button" class="button" name="Cancel" value="<?php putGS('Cancel'); ?>" onclick="location.href='<?php echo "/$ADMIN/users/?" . get_user_urlparams(); ?>'">
-->				</div>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</form>
