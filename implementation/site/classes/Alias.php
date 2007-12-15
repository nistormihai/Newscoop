<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, because $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');

/**
 * @package Campsite
 */
class Alias extends DatabaseObject {
	var $m_dbTableName = 'Aliases';
	var $m_keyColumnNames = array('Id');
	var $m_keyIsAutoIncrement = true;
	var $m_columnNames = array('Id', 'Name', 'IdPublication');

	/**
	 * Constructor.
	 * @param int $p_id
	 */
	public function Alias($p_id = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		if (!is_null($p_id)) {
    		$this->m_data['Id'] = $p_id;
			$this->fetch();
		}
	} // constructor


	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->m_data['Id'];
	} // fn getId


	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 *
	 */
	public function setName($p_name)
	{
		return $this->setProperty('Name', $p_name);
	} // fn setName


	/**
	 * @return int
	 */
	public function getPublicationId()
	{
		return $this->m_data['IdPublication'];
	} // fn getPublicationId


	/**
	 * @param int $p_value
	 * @return boolean
	 */
	public function setPublicationId($p_value)
	{
		return $this->setProperty('IdPublication', $p_value);
	} // fn setPublicationId


	/**
	 * Get all the aliases that match the given criteria.
	 *
	 * @param int $p_id
	 * @param int $p_publicationId
	 * @param string $p_name
	 * @return array
	 */
	public static function GetAliases($p_id = null, $p_publicationId = null, $p_name = null)
	{
		$contraints = array();
		if (!is_null($p_publicationId)) {
			$contraints[] = array("IdPublication", $p_publicationId);
		}
		if (!is_null($p_name)) {
			$contraints[] = array("Name", $p_name);
		}
		if (!is_null($p_id)) {
			$contraints[] = array("Id", $p_id);
		}
		return DatabaseObject::Search('Alias', $contraints);
	} // fn GetAliases

} // class Alias

?>