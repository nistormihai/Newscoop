<?php
camp_load_translation_strings("imagearchive");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Image.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ImageSearch.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

// Check input
$f_image_description = Input::Get('f_image_description');
$f_image_photographer = Input::Get('f_image_photographer');
$f_image_place = Input::Get('f_image_place');
$f_image_date = Input::Get('f_image_date');
$f_image_url = Input::Get('f_image_url', 'string', '', true);

if (!Input::IsValid()) {
	camp_html_goto_page("/$ADMIN/imagearchive/index.php");
}
$uploadFileSpecified = isset($_FILES['f_image_file'])
  					   && isset($_FILES['f_image_file']['name'])
  					   && !empty($_FILES['f_image_file']['name']);
if (empty($f_image_url) && !$uploadFileSpecified) {
	camp_html_add_msg(getGS("You must select an image file to upload."));
	camp_html_goto_page("/$ADMIN/imagearchive/add.php");
}
if (!$g_user->hasPermission('AddImage')) {
	camp_html_goto_page("/$ADMIN/logout.php");
}
$attributes = array();
$attributes['Description'] = $f_image_description;
$attributes['Photographer'] = $f_image_photographer;
$attributes['Place'] = $f_image_place;
$attributes['Date'] = $f_image_date;
if (!empty($f_image_url)) {
	if (camp_is_valid_url($f_image_url)) {
		$image = Image::OnAddRemoteImage($f_image_url, $attributes, $g_user->getUserId());
	} else {
		camp_html_add_msg(getGS("The URL you entered is invalid: '$1'", htmlspecialchars($f_image_url)));
		camp_html_goto_page("/$ADMIN/imagearchive/add.php");
	}
} elseif (!empty($_FILES['f_image_file'])) {
	$image = Image::OnImageUpload($_FILES['f_image_file'], $attributes, $g_user->getUserId());
} else {
	camp_html_add_msg(getGS("You must select an image file to upload."));
	camp_html_goto_page("/$ADMIN/imagearchive/add.php");
}

// Check if image was added successfully
if (PEAR::isError($image)) {
	camp_html_add_msg($image->getMessage());
	camp_html_goto_page("/$ADMIN/imagearchive/add.php");
}

camp_html_add_msg(getGS("Image added."), "ok");
camp_html_goto_page("/$ADMIN/imagearchive/edit.php?f_image_id=".$image->getImageId());
?>