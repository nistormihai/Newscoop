<?php
/**
 * Campsite customized Smarty plugin
 * @package Campsite
 */


/**
 * Campsite list_subtopics block plugin
 *
 * Type:     block
 * Name:     list_subtopics
 * Purpose:  Provides a...
 *
 * @param string
 *     $p_params
 * @param string
 *     $p_smarty
 * @param string
 *     $p_content
 *
 * @return
 *
 */
function smarty_block_list_subtopics($p_params, $p_content, &$p_smarty, &$p_repeat)
{
    require_once $p_smarty->_get_plugin_filepath('shared','escape_special_chars');

    // gets the context variable
    $campContext = $p_smarty->get_template_vars('campsite');
    $html = '';

    if (!isset($p_content)) {
    	$start = 0;
    	$subtopicsList = new SubtopicsList($start, $p_params);
    	$campContext->setCurrentList($subtopicsList, array('topic'));
    }

    $currentSubtopic = $campContext->current_subtopics_list->defaultIterator()->current();
    if (is_null($currentSubtopic)) {
	    $p_repeat = false;
	    $campContext->resetCurrentList();
    	return $html;
    } else {
    	$p_repeat = true;
    	$campContext->topic = $currentSubtopic;
    }

    if (isset($p_content)) {
		$html = $p_content;
	    if ($p_repeat) {
    		$campContext->current_subtopics_list->defaultIterator()->next();
    		if (!is_null($campContext->current_subtopics_list->current)) {
    		    $campContext->topic = $campContext->current_subtopics_list->current;
    		}
    	}
    }

    return $html;
}

?>