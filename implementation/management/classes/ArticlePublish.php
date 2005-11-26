<?php 
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable 
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT'] 
// is not defined in these cases.
if (!isset($g_documentRoot)) {
    $g_documentRoot = $_SERVER['DOCUMENT_ROOT'];
}
require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/Article.php');

/**
 * @package Campsite
 */
class ArticlePublish extends DatabaseObject {
	var $m_keyColumnNames = array('id');
	var $m_dbTableName = 'ArticlePublish';
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('id', 
							   'fk_article_number', 
							   'fk_language_id', 
							   'time_action', 
							   'publish_action', 
							   'publish_on_front_page', 
							   'publish_on_section_page', 
							   'is_completed');
	
	/**
	 * This table delays an article's publish time to a later date.
	 *
	 * @param int $p_id
	 */
	function ArticlePublish($p_id = null) 
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['id'] = $p_id;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor
	

	/**
	 * Get the unique ID that identifies this action.
	 * @return int
	 */
	function getArticlePublishId()
	{
		return $this->getProperty('id');
	} // fn getArticlePublishId
	
	
	/**
	 * Return the article number associated with this action.
	 * @return int
	 */
	function getArticleNumber() 
	{
	    return $this->getProperty('fk_article_number');
	} // fn getArticleNumber
	
	
	/**
	 * Set the Article number.
	 * @param int $p_value
	 * @return boolean
	 */
	function setArticleNumber($p_value)
	{
		return $this->setProperty('fk_article_number', $p_value);
	} // fn setArticleNumber
	
	
	/**
	 * Return the language ID of the article.
	 * @return int
	 */
	function getLanguageId() 
	{
	    return $this->getProperty('fk_language_id');
	} // fn getLanguageId
	
	
	/**
	 * Set the language ID.
	 * @param int $p_value
	 * @return boolean
	 */
	function setLanguageId($p_value)
	{
		return $this->setProperty('fk_language_id', $p_value);
	} // fn setLanguageId
	
	
	/**
	 * Get the published state to switch to when the "publish time" arrives:
	 * NULL for no action, 'P' for Publish, or 'U' for Unpublish.
	 * @return mixed
	 */ 
	function getPublishAction() 
	{
		return $this->m_data['publish_action'];
	} // fn getPublishAction

	
	/**
	 * Set the published state to switch to when the "publish time" arrives:
	 * NULL for no action, 'P' for Publish, or 'U' for Unpublish.	 
	 * @return void
	 */
	function setPublishAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'P') || ($p_value == 'U') ) {
			$this->setProperty('publish_action', $p_value);
		}
		elseif (is_null($p_value)) {
			$this->setProperty('publish_action', 'NULL', true, true);
		}
	} // fn setPublishAction
	
	
	/**
	 * Get the front page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function getFrontPageAction() 
	{
		return $this->m_data['publish_on_front_page'];
	} // fn getFrontPageAction
	
	
	/**
	 * Set the front page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function setFrontPageAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'S') || ($p_value == 'R') ) {
			$this->setProperty('publish_on_front_page', $p_value);
		}
		elseif (is_null($p_value)) {
			$this->setProperty('publish_on_front_page', 'NULL', true, true);
		}
	} // fn setFrontPageAction
	
	
	/**
	 * Get the section page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function getSectionPageAction() 
	{
		return $this->m_data['publish_on_section_page'];
	} // fn getSectionPageAction
	
	
	/**
	 * Set the section page state to switch to when the "publish time" arrives.
	 * This can be NULL for no action, 'S' for Show, or 'R' for Remove.
	 * @return mixed
	 */
	function setSectionPageAction($p_value) 
	{
		$p_value = strtoupper($p_value);
		if ( ($p_value == 'S') || ($p_value == 'R') ) {
			$this->setProperty('publish_on_section_page', $p_value);
		}
		elseif (is_null($p_value)) {
			$this->setProperty('publish_on_section_page', 'NULL', true, true);
		}
	} // fn setSectionPageAction

	
	/**
	 * Get the time the event is scheduled to happen.
	 * @return string
	 */
	function getActionTime() 
	{
		return $this->m_data['time_action'];
	} // fn getActionTime
	
	
	/**
	 * Set the time when the action(s) should be taken.
	 * The parameter given should be in the form "YYYY-MM-DD HH:MM:SS".
	 * @param string $p_value
	 * @return boolean
	 */
	function setActionTime($p_value)
	{
		return $this->setProperty('time_action', $p_value);
	} // fn setActionTime
	
	
	/**
	 * Mark that this action has been completed.
	 * @return void
	 */
	function setCompleted() 
	{
	    $this->setProperty('is_completed', 'Y');
	} // fn setCompleted
	
	
	/**
	 * Execute the action, and mark the action as completed.
	 * @return void
	 */
	function doAction() 
	{
		$publishAction = $this->getPublishAction();
		$frontPageAction = $this->getFrontPageAction();
		$sectionPageAction = $this->getSectionPageAction();
        $article =& new Article($this->m_data['fk_language_id'], $this->m_data['fk_article_number']);
		if ($publishAction == 'P') {
		    $article->setPublished('Y');
		}
		if ($publishAction == 'U') {
            $article->setPublished('S');   
		}
		if ($frontPageAction == 'S') {
		    $article->setOnFrontPage(true);
		}
		if ($frontPageAction == 'R') {
		    $article->setOnFrontPage(false);
		}
		if ($sectionPageAction == 'S') {
		    $article->setOnSectionPage(true);
		}
		if ($sectionPageAction == 'R') {
		    $article->setOnSectionPage(false);
		}
		$this->setCompleted();	    
	} // fn doAction
	
	
	/**
	 * Get all the events that will change the article's state.
	 * Returns an array of ArticlePublish objects.
	 *
	 * @param int $p_articleId
	 * @param int $p_languageId
	 * @return array
	 */
	function GetArticleEvents($p_articleId, $p_languageId = null) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM ArticlePublish '
					." WHERE fk_article_number=$p_articleId";
		if (!is_null($p_languageId)) {
			$queryStr .= " AND fk_language_id=$p_languageId ";
		}
		$queryStr .= ' ORDER BY time_action ASC ';
		$result = DbObjectArray::Create('ArticlePublish', $queryStr);
		return $result;
	} // fn GetArticleEvents
	
	
	/**
	 * Get all the actions that currently need to be performed.
	 * @return array
	 */
	function GetPendingActions() 
	{
	    global $Campsite;
	    $datetime = strftime("%Y-%m-%d %H:%M:00");
        $queryStr = "SELECT * FROM ArticlePublish, Articles "
                    . " WHERE ArticlePublish.time_action <= '$datetime'"
                    . " AND ArticlePublish.is_completed != 'Y'"
                    . " AND Articles.Published != 'N'"
                    . " ORDER BY ArticlePublish.time_action ASC";
        $result = DbObjectArray::Create('ArticlePublish', $queryStr);
        return $result;
	} // fn GetPendingActions
	
	
	/**
	 * Execute all pending actions.
	 * @return void
	 */
	function DoPendingActions() 
	{
        $actions = ArticlePublish::GetPendingActions();
    	foreach ($actions as $articlePublishObj) {
    	    $articlePublishObj->doAction();
    	}
	} // fn DoPendingActions
	
	
	/**
	 * For now, this is mostly a hack to get the home page working.
	 * The raw array is returned.
	 *
	 * @param int $p_limit
	 * @return array
	 */
	function GetFutureActions($p_limit) 
	{
	    global $Campsite;
	    $datetime = strftime("%Y-%m-%d %H:%M:00");
	    $dummyArticle =& new Article();
	    $columnNames = $dummyArticle->getColumnNames(true);
        $queryStr = "SELECT id, time_action, publish_action, publish_on_front_page, publish_on_section_page,"
                    . implode(",", $columnNames). " FROM Articles, ArticlePublish "
                    . " WHERE ArticlePublish.time_action >= '" . $datetime . "'"
                    . " AND ArticlePublish.is_completed != 'Y'"
                    . " AND Articles.Published != 'N'"
                    . " AND Articles.Number=ArticlePublish.fk_article_number "
                    . " AND Articles.IdLanguage=ArticlePublish.fk_language_id "
                    . " ORDER BY time_action DESC"
                    . " LIMIT $p_limit";
		$rows = $Campsite['db']->GetAll($queryStr);
		$addKeys = array();
		if ($rows && (count($rows) > 0)) {
    		foreach ($rows as $row) {
    		    $row["ObjectType"] = "article";
    		    $addKeys[$row['time_action']] = $row;
    		}
		}
        return $addKeys;
	} // fn GetFutureActions
	
} // class ArticlePublish

?>