<?php
class poll_linker
{
    function getSelected($type, $IdLanguage, $IdPublication=null, $NrIssue=null, $NrSection=null, $NrArticle=null)
    {
        global $DB;
        $NrPolls = array();
        
        switch ($type) {
            case 'article':
                $query = "SELECT NrPoll
                          FROM   poll_article
                          WHERE  NrArticle  = $NrArticle AND
                                 IdLanguage = $IdLanguage";
            break; 
            
            case 'section':
                $query = "SELECT NrPoll
                          FROM   poll_section
                          WHERE  NrSection      = $NrSection        AND
                                 IdPublication  = $IdPublication    AND
                                 NrIssue        = $NrIssue          AND
                                 IdLanguage     = $IdLanguage";
            break;   
            
            case 'issue':
            
            break;
            
            case 'publication':
            
            break;
            
            default:
                return array();
            break;
        }
        $res    = sqlQuery($DB['modules'], $query);  
        while ($row = mysql_fetch_array($res)) {
            $NrPolls[$row['NrPoll']] = true; 
        }
        return $NrPolls;            
    }
    
    function selectPoll($type, $IdLanguage, $IdPublication=null, $NrIssue=null, $NrSection=null, $NrArticle=null)
    {
        global $DB;
        $selected = $this->getSelected($type, $IdLanguage, $IdPublication, $NrIssue, $NrSection, $NrArticle);
        $selector = '<select name="NrPolls[]" size="7" multiple>';
        $query = "SELECT m.Number, q.Title
                  FROM   poll_main AS m, 
                         poll_questions AS q
                  WHERE  m.Number      = q.NrPoll   AND 
                         q.IdLanguage  = $IdLanguage";
        $polls = sqlQuery($DB['modules'], $query);
        
        while ($poll = mysql_fetch_array($polls)) {
            $selector .= "<option value='{$poll['Number']}'";
            if ($selected[$poll['Number']]) $selector .= " selected";
            reset($selected);
            $selector .= ">{$poll['Title']}</option>";
        }
        
        $selector .= '</select>';        
        return $selector;
    }


    function LinkPoll($NrPolls, $type, $IdLanguage, $IdPublication=null, $NrIssue=null, $NrSection=null, $NrArticle=null)
    {
        global $DB;
        switch ($type) {
            case 'article':
                $query[] = "DELETE
                            FROM   poll_article
                            WHERE  NrArticle  = $NrArticle    AND
                                   IdLanguage = $IdLanguage";
            break; 
            
            case 'section':
                $query[] = "DELETE
                            FROM   poll_section
                            WHERE  NrSection      = $NrSection        AND
                                   IdPublication  = $IdPublication    AND
                                   NrIssue        = $NrIssue          AND
                                   IdLanguage     = $IdLanguage";
            break;   
            
            case 'issue':
            
            break;
            
            case 'publication':
            
            break;   
        }

        if (is_array($NrPolls)) {
            while (list($key, $NrPoll) = each($NrPolls)) {
                switch ($type) {
                case 'article':
                    $query[] = "INSERT
                                INTO   poll_article
                                SET    NrPoll     = $NrPoll,
                                       IdLanguage = $IdLanguage,
                                       NrArticle  = $NrArticle";
                break; 
                
                case 'section':
                    $query[] = "INSERT
                                INTO   poll_section
                                SET    NrPoll         = $NrPoll,
                                       IdLanguage     = $IdLanguage,
                                       NrSection      = $NrSection,
                                       IdPublication  = $IdPublication,
                                       NrIssue        = $NrIssue";
                break;   
                
                case 'issue':
                
                break;
                
                case 'publication':
                
                break;
                }
            }
        }
        sqlQuery($DB['modules'], $query);
    }
}
?>