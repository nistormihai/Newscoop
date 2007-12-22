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
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/db_connect.php');
require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/DbObjectArray.php');
require_once($g_documentRoot.'/classes/SQLSelectClause.php');
require_once($g_documentRoot.'/classes/Log.php');
require_once($g_documentRoot.'/classes/Language.php');
require_once($g_documentRoot.'/classes/Section.php');
require_once($g_documentRoot.'/classes/IssuePublish.php');
require_once($g_documentRoot.'/template_engine/classes/CampTemplate.php');

/**
 * @package Campsite
 */
class Issue extends DatabaseObject {
	var $m_dbTableName = 'Issues';
	var $m_keyColumnNames = array('IdPublication', 'Number', 'IdLanguage');
	var $m_columnNames = array(
		'IdPublication',
		'Number',
		'IdLanguage',
		'Name',
		'PublicationDate',
		'Published',
		'IssueTplId',
		'SectionTplId',
		'ArticleTplId',
		'ShortName');
	var $m_languageName = null;

	/**
	 * A publication has Issues, Issues have Sections and Articles.
	 * @param int $p_publicationId
	 * @param int $p_languageId
	 * @param int $p_issueNumber
	 */
	function Issue($p_publicationId = null, $p_languageId = null, $p_issueNumber = null)
	{
		parent::DatabaseObject($this->m_columnNames);
		$this->m_data['IdPublication'] = $p_publicationId;
		$this->m_data['IdLanguage'] = $p_languageId;
		$this->m_data['Number'] = $p_issueNumber;
		if ($this->keyValuesExist()) {
			$this->fetch();
		}
	} // constructor


	/**
	 * Create an issue.
	 * @param string $p_shortName
	 * @param array $p_values
	 * @return boolean
	 */
	function create($p_shortName, $p_values = null)
	{
	    $tmpValues = array('ShortName' => $p_shortName);
	    if (!is_null($p_values)) {
	       $tmpValues = array_merge($p_values, $tmpValues);
	    }
	    $success = parent::create($tmpValues);
	    if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
	    	$logtext = getGS('Issue $1 added in publication $2',
	    					 $this->m_data['Name']." (".$this->m_data['Number'].")",
	    					 $this->m_data['IdPublication']);
    		Log::Message($logtext, null, 11);
	    }
	    return $success;
	} // fn create


	/**
	 * Delete the Issue, and optionally all sections and articles contained within it.
	 * @param boolean $p_deleteSections
	 * @param boolean $p_deleteArticles
	 * @return int
	 * 		Return the number of articles deleted.
	 */
	function delete($p_deleteSections = true, $p_deleteArticles = true)
	{
		global $g_ado_db;

		// Delete all scheduled publishing events
	    IssuePublish::OnIssueDelete($this->m_data['IdPublication'], $this->m_data['Number'], $this->m_data['IdLanguage']);

	    // plugins: Remove poll assignments
		if (class_exists('PollIssue')) {
    		PollIssue::OnIssueDelete($this->getLanguageId(), $this->getIssueNumber(), $this->getPublicationId());
		}
		
		$articlesDeleted = 0;
		if ($p_deleteSections) {
    		$sections = Section::GetSections($this->m_data['IdPublication'], $this->m_data['Number'], $this->m_data['IdLanguage']);
    		foreach ($sections as $section) {
    		    $articlesDeleted += $section->delete($p_deleteArticles);
    		}
		}
	    $success = parent::delete();
	    if ($success) {
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
	    	$logtext = getGS('Issue $1 from publication $2 deleted',
	    		$this->m_data['Name']." (".$this->m_data['Number'].")",
	    		$this->m_data['IdPublication']);
			Log::Message($logtext, null, 12);
	    }
		return $articlesDeleted;
	} // fn delete


	/**
	 * Copy this issue and all sections.
	 * @param int $p_destPublicationId
	 * @param int $p_destIssueId
	 * @param int $p_destLanguageId
	 * @return Issue
	 */
	function __copy($p_destPublicationId, $p_destIssueId, $p_destLanguageId)
	{
        // Copy the issue
        $newIssue =& new Issue($p_destPublicationId, $p_destLanguageId, $p_destIssueId);
        $columns = array();
        $columns['Name'] = mysql_real_escape_string($this->getName());
    	$columns['IssueTplId'] = $this->m_data['IssueTplId'];
    	$columns['SectionTplId'] = $this->m_data['SectionTplId'];
    	$columns['ArticleTplId'] = $this->m_data['ArticleTplId'];
        $created = $newIssue->create($p_destIssueId, $columns);
        if ($created) {
        	// Copy the sections in the issue
            $sections = Section::GetSections($this->m_data['IdPublication'],
                							 $this->m_data['Number'],
                							 $this->m_data['IdLanguage']);
            foreach ($sections as $section) {
                $section->copy($p_destPublicationId, $p_destIssueId, $p_destLanguageId, null, false);
            }
            return $newIssue;
        } else {
            return null;
        }
	} // fn __copy


	/**
	 * Create a copy of this issue.  You can use this to:
	 * 1) Translate an issue.  In this case do:
	 *    $issue->copy(null, $issue->getIssueNumber(), $destinationLanguage);
	 * 2) Duplicate all translations of an issue within a publication:
	 *    $issue->copy();
	 * 3) Copy an issue to another publication:
	 *    $issue->copy($destinationPublication);
	 * Note: All sections will be copied, but not the articles.
	 *
	 * @param int $p_destPublicationId
	 *     (optional) Specify the destination publication.
	 *     Default is this issue's publication ID.
	 * @param int $p_destIssueId
	 *     (optional) Specify the destination issue ID.
	 *     If not specified, a new one will be generated.
	 * @param int $p_destLanguageId
	 *     (optional) Use this if you want the copy to be a translation of the current issue.
	 *     Default is to copy all translations of this issue.
	 * @return mixed
	 *		An array of Issues, a single Issue, or null on error.
	 */
	function copy($p_destPublicationId = null, $p_destIssueId = null, $p_destLanguageId = null)
	{
	    global $g_ado_db;
	    if (is_null($p_destPublicationId)) {
	        $p_destPublicationId = $this->m_data['IdPublication'];
	    }
	    if (is_null($p_destIssueId)) {
	        $p_destIssueId = Issue::GetUnusedIssueId($this->m_data['IdPublication']);
	    }
	    if (is_null($p_destLanguageId)) {
            $queryStr = 'SELECT * FROM Issues '
                        .' WHERE IdPublication='.$this->m_data['IdPublication']
                        .' AND Number='.$this->m_data['Number'];
            $srcIssues = DbObjectArray::Create('Issue', $queryStr);

            // Copy all translations of this issue.
            $newIssues = array();
            foreach ($srcIssues as $issue) {
                $newIssues[] = $issue->__copy($p_destPublicationId, $p_destIssueId, $issue->getLanguageId());
            }
            return $newIssues;
	    } else {
	        // Translate the issue.
	        return $this->__copy($p_destPublicationId, $p_destIssueId, $p_destLanguageId);
	    }
	} // fn copy


	/**
	 * Return the publication ID of the publication that contains this issue.
	 * @return int
	 */
	function getPublicationId()
	{
		return $this->m_data['IdPublication'];
	} // fn getPublicationId


	/**
	 * Return the language ID of the issue.
	 * @return int
	 */
	function getLanguageId()
	{
		return $this->m_data['IdLanguage'];
	} // fn getLanguageId


	/**
	 * Changing an issue's language will change the section language as well.
	 *
	 * @param int $p_value
	 */
	function setLanguageId($p_value)
	{
		global $g_ado_db;
		$sql = "UPDATE Sections SET IdLanguage=$p_value"
			  ." WHERE IdPublication=".$this->m_data['IdPublication']
			  ." AND NrIssue=".$this->m_data['Number']
			  ." AND IdLanguage=".$this->m_data['IdLanguage'];
		$success = $g_ado_db->Execute($sql);
		if ($success) {
			$this->setProperty('IdLanguage', $p_value);
		}
	} // fn setLanguageId


	/**
	 * A simple way to get the name of the language the article is
	 * written in.  The value is cached in case there are multiple
	 * calls to this function.
	 *
	 * @return string
	 */
	function getLanguageName()
	{
		if (is_null($this->m_languageName)) {
			$language =& new Language($this->m_data['IdLanguage']);
			$this->m_languageName = $language->getNativeName();
		}
		return $this->m_languageName;
	} // fn getLanguageName


	/**
	 * @return int
	 */
	function getIssueNumber()
	{
		return $this->m_data['Number'];
	} // fn getIssueNumber


	/**
	 * Get the name of the issue.
	 * @return string
	 */
	function getName()
	{
		return $this->m_data['Name'];
	} // fn getName


	/**
	 * Set the name of the issue.
	 * @param string
	 * @return boolean
	 */
	function setName($p_value)
	{
	    return $this->setProperty('Name', $p_value);
	} // fn setName


	/**
	 * Get the string used for the URL to this issue.
	 * @return string
	 */
	function getUrlName()
	{
		return $this->m_data['ShortName'];
	} // fn getUrlName


	/**
	 * Set the string used in the URL to access this Issue.
	 *
	 * @return boolean
	 */
	function setUrlName($p_value)
	{
	    return $this->setProperty('ShortName', $p_value);
	} // fn setUrlName


	/**
	 * Get the default template ID used for articles in this issue.
	 * @return int
	 */
	function getArticleTemplateId()
	{
		return $this->m_data['ArticleTplId'];
	} // fn getArticleTemplateId


	/**
	 * Set the default template ID used for articles in this issue.
	 *
	 * @param int $p_value
	 */
	function setArticleTemplateId($p_value)
	{
		if (is_numeric($p_value)) {
			return $this->setProperty('ArticleTplId', $p_value);
		}
	} // fn setArticleTemplateId


	/**
	 * Get the default template ID used for sections in this issue.
	 * @return int
	 */
	function getSectionTemplateId()
	{
		return $this->m_data['SectionTplId'];
	} // fn getSectionTemplateId


	/**
	 * Set the default template ID used for sections in this issue.
	 *
	 * @param int $p_value
	 */
	function setSectionTemplateId($p_value)
	{
		if (is_numeric($p_value)) {
			return $this->setProperty('SectionTplId', $p_value);
		}
	} // fn setSectionTemplateId


	/**
	 * Get the template ID used for this issue.
	 * @return int
	 */
	function getIssueTemplateId()
	{
		return $this->m_data['IssueTplId'];
	} // fn getIssueTemplateId


	/**
	 * Set the template ID used for this issue.
	 *
	 * @param int $p_value
	 */
	function setIssueTemplateId($p_value)
	{
		if (is_numeric($p_value)) {
			return $this->setProperty('IssueTplId', $p_value);
		}
	} // fn setIssueTemplateId


	/**
	 * Return the current state in the workflow:
	 * 'Y' if the issue is published, 'N' if it is not published.
	 *
	 * @return string
	 */
	function getWorkflowStatus()
	{
		return $this->m_data['Published'];
	} // fn getWorkflowStatus


	/**
	 * Set the workflow state of the issue.
	 *
	 * @param string $p_value
	 *		Can be NULL, 'Y', 'N', TRUE, or FALSE.
	 *		If set to NULL, the current value will be reversed.
	 *
	 * @return void
	 */
	function setWorkflowStatus($p_value = null)
	{
		$doPublish = null;
		if (is_null($p_value)) {
			if ($this->m_data['Published'] == 'Y') {
				$doPublish = false;
			} else {
				$doPublish = true;
			}
		} else {
			if (is_string($p_value)) {
				$p_value = strtoupper($p_value);
			}
			if (($this->m_data['Published'] == 'N') && (($p_value == 'Y') || ($p_value === true))) {
				$doPublish = true;
			} elseif (($this->m_data['Published'] == 'Y') && (($p_value == 'N') || ($p_value === false))) {
				$doPublish = false;
			}
		}
		if (!is_null($doPublish)) {
			if ($doPublish) {
				$this->setProperty('Published', 'Y', true);
				$this->setProperty('PublicationDate', 'NOW()', true, true);
			} else {
				$this->setProperty('Published', 'N', true);
			}

			// Log message
			if ($this->getWorkflowStatus() == 'Y') {
				$status = getGS('Published');
			} else {
				$status = getGS('Not published');
			}
			if (function_exists("camp_load_translation_strings")) {
				camp_load_translation_strings("api");
			}
			$logtext = getGS('Issue $1 changed status to $2',
							 $this->m_data['Number'].'. '.$this->m_data['Name'].' ('.$this->getLanguageName().')',
							 $status);
			Log::Message($logtext, null, 14);
		}
	} // fn setWorkflowStatus


	/**
	 * Get publication date in the form YYYY-MM-DD HH:MM:SS
	 *
	 * @return string
	 */
	function getPublicationDate()
	{
		return $this->m_data['PublicationDate'];
	} // fn getPublicationDate


	/**
	 * Set the publication date.  Given value should be in the form
	 * YYYY-MM-DD HH:MM:SS.
	 *
	 * @param string $p_value
	 */
	function setPublicationDate($p_value)
	{
		if (is_string($p_value)) {
			return $this->setProperty('PublicationDate', $p_value);
		}
	} // fn setPublicationDate


	/**
	 * Get all the languages to which this issue has been translated.
	 *
	 * @param boolean $p_getUnusedLanguagesOnly
	 * 		Reverses the search and finds only those languages which this
	 * 		issue has not been translated into.
	 * @return array
	 * 		Return an array of Language objects.
	 */
	function getLanguages($p_getUnusedLanguagesOnly = false)
	{
		$tmpLanguage =& new Language();
		$columnNames = $tmpLanguage->getColumnNames(true);
		if ($p_getUnusedLanguagesOnly) {
			$queryStr = "SELECT ".implode(',', $columnNames)
						." FROM Languages LEFT JOIN Issues "
						." ON Issues.IdPublication = ".$this->m_data['IdPublication']
						." AND Issues.Number= ".$this->m_data['Number']
						." AND Issues.IdLanguage = Languages.Id "
						." WHERE Issues.IdPublication IS NULL";
		} else {
			$queryStr = "SELECT ".implode(',', $columnNames)
						." FROM Languages, Issues "
						." WHERE Issues.IdPublication = ".$this->m_data['IdPublication']
						." AND Issues.Number= ".$this->m_data['Number']
						." AND Issues.IdLanguage = Languages.Id ";
		}
		$languages = DbObjectArray::Create('Language', $queryStr);
		return $languages;
	} // fn getLanguages


	/**
	 * Get all the issues in the given publication as return them as an array
	 * of Issue objects.
	 *
	 * @param int $p_publicationId
	 * 		The publication ID.
	 *
	 * @param int $p_languageId
	 *		(Optional) Only return issues with this language.
	 *
	 * @param int $p_issueId
	 *		(Optional) Only return issues with this Issue ID.
	 *
	 * @param string $p_urlName
	 * 		(Optional) Only return issues that match this URL Name.
	 *
	 * @param int $p_preferredLanguage
	 *		(Optional) List this language before others.  This will override any 'ORDER BY' sql
	 *		options you have.
	 *
	 * @param array $p_sqlOptions
	 *
	 * @return array
	 */
	function GetIssues($p_publicationId = null,
	                   $p_languageId = null,
	                   $p_issueNumber = null,
	                   $p_urlName = null,
	                   $p_preferredLanguage = null,
	                   $p_sqlOptions = null)
	{
		$tmpIssue =& new Issue();
		$columnNames = $tmpIssue->getColumnNames(true);
		$queryStr = 'SELECT '.implode(',', $columnNames);
		if (!is_null($p_preferredLanguage)) {
			$queryStr .= ", abs(IdLanguage-$p_preferredLanguage) as LanguageOrder";
			$p_sqlOptions['ORDER BY'] = array('Number' => 'DESC', 'LanguageOrder' => 'ASC');
		}
		// We have to display the language name so oftern that we might
		// as well fetch it by default.
		$queryStr .= ', Languages.OrigName as LanguageName';
		$queryStr .= ' FROM Issues, Languages ';
		$whereClause = array();
		$whereClause[] = "Issues.IdLanguage=Languages.Id";
		if (!is_null($p_publicationId)) {
			$whereClause[] = "Issues.IdPublication=$p_publicationId";
		}
		if (!is_null($p_languageId)) {
			$whereClause[] = "Issues.IdLanguage=$p_languageId";
		}
		if (!is_null($p_issueNumber)) {
			$whereClause[] = "Issues.Number=$p_issueNumber";
		}
		if (!is_null($p_urlName)) {
			$whereClause[] = "Issues.ShortName='".mysql_real_escape_string($p_urlName)."'";
		}
		if (count($whereClause) > 0) {
			$queryStr .= ' WHERE '.implode(' AND ', $whereClause);
		}
		$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		global $g_ado_db;
		$issues = array();
		$rows = $g_ado_db->GetAll($queryStr);
		if (is_array($rows)) {
			foreach ($rows as $row) {
				$tmpObj =& new Issue();
				$tmpObj->fetch($row);
				$tmpObj->m_languageName = $row['LanguageName'];
				$issues[] = $tmpObj;
			}
		}

		return $issues;
	} // fn GetIssues


	/**
	 * Return the total number of issues in the database.
	 *
	 * @param int $p_publicationId
	 *		If specified, return the total number of issues in the given publication.
	 *
	 * @return int
	 */
	function GetNumIssues($p_publicationId = null)
	{
		global $g_ado_db;
		$queryStr = 'SELECT COUNT(*) FROM Issues ';
		if (is_numeric($p_publicationId)) {
			$queryStr .= " WHERE IdPublication=$p_publicationId";
		}
		$total = $g_ado_db->GetOne($queryStr);
		return $total;
	} // fn GetNumIssues


	/**
	 * Return an issue number that is not in use.
	 * @param int $p_publicationId
	 * @return int
	 */
	function GetUnusedIssueId($p_publicationId)
	{
		global $g_ado_db;
		$queryStr = "SELECT MAX(Number) + 1 FROM Issues "
		            ." WHERE IdPublication=$p_publicationId";
		$number = $g_ado_db->GetOne($queryStr);
		return $number;
	} // fn GetUnusedIssueId


	/**
	 * Returns the last published issue for the given publication / language.
	 * Returns null if no issue was found.
	 *
	 * @param int $p_publicationId
	 * @param int $p_languageId
	 * @return mixed
	 */
	function GetCurrentIssue($p_publicationId, $p_languageId = null)
	{
		global $g_ado_db;
		$queryStr = "SELECT Number, IdLanguage FROM Issues WHERE Published = 'Y' AND "
					. "IdPublication = $p_publicationId";
		if (!is_null($p_languageId)) {
			$queryStr .= " AND IdLanguage = $p_languageId";
		}
		$queryStr .= " ORDER BY Number DESC LIMIT 0, 1";
		$result = $g_ado_db->GetRow($queryStr);
		if (is_null($result)) {
	    	return null;
		}
		return new Issue($p_publicationId, $result['IdLanguage'], $result['Number']);
	}


	/**
	 * Return the last Issue created in this publication or NULL if there
	 * are no previous issues.
	 *
	 * @param int $p_publicationId
	 * @return Issue
	 */
	function GetLastCreatedIssue($p_publicationId)
	{
		global $g_ado_db;
		$queryStr = "SELECT MAX(Number) FROM Issues WHERE IdPublication=$p_publicationId";
		$maxIssueNumber = $g_ado_db->GetOne($queryStr);
		if (empty($maxIssueNumber)) {
			 return null;
		}
		$queryStr = "SELECT IdLanguage FROM Issues WHERE IdPublication=$p_publicationId AND Number=$maxIssueNumber";
		$idLanguage = $g_ado_db->GetOne($queryStr);
		$issue =& new Issue($p_publicationId, $idLanguage, $maxIssueNumber);
		return $issue;
	} // fn GetLastCreatedIssue


    /**
     * Gets an issues list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     * @param integer $p_count
     *    The total count of the elements; this count is computed without
     *    applying the start ($p_start) and limit parameters ($p_limit)
     *
     * @return array $issuesList
     *    An array of Issue objects
     */
    public static function GetList(array $p_parameters, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;

        $hasPublicationId = false;
        $hasLanguageId = false;
        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (empty($comparisonOperation)) {
                break;
            }
            if (strpos($comparisonOperation['left'], 'IdPublication') !== false) {
                $hasPublicationId = true;
            }
            if (strpos($comparisonOperation['left'], 'IdLanguage') !== false) {
                $hasLanguageId = true;
            }

            $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $comparisonOperation['right'] . "' ";
            $selectClauseObj->addWhere($whereCondition);
            $countClauseObj->addWhere($whereCondition);
        }

        // validates whether publication identifier was given
        if ($hasPublicationId == false) {
            CampTemplate::singleton()->trigger_error('missed parameter Publication '
                .'Identifier in statement list_topics');
            return;
        }
        // validates whether language identifier was given
/*        if ($hasLanguageId == false) {
            CampTemplate::singleton()->trigger_error('missed parameter Language '
                .'Identifier in statement list_topics');
            return;
        }
*/
        // sets the columns to be fetched
        $tmpIssue = new Issue();
		$columnNames = $tmpIssue->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }
        $countClauseObj->addColumn('COUNT(*)');

        // sets the main table for the query
        $selectClauseObj->setTable($tmpIssue->getDbTableName());
        $countClauseObj->setTable($tmpIssue->getDbTableName());
        unset($tmpIssue);

        if (is_array($p_order)) {
            $order = Issue::ProcessListOrder($p_order);
            // sets the order condition if any
            foreach ($order as $orderField=>$orderDirection) {
                $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
            }
        }

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $selectQuery = $selectClauseObj->buildQuery();
        $countQuery = $countClauseObj->buildQuery();
        $issues = $g_ado_db->GetAll($selectQuery);
        if (!is_array($issues)) {
            $p_count = 0;
            return array();
        }
        $p_count = $g_ado_db->GetOne($countQuery);

        // builds the array of issue objects
        $issuesList = array();
        foreach ($issues as $issue) {
            $issObj = new Issue($issue['IdPublication'],
                                $issue['IdLanguage'],
                                $issue['Number']);
            if ($issObj->exists()) {
                $issuesList[] = $issObj;
            }
        }

        return $issuesList;
    } // fn GetList


    /**
     * Processes a paremeter (condition) coming from template tags.
     *
     * @param array $p_param
     *      The array of parameters
     *
     * @return array $comparisonOperation
     *      The array containing processed values of the condition
     */
    private static function ProcessListParameters($p_param)
    {
        $comparisonOperation = array();

        switch (strtolower($p_param->getLeftOperand())) {
        case 'year':
        case 'publish_year':
            $comparisonOperation['left'] = 'YEAR(PublicationDate)';
            break;
        case 'mon_nr':
        case 'publish_month':
            $comparisonOperation['left'] = 'MONTH(PublicationDate)';
            break;
        case 'mday':
        case 'publish_mday':
            $comparisonOperation['left'] = 'DAYOFMONTH(PublicationDate)';
            break;
        case 'yday':
            $comparisonOperation['left'] = 'DAYOFYEAR(PublicationDate)';
            break;
        case 'wday':
            $comparisonOperation['left'] = 'DAYOFWEEK(PublicationDate)';
            break;
        case 'hour':
            $comparisonOperation['left'] = 'HOUR(PublicationDate)';
            break;
        case 'min':
            $comparisonOperation['left'] = 'MINUTE(PublicationDate)';
            break;
        case 'sec':
            $comparisonOperation['left'] = 'SECOND(PublicationDate)';
            break;
        case 'name':
            $comparisonOperation['left'] = 'Name';
            break;
        case 'number':
            $comparisonOperation['left'] = 'Number';
            break;
        case 'publicationdate':
            $comparisonOperation['left'] = 'PublicationDate';
            break;
        case 'idpublication':
            $comparisonOperation['left'] = 'IdPublication';
            break;
        case 'idlanguage':
            $comparisonOperation['left'] = 'IdLanguage';
            break;
        }

        if (isset($comparisonOperation['left'])) {
            $operatorObj = $p_param->getOperator();
            $comparisonOperation['right'] = $p_param->getRightOperand();
            $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $comparisonOperation;
    } // fn ProcessListParameters


    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $field=>$direction) {
            $dbField = null;
            switch (strtolower($field)) {
                case 'bynumber':
                    $dbField = 'Number';
                    break;
                case 'byname':
                    $dbField = 'Name';
                    break;
                case 'bydate':
                case 'bycreationdate':
                case 'bypublishdate':
                    $dbField = 'PublicationDate';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }

} // class Issue

?>