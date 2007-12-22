<?php
// Check permissions
if (!$g_user->hasPermission('ManagePoll')) {
    camp_html_display_error(getGS('You do not have the right to manage polls.'));
    exit;
}

$f_poll_nr = Input::Get('f_poll_nr', 'int');
$f_fk_language_id = Input::Get('f_fk_language_id', 'int');

$f_title = Input::Get('f_title', 'string');
$f_question = Input::Get('f_question', 'string');
$f_date_begin = Input::Get('f_date_begin', 'string');
$f_date_end = Input::Get('f_date_end', 'string');
$f_is_display_expired = Input::Get('f_is_display_expired', 'boolean');
$f_is_used_as_default = Input::Get('f_is_used_as_default', 'boolean');
$f_nr_of_answers = Input::Get('f_nr_of_answers', 'int');

$f_answers = Input::Get('f_answer', 'array');

if ($f_poll_nr) {
    // update existing poll   
    $poll = new Poll($f_fk_language_id, $f_poll_nr);
    $poll->setProperty('title', $f_title);
    $poll->setProperty('question', $f_question);
    $poll->setProperty('date_begin', $f_date_begin);
    $poll->setProperty('date_end', $f_date_end);
    $poll->setProperty('is_display_expired', $f_is_display_expired);
    $poll->setProperty('nr_of_answers', $f_nr_of_answers);
    
    $poll->setAsDefault($f_is_used_as_default);

    foreach ($f_answers as $nr_answer => $text) {
        if ($text !== '__undefined__') {
            $answer =& new PollAnswer($f_fk_language_id, $poll->getNumber(), $nr_answer);
            if ($answer->exists()) {
                $answer->setProperty('answer', $text);   
            } else {
                $answer->create($text);
            }
        }
    }
    
    PollAnswer::SyncNrOfAnswers($poll->getLanguageId(), $poll->getNumber());   

} else {
    // create new poll
    $poll =& new Poll($f_fk_language_id);   
    $success = $poll->create($f_title, $f_question, $f_date_begin, $f_date_end, $f_nr_of_answers, $f_is_display_expired);
    
    if ($success) {
        foreach ($f_answers as $nr_answer => $text) {
            if ($text !== '__undefined__') {
                $answer =& new PollAnswer($f_fk_language_id, $poll->getNumber(), $nr_answer);
                $success = $answer->create($text);
            }
        }            
    }
}
$f_from = Input::Get('f_from', 'string', 'index.php');
header('Location: '.$f_from);
?>