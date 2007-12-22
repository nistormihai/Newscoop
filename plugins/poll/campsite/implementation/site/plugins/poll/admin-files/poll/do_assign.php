<?php
// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$f_target = Input::Get('f_target', 'string');
$f_language_id = Input::Get('f_language_id', 'int');
$f_publication_id = Input::Get('f_publication_id', 'int');
$f_issue_nr = Input::Get('f_issue_nr', 'int');
$f_section_nr = Input::Get('f_section_nr', 'int');
$f_article_nr = Input::Get('f_article_nr', 'int');

$f_poll_exists = Input::Get('f_poll_exists', 'array', array());
$f_poll_checked = Input::Get('f_poll_checked', 'array', array());

$p_a = 0;
$p_u = 0;
 
switch ($f_target) {
    case 'publication':
        foreach ($f_poll_exists as $poll_nr => $lost) {
            $PollPublication =& new PollPublication($poll_nr, $f_publication_id);
            
            if (array_key_exists($poll_nr, $f_poll_checked) && !$PollPublication->exists()) {
                $PollPublication->create();
                $p_a++;
            } elseif (!array_key_exists($poll_nr, $f_poll_checked) && $PollPublication->exists()) {
                $PollPublication->delete();
                $p_u++;  
            }
        }
        ?>
        <script>
        window.opener.document.forms[0].onsubmit();
        window.opener.document.forms[0].submit();
        window.close();
        </script>
        <?php
    break;
    
    case 'issue':
        foreach ($f_poll_exists as $poll_nr => $lost) {
            $PollIssue =& new PollIssue($poll_nr, $f_language_id, $f_issue_nr, $f_publication_id);
            $x = $PollIssue->exists();
            if (array_key_exists($poll_nr, $f_poll_checked) && !$PollIssue->exists()) {
                $PollIssue->create();
                $p_a++;
            } elseif (!array_key_exists($poll_nr, $f_poll_checked) && $PollIssue->exists()) {
                $PollIssue->delete(); 
                $p_u++;   
            }
        }
        ?>
        <script>
        window.opener.document.forms['issue_edit'].onsubmit();
        window.opener.document.forms['issue_edit'].submit();
        window.close();
        </script>
        <?php
    break;
    
    case 'section':
        foreach ($f_poll_exists as $poll_nr => $val) {
            $PollSection =& new PollSection($poll_nr, $f_language_id, $f_section_nr, $f_issue_nr, $f_publication_id);
            
            if (array_key_exists($poll_nr, $f_poll_checked) && !$PollSection->exists()) {
                $PollSection->create();
                $a++;
            } elseif (!array_key_exists($poll_nr, $f_poll_checked) && $PollSection->exists()) {
                $PollSection->delete();
                $u++;    
            }
        }
        ?>
        <script>
        window.opener.document.forms['section_edit'].onsubmit();
        window.opener.document.forms['section_edit'].submit();
        window.close();
        </script>
        <?php
    break;
    
    case 'article':
        foreach ($f_poll_exists as $poll_nr => $val) {
            $PollArticle =& new PollArticle($poll_nr, $f_language_id, $f_article_nr);
            
            if (array_key_exists($poll_nr, $f_poll_checked) && !$PollArticle->exists()) {
                $PollArticle->create();
                $p_a++;
            } elseif (!array_key_exists($poll_nr, $f_poll_checked) && $PollArticle->exists()) {
                $PollArticle->delete();
                $p_u++;   
            }
        }
        ?>
        <script>
        //window.opener.document.forms['article_edit'].f_message.value = "<?php putGS("$1/$2 polls assigned/unassigned.", $p_a, $p_u); ?>";
        window.opener.document.forms['article_edit'].onsubmit();
        window.opener.document.forms['article_edit'].submit();
        window.close();
        </script>
        <?php
    break;
    
    default:
	   camp_html_display_error(getGS('Invalid input'), 'javascript: window.close()');
	   exit;
    break; 
}
?>
