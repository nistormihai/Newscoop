<?php
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
require_once($g_documentRoot.'/classes/ParserCom.php');

/**
 * @package Campsite
 */
class Topic extends DatabaseObject {
	var $m_keyColumnNames = array('Id', 'LanguageId');

	var $m_dbTableName = 'Topics';
	
	var $m_columnNames = array('Id', 'LanguageId', 'Name', 'ParentId');
	
	var $m_hasSubtopics = null;
	
	/**
	 * A topic is like a category for a piece of data.
	 *
	 * If the topic does not exist in the given language, 
	 * it will try to fetch a translation.
	 *
	 * @param int $p_id
	 * @param int $p_languageId
	 */
	function Topic($p_id = null, $p_languageId = null)
	{
		global $Campsite;

		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['Id'] = $p_id;
		$this->m_data['LanguageId'] = $p_languageId;
		if (!$this->keyValuesExist() || !$this->fetch()) {
			if ($p_languageId == null) {
				$p_languageId = 0;
			}
			$queryStr = "SELECT *, ABS(LanguageId - $p_languageId) as langDiff FROM Topics WHERE"
				. " Id = $p_id ORDER BY langDiff ASC";
			if ($row = $Campsite['db']->GetRow($queryStr)) {
				foreach ($row as $key=>$value) {
					$this->m_data[$key] = $value;
				}
				$this->m_exists = true;
			}
		}
	} // constructor
	

	/**
	 * Create a new topic.
	 *
	 * @param array $p_values
	 * @return boolean
	 */
	function create($p_values = null)
	{
		global $Campsite;
		$queryStr = "UPDATE AutoId SET TopicId = LAST_INSERT_ID(TopicId + 1)";
		$Campsite['db']->Execute($queryStr);
		$this->m_data['Id'] = $Campsite['db']->Insert_ID();
		$this->m_data['LanguageId'] = 1;
		if (isset($p_values['LanguageId'])) {
			$this->m_data['LanguageId'] = $p_values['LanguageId'];
		}
		$success = parent::create($p_values);
		if ($success) {
			ParserCom::SendMessage('topics', 'create', array("tpid" => $this->m_data['Id']));
		}
		return $success;
	} // fn create
	

	/**
	 * Delete the topic.
	 * @return boolean
	 */
	function delete()
	{
		$deleted = parent::delete();
		if ($deleted) {
			ParserCom::SendMessage('topics', 'delete', array("tpid" => $this->m_data['Id']));
		}
		return $deleted;
	} // fn delete
	
	
	/**
	 * @return string
	 */
	function getName() 
	{
		return $this->getProperty('Name');
	} // fn getName
	
	
	/**
	 * @param string $p_value
	 * @return boolean
	 */
	function setName($p_value) 
	{
		$changed = $this->setProperty('Name', $p_value);
		if ($changed) {
			ParserCom::SendMessage('topics', 'modify', array("tpid"=> $this->m_data['Id']));
		}
		return $changed;
	} // fn setName
	
	
	/**
	 * @return int
	 */
	function getTopicId() 
	{
		return $this->getProperty('Id');
	} // fn getTopicId
	
	
	/**
	 * Get the language of the topic.
	 * @return int
	 */
	function getLanguageId() 
	{
	    return $this->getProperty('LanguageId');
	} // fn getLanguageId
	
	
	/**
	 * @return int
	 */
	function getParentId() 
	{
		return $this->getProperty('ParentId');
	}
	
	
	/**
	 * Return an array of Topics starting from the root down
	 * to and including the current topic.
	 *
	 * @return array
	 */
	function getPath() 
	{
		global $Campsite;
		$row = true;
		$currentId = $this->m_data['Id'];
		$stack = array();
		while (($row !== false) && (count($row) > 0)) {
			$queryStr = 'SELECT * FROM Topics WHERE Id = '.$currentId;
			$row = $Campsite['db']->GetRow($queryStr);
			if (($row !== false) && (count($row) > 0)) {
				$topic =& new Topic();
				$topic->fetch($row);
				array_unshift($stack, $topic);
				$currentId = $topic->getParentId();
			}			
		}
		return $stack;
	} // fn getPath
		
	
	/**
	 * Get the subtopics (as an array of Topics) for this topic.
	 *
	 * @param int $p_languageId
	 * @param array $p_sqlOptions
	 * @return array
	 */
	function getSubtopics($p_languageId = null, $p_sqlOptions = null) 
	{
		global $Campsite;
		$queryStr = 'SELECT * FROM Topics '
					.' WHERE ParentId = '.$this->m_data['Id'];
		if (!is_null($p_languageId)) {
			$queryStr .= ' AND LanguageId='.$p_languageId;
		}
		$queryStr .= ' ORDER BY Name ';
		$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		$subtopics =& DbObjectArray::Create('Topic', $queryStr);
		return $subtopics;
	} // fn getSubtopics
	
	
	/**
	 * Return true if this topic has subtopics.
	 *
	 * @return boolean
	 */
	function hasSubtopics() 
	{
		global $Campsite;
		// Returned the cached value if available.
		if (!is_null($this->m_hasSubtopics)) {
			return $this->m_hasSubtopics;
		}
		$queryStr = 'SELECT COUNT(*) FROM Topics WHERE ParentId = '.$this->m_data['Id'];
		$numRows = $Campsite['db']->GetOne($queryStr);
		return ($numRows > 0);
	} // fn hasSubtopics

	
	/**
	 * Search the Topics table.
	 *
	 * @param int $p_id
	 * @param int $p_languageId
	 * @param string $p_name
	 * @param int $p_parentId
	 * @param array $p_sqlOptions
	 * @return array
	 */
	function GetTopics($p_id = null, $p_languageId = null, $p_name = null, 
					   $p_parentId = null, $p_sqlOptions = null)
	{
		$constraints = array();
		if (!is_null($p_id)) {
			$constraints[] = array("Id", $p_id);
		}
		if (!is_null($p_languageId)) {
			$constraints[] = array("LanguageId", $p_languageId);
		}
		if (!is_null($p_name)) {
			$constraints[] = array("Name", $p_name);
		}
		if (!is_null($p_parentId)) {
			$constraints[] = array("ParentId", $p_parentId);
		}
		return DatabaseObject::Search('Topics', $constraints, $p_sqlOptions);
	} // fn GetTopics
	
} // class Topics

?>