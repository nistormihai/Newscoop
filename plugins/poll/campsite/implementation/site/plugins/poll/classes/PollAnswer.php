<?php
/**
 * @package Campsite
 */
class PollAnswer extends DatabaseObject {
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('fk_poll_nr', 'fk_language_id', 'nr_answer');

    var $m_dbTableName = 'plugin_poll_answer';

    var $m_columnNames = array(
        // int - poll id
        'fk_poll_nr',

        // int - language id
        'fk_language_id',

        // int - nr of answer
        'nr_answer',

        // string - the literal answer
        'answer',
        
        // int - number of votes for this answer
        'nr_of_votes',
        
        // float - score of this answers in this language
        'percentage',
        
        // float - score of this answers overall languages
        'percentage_overall',
        
        // timestamp - last_modified
        'last_modified'
        );

    /**
     * Construct by passing in the primary key to access the poll answer in
     * the database.
     *
     * @param int $p_IdLanguage
     * @param int $p_id
     */
    function PollAnswer($p_fk_language_id = null, $p_fk_poll_nr = null, $p_nr_answer = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['fk_language_id'] = $p_fk_language_id;
        $this->m_data['fk_poll_nr'] = $p_fk_poll_nr;
        $this->m_data['nr_answer'] = $p_nr_answer;
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
    } // constructor


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    function __create($p_values = null) { return parent::create($p_values); }


    /**
     * Create an poll answer in the database.  Use the SET functions to
     * change individual values.
     *
     * @param string $p_fk_default_language_id
     * @param date $p_date_begin
     * @param date $p_date_end
     * @param int $p_nr_of_answers
     * @param bool $p_is_show_after_expiration
     * @return void
     */
    function create($p_answer)
    {
        global $g_ado_db;
        
        if (!strlen($p_answer)) {
            return false;   
        }

        // Create the record
        $values = array(
            'answer' => $p_answer      
        );


        $success = parent::create($values);
        if (!$success) {
            return;
        }

        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Poll Id $1 created.', $this->m_data['IdPoll']);
        Log::Message($logtext, null, 31);
        */
        
        return true;
    } // fn create

    /**
     * Create a translation of an answer set.
     *
     * @param int $p_languageId
     * @param int $p_userId
     * @param string $p_name
     * @return Article
     */
    function CreateTranslationSet($p_fk_poll_nr, $p_source_language_id, $p_target_language_id)
    {
        // Construct the duplicate PollQuestion object.
        foreach (PollAnswer::getAnswers($p_fk_poll_nr, $p_source_language_id) as $answer) {
            $answer_copy = new PollAnswer($p_target_language_id, $p_fk_poll_nr, $answer->getNumber());
            $answer_copy->create($answer->getProperty('answer'));
        }
        /*
        if (function_exists("camp_load_translation_strings")) {
            camp_load_translation_strings("api");
        }
        $logtext = getGS('Article #$1 "$2" ($3) translated to "$5" ($4)',
            $this->getArticleNumber(), $this->getTitle(), $this->getLanguageName(),
            $articleCopy->getTitle(), $articleCopy->getLanguageName());
        Log::Message($logtext, null, 31);
        */
        
        return $pollAnswerCopy;
    } // fn createTranslation


    /**
     * Delete poll from database.  This will
     * only delete one specific translation of the poll question.
     *
     * @return boolean
     */
    function delete()
    {        
        // Delete from plugin_poll_question table
        $deleted = parent::delete();

        /*
        if ($deleted) {
            if (function_exists("camp_load_translation_strings")) {
                camp_load_translation_strings("api");
            }
            $logtext = getGS('Article #$1: "$2" ($3) deleted.',
                $this->m_data['Number'], $this->m_data['Name'],    $this->getLanguageName())
                ." (".getGS("Publication")." ".$this->m_data['IdPublication'].", "
                ." ".getGS("Issue")." ".$this->m_data['NrIssue'].", "
                ." ".getGS("Section")." ".$this->m_data['NrSection'].")";
            Log::Message($logtext, null, 32);
        }
        */
        return $deleted;
    } // fn delete
    
    public function OnPollDelete($p_fk_poll_nr, $p_fk_language_id)
    {
        foreach (PollAnswer::getAnswers($p_fk_poll_nr, $p_fk_language_id) as $answer) {
            $answer->delete();   
        }   
    }
    
    public function getAnswers($p_fk_poll_nr = null, $p_fk_language_id = null)
    {
        global $g_ado_db;
        $answers = array();
               
        if (!is_null($p_fk_poll_nr) && !is_null($p_fk_language_id)) {
            $fk_poll_nr = $p_fk_poll_nr;
            $fk_language_id = $p_fk_language_id;   
        } elseif (isset($this)) {
            $fk_poll_nr = $this->m_data['fk_poll_nr']; 
            $fk_language_id = $this->m_data['fk_language_id'];      
        }
        
        if (!$fk_poll_nr || !$fk_language_id) {
            return array();   
        }
        
        $query = "SELECT    nr_answer
                  FROM      plugin_poll_answer
                  WHERE     fk_poll_nr = $fk_poll_nr
                        AND fk_language_id = $fk_language_id
                  ORDER BY  nr_answer";
        
        $res = $g_ado_db->Execute($query);
        
        while ($row = $res->fetchRow()) {
            $answers[] = new PollAnswer($fk_language_id, $fk_poll_nr, $row['nr_answer']);      
        } 
        
        return $answers;    
    }
    
    public static function SyncNrOfAnswers($p_fk_language_id, $p_fk_poll_nr)
    {
        global $g_ado_db;
        
        $poll = new Poll($p_fk_language_id, $p_fk_poll_nr);
        $nr_of_answers = $poll->getProperty('nr_of_answers');
        
        $query = "DELETE FROM   plugin_poll_answer
                  WHERE         fk_poll_nr = $p_fk_poll_nr
                            AND fk_language_id = $p_fk_language_id
                            AND nr_answer > $nr_of_answers";
        $g_ado_db->execute($query);  
        
        Poll::triggerStatistics($p_fk_poll_nr);
    }
       
    public function getPoll()
    {
        $poll = new Poll($this->m_data['fk_language_id'], $this->m_data['fk_poll_id']); 
        
        return $poll;  
    }
    
    public function vote()
    {
        $this->setProperty('nr_of_votes', $this->getProperty('nr_of_votes') + 1);
        
        Poll::triggerStatistics($this->m_data['fk_poll_nr']);   
    }
        
    public function getNumber()
    {
        return $this->m_data['nr_answer'];   
    }
    
    public function getPollNumber()
    {
        return $this->m_data['fk_poll_nr'];   
    }
    
    public function getAnswer()
    {
        return $this->getProperty('answer');   
    }
    
    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');   
    }
    
    /////////////////// Special template engine methods below here /////////////////////////////
    
    /**
     * Gets an issue list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_count
     *    The count of answers.
     *
     * @return array $issuesList
     *    An array of Issue objects
     */
    public static function GetList($p_parameters, $p_order = null, &$p_count)
    {
        global $g_ado_db;
        
        if (!is_array($p_parameters)) {
            return null;
        }

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (empty($comparisonOperation)) {
                continue;
            }
            if (strpos($comparisonOperation['left'], 'poll_nr') !== false) {
                $poll_nr = $comparisonOperation['right'];
            }
            if (strpos($comparisonOperation['left'], 'language_id') !== false) {
                $language_id = $comparisonOperation['right'];
            }
        }
        
        $sqlClauseObj = new SQLSelectClause();
        
        // sets the columns to be fetched
        $tmpPollAnswer = new PollAnswer($language_id, $poll_nr);
		$columnNames = $tmpPollAnswer->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $sqlClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpPollAnswer->getDbTableName();
        $sqlClauseObj->setTable($mainTblName);
        unset($tmpPollAnswer);
        

        if (empty($language_id) || empty($poll_nr)) {
            return;   
        }
                
        $sqlClauseObj->addWhere("fk_language_id = $language_id");       
        $sqlClauseObj->addWhere("fk_poll_nr = $poll_nr");

        
        if (!is_array($p_order)) {
            $p_order = array('nr_answer' => 'ASC');
        }

        // sets the order condition if any
        foreach ($p_order as $orderColumn => $orderDirection) {
            $sqlClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }
        
        $sqlQuery = $sqlClauseObj->buildQuery();
        
        // count all available results
        $countRes = $g_ado_db->Execute($sqlQuery);
        $p_count = $countRes->recordCount();
        
        //get the wanted rows
        $pollAnswerRes = $g_ado_db->Execute($sqlQuery);
        
        // builds the array of poll objects
        $pollAnswersList = array();
        while ($pollAnswer = $pollAnswerRes->FetchRow()) {
            $pollAnswerObj = new PollAnswer($pollAnswer['fk_language_id'], $pollAnswer['fk_poll_nr'], $pollAnswer['nr_answer']);
            if ($pollAnswerObj->exists()) {
                $pollAnswersList[] = $pollAnswerObj;
            }
        }

        return $pollAnswersList;
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
        case 'poll_nr':
            $comparisonOperation['left'] = 'poll_nr';
            break;
        case 'language_id':
            $comparisonOperation['left'] = 'language_id';
            break;
        }

        if (isset($comparisonOperation['left'])) {
            $operatorObj = $p_param->getOperator();
            $comparisonOperation['right'] = $p_param->getRightOperand();
            $comparisonOperation['symbol'] = $operatorObj->getSymbol('sql');
        }

        return $comparisonOperation;
    } // fn ProcessListParameters
} // class PollQuestion

?>