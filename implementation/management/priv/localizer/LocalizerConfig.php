<?php
/**
 * @package Campware
 */

/**
 * Since the XML_Serializer package is not yet stable,
 * we must use our own package.  The package has a bug fix applied
 * that is required for the Localizer XML files to work.
 */
require_once($_SERVER['DOCUMENT_ROOT'].'/include/XML_Serializer/Serializer.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/include/XML_Serializer/Unserializer.php');

global $g_localizerConfig;

// The default language, which forms the keys for 
// all other languages.
$g_localizerConfig['DEFAULT_LANGUAGE'] = 'en';

// Filename prefix for translation files.
$g_localizerConfig['FILENAME_PREFIX'] = 'locals';

// Filename prefix for the global translation file -
// a file that is always loaded with the particular 
// locals file.
$g_localizerConfig['FILENAME_PREFIX_GLOBAL'] = 'globals';

// Set to a specific type if your code is using that type.
// Currently supported types are 'gs' and 'xml'.
// You can also set this to the empty string and the code
// will do its best to figure out the current type.
$g_localizerConfig['DEFAULT_FILE_TYPE'] = 'gs';

// The top-level directory to the set of directories
// that need translation files.
$g_localizerConfig['BASE_DIR'] = $_SERVER['DOCUMENT_ROOT'];

// The top-level directory to the set of directories
// that need translation files.
$g_localizerConfig['TRANSLATION_DIR'] = $_SERVER['DOCUMENT_ROOT'].'/admin-files/lang';

// Name of the XML file that contains the list of supported languages.
$g_localizerConfig['LANGUAGE_METADATA_FILENAME'] = 'languages.xml';

// File encoding for XML files.
$g_localizerConfig['FILE_ENCODING'] = 'UTF-8';

// For the interface - the relative path (from DOCUMENT_ROOT)
// of the icons directory
global $Campsite;
$g_localizerConfig['ICONS_DIR'] = $Campsite['ADMIN_IMAGE_BASE_URL'];

// The size of the input fields for the admin interface.
$g_localizerConfig['INPUT_SIZE'] = 70;

// List supported file types, in order of preference.
$g_localizerConfig['FILE_TYPES'] = array('xml', 'gs');

$g_localizerConfig['LOADED_FILES'] = array();

// Map of prefixes to directory names.
$mapPrefixToDir = array();
$mapPrefixToDir[""] = null;
$mapPrefixToDir["globals"] = null;
$mapPrefixToDir["home"] = "/admin-files/";
$mapPrefixToDir["api"] = "/classes/";
$mapPrefixToDir["pub"] = "/admin-files/pub";
$mapPrefixToDir["issues"] = "/admin-files/issues";
$mapPrefixToDir["sections"] = "/admin-files/sections";
$mapPrefixToDir["articles"] = "/admin-files/articles";
$mapPrefixToDir["article_images"] = "/admin-files/articles/images";
$mapPrefixToDir["article_files"] = "/admin-files/articles/files";
$mapPrefixToDir["article_topics"] = "/admin-files/articles/topics";
$mapPrefixToDir["imagearchive"] = "/admin-files/imagearchive";
$mapPrefixToDir["templates"] = "/admin-files/templates";
$mapPrefixToDir["article_types"] = "/admin-files/article_types";
$mapPrefixToDir["article_type_fields"] = "/admin-files/article_types/fields";
$mapPrefixToDir["topics"] = "/admin-files/topics";
$mapPrefixToDir["languages"] = "/admin-files/languages";
$mapPrefixToDir["country"] = "/admin-files/country";
$mapPrefixToDir["localizer"] = "/admin-files/localizer";
$mapPrefixToDir["logs"] = "/admin-files/logs";
$mapPrefixToDir["users"] = "/admin-files/users";
$mapPrefixToDir["user_subscriptions"] = "/admin-files/users/subscriptions";
$mapPrefixToDir["user_subscription_sections"] = "/admin-files/users/subscriptions/sections";
$mapPrefixToDir["user_types"] = "/admin-files/user_types";
$g_localizerConfig["MAP_PREFIX_TO_DIR"] = $mapPrefixToDir;
unset($mapPrefixToDir);

?>