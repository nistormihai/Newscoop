<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleImage.php");
require_once($_SERVER['DOCUMENT_ROOT']. "/classes/ArticleComment.php");

// This is used in TransformSubheads() in order to figure out when
// a SPAN tag closes.
global $g_spanCounter;
$g_spanCounter = -1;

// This is used in TransformInternalLinks() to figure out when
// the internal link tag closes.
global $g_internalLinkCounter;
$g_internalLinkCounter = 0;

/**
 * This function is a callback for preg_replace_callback();
 * It will replace <span class="campsite_subhead">...</span>
 * with <!** Title>...<!** EndTitle>
 */
function TransformSubheads($match) {
	global $g_spanCounter;
	// This matches '<span class="campsite_subhead">'
	if (preg_match("/<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>/i", $match[0])) {
		//echo "matched ".htmlspecialchars($match[0]);
		$g_spanCounter = 1;
		return "<!** Title>";
	}
	// This matches '<span'
	elseif (($g_spanCounter >= 0) && preg_match("/<\s*span/i", $match[0])) {
		$g_spanCounter += 1;
	}
	// This matches '</span>'
	elseif (($g_spanCounter >= 0) && preg_match("/<\s*\/\s*span\s*>/i", $match[0])) {
		$g_spanCounter -= 1;
	}
	if ($g_spanCounter == 0) {
		$g_spanCounter = -1;
		return "<!** EndTitle>";
	}
	return $match[0];
} // fn TransformSubheads


/**
 * This function is a callback for preg_replace_callback().
 * It will replace <a href="campsite_internal_link?...">...</a>
 * with <!** Link Internal ...> ... <!** EndLink>
 * @param array p_match
 * @return string
 */
function TransformInternalLinks($p_match) {
	global $g_internalLinkCounter;
	// This matches '</a>'
	if (preg_match("/<\s*\/a\s*>/i", $p_match[0])) {
		// Check if we are closing an internal link
		if ($g_internalLinkCounter > 0) {
			// Replace the HTML tag with a template tag
			$retval = "<!** EndLink>";
			$g_internalLinkCounter = 0;
			return $retval;
		}
		else {
			// Leave the HTML tag as is (for external links).
			return '</a>';
		}
	}
	// This matches '<a href="campsite_internal_link?IdPublication=1&..." ...>'
	elseif (preg_match("/<\s*a\s*(((href\s*=\s*[\"']campsite_internal_link[?][\w&=;]*[\"'])|(target\s*=\s*['\"][_\w]*['\"]))[\s]*)*[\s\w\"']*>/i", $p_match[0])) {

		// Get the URL
		preg_match("/href\s*=\s*[\"'](campsite_internal_link[?][\w&=;]*)[\"']/i", $p_match[0], $url);
		$url = isset($url[1]) ? $url[1] : '';
		$parsedUrl = parse_url($url);
		$parsedUrl = str_replace("&amp;", "&", $parsedUrl);

		// Get the target, if there is one
		preg_match("/target\s*=\s*[\"']([_\w]*)[\"']/i", $p_match[0], $target);
		$target = isset($target[1]) ? $target[1] : null;

		// Replace the HTML tag with a template tag
		$retval = "<!** Link Internal ".$parsedUrl["query"];
		if (!is_null($target)) {
			$retval .= " TARGET ".$target;
		}
		$retval .= ">";

		// Mark that we are now inside an internal link.
		$g_internalLinkCounter = 1;
		return $retval;
	}
} // fn TransformInternalLinks


/**
 * This function is a callback for preg_replace_callback().
 * It will replace <img src="http://[hostname]/[image_dir]/cms-image-000000001.jpg" align="center" alt="alternate text" sub="caption text" id="5">
 * with <!** Image [image_template_id] align=CENTER alt="alternate text" sub="caption text">
 * @param array p_match
 * @return string
 */
function TransformImageTags($p_match) {
	global $f_article_number;
	array_shift($p_match);
	$attrs = array();
	foreach ($p_match as $attr) {
		$attr = split('=', $attr);
		if (isset($attr[0]) && !empty($attr[0])) {
			$attrName = trim(strtolower($attr[0]));
			$attrValue = isset($attr[1]) ? $attr[1] : '';
			// Strip out the quotes
			$attrValue = str_replace('"', '', $attrValue);
			$attrValue = str_replace("'", '', $attrValue);
			$attrs[$attrName] = $attrValue;
		}
	}

	if (!isset($attrs['id'])) {
		return '';
	} else {
		$templateId = $attrs['id'];
		$articleImage =& new ArticleImage($f_article_number, null, $templateId);
		if (!$articleImage->exists()) {
			return '';
		}
	}
	$alignTag = '';
	if (isset($attrs['align'])) {
		$alignTag = 'align='.$attrs['align'];
	}
	$altTag = '';
	if (isset($attrs['alt'])) {
		$altTag = 'alt="'.$attrs['alt'].'"';
	}
	$captionTag = '';
	if (isset($attrs['sub'])) {
		$captionTag = 'sub="'.$attrs['sub'].'"';
	}
	$imageTag = "<!** Image $templateId $alignTag $altTag $captionTag>";
	return $imageTag;
} // fn TransformImageTags


$f_publication_id = Input::Get('f_publication_id', 'int', 0, true);
$f_issue_number = Input::Get('f_issue_number', 'int', 0, true);
$f_section_number = Input::Get('f_section_number', 'int', 0, true);
$f_language_id = Input::Get('f_language_id', 'int', 0, true);

$f_language_selected = Input::Get('f_language_selected', 'int', 0);
$f_article_number = Input::Get('f_article_number', 'int', 0);
$f_on_front_page = Input::Get('f_on_front_page', 'string', '', true);
$f_on_section_page = Input::Get('f_on_section_page', 'string', '', true);
$f_is_public = Input::Get('f_is_public', 'string', '', true);
$f_keywords = Input::Get('f_keywords');
$f_article_title = Input::Get('f_article_title');
$f_message = Input::Get('f_message', 'string', '', true);
$f_creation_date = Input::Get('f_creation_date');
$f_comment_status = Input::Get('f_comment_status', 'string', '', true);
if (isset($_REQUEST['save_and_close'])) {
	$f_save_button = 'save_and_close';
	$BackLink = "/$ADMIN/articles/index.php?f_publication_id=$f_publication_id&f_issue_number=$f_issue_number&f_language_id=$f_language_id&f_section_number=$f_section_number";
} else {
	$f_save_button = 'save';
	$BackLink = "/$ADMIN/";
}

if (!Input::IsValid()) {
	camp_html_display_error(getGS('Invalid input: $1', Input::GetErrorString()), $BackLink);
	exit;
}

// Fetch article
$articleObj =& new Article($f_language_selected, $f_article_number);
if (!$articleObj->exists()) {
	camp_html_display_error(getGS('No such article.'), $BackLink);
}

$articleTypeObj = $articleObj->getArticleData();
$dbColumns = $articleTypeObj->getUserDefinedColumns();

$articleFields = array();
foreach ($dbColumns as $dbColumn) {
	if (isset($_REQUEST[$dbColumn->getName()])) {
		$articleFields[$dbColumn->getName()] = trim(Input::Get($dbColumn->getName()));
	}
}

if (!empty($f_message)) {
	camp_html_add_msg($f_message, "ok");
}

if (!$articleObj->userCanModify($g_user)) {
	camp_html_add_msg(getGS("You do not have the right to change this article.  You may only edit your own articles and once submitted an article can only be changed by authorized users."));
	camp_html_goto_page($BackLink);
}
// Only users with a lock on the article can change it.
if ($articleObj->isLocked() && ($g_user->getUserId() != $articleObj->getLockedByUser())) {
	$diffSeconds = time() - strtotime($articleObj->getLockTime());
	$hours = floor($diffSeconds/3600);
	$diffSeconds -= $hours * 3600;
	$minutes = floor($diffSeconds/60);
	$lockUser =& new User($articleObj->getLockedByUser());
	camp_html_add_msg(getGS('Could not save the article. It has been locked by $1 $2 hours and $3 minutes ago.', $lockUser->getRealName(), $hours, $minutes));
	camp_html_goto_page($BackLink);
}

// Update the first comment if the article title has changed
if ($f_article_title != $articleObj->getTitle()) {
	$firstPostId = ArticleComment::GetCommentThreadId($articleObj->getArticleNumber(), $articleObj->getLanguageId());
	if ($firstPostId) {
		$firstPost =& new Phorum_message($firstPostId);
		$firstPost->setSubject($f_article_title);
	}
}

// Update the article.
$articleObj->setOnFrontPage(!empty($f_on_front_page));
$articleObj->setOnSectionPage(!empty($f_on_section_page));
$articleObj->setIsPublic(!empty($f_is_public));
$articleObj->setKeywords($f_keywords);
$articleObj->setTitle($f_article_title);
$articleObj->setIsIndexed(false);
if (!empty($f_comment_status)) {
    if ($f_comment_status == "enabled" || $f_comment_status == "locked") {
        $commentsEnabled = true;
    } else {
        $commentsEnabled = false;
    }
    // If status has changed, then you need to show/hide all the comments
    // as appropriate.
    if ($articleObj->commentsEnabled() != $commentsEnabled) {
	    $articleObj->setCommentsEnabled($commentsEnabled);
		$comments = ArticleComment::GetArticleComments($f_article_number, $f_language_selected);
		if ($comments) {
			foreach ($comments as $comment) {
				$comment->setStatus($commentsEnabled?PHORUM_STATUS_APPROVED:PHORUM_STATUS_HIDDEN);
			}
		}
    }
    $articleObj->setCommentsLocked($f_comment_status == "locked");
}

// Make sure that the time stamp is updated.
$articleObj->setProperty('time_updated', 'NOW()', true, true);

// Verify creation date is in the correct format.
// If not, dont change it.
if (preg_match("/\d{4}-\d{2}-\d{2}/", $f_creation_date)) {
	$articleObj->setCreationDate($f_creation_date);
}

foreach ($articleFields as $dbColumnName => $text) {
	// Replace <span class="subhead"> ... </span> with <!** Title> ... <!** EndTitle>
	$text = preg_replace_callback("/(<\s*span[^>]*class\s*=\s*[\"']campsite_subhead[\"'][^>]*>|<\s*span|<\s*\/\s*span\s*>)/i", "TransformSubheads", $text);

	// Replace <a href="campsite_internal_link?IdPublication=1&..." ...> ... </a>
	// with <!** Link Internal IdPublication=1&...> ... <!** EndLink>
	$text = preg_replace_callback("/(<\s*a\s*(((href\s*=\s*[\"']campsite_internal_link[?][\w&=;]*[\"'])|(target\s*=\s*['\"][_\w]*['\"]))[\s]*)*[\s\w\"']*>)|(<\s*\/a\s*>)/i", "TransformInternalLinks", $text);

	// Replace <img src="A" align="B" alt="C" sub="D">
	// with <!** Image [image_template_id] align=B alt="C" sub="D">
	$srcAttr = "(src\s*=\s*[\"'][^'\"]*[\"'])";
	$altAttr = "(alt\s*=\s*['\"][^'\"]*['\"])";
	$alignAttr = "(align\s*=\s*['\"][^'\"]*['\"])";
	$subAttr = "(sub\s*=\s*['\"][^'\"]*['\"])";
	$subAttr = "(id\s*=\s*['\"][^'\"]*['\"])";
	$text = preg_replace_callback("/<\s*img\s*(($srcAttr|$altAttr|$alignAttr|$subAttr)\s*)*[\s\w\"']*\/>/i", "TransformImageTags", $text);
	$articleTypeObj->setProperty($dbColumnName, $text);
}

if ($f_save_button == "save") {
	camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'edit.php'));
} elseif ($f_save_button == "save_and_close") {
	if ($f_publication_id > 0) {
		camp_html_goto_page(camp_html_article_url($articleObj, $f_language_id, 'index.php'));
	} else {
		camp_html_goto_page("/$ADMIN/");
	}
}
?>