<?php
/**
 * @package Campsite
 */
class InterviewItem extends DatabaseObject {
    /**
     * The column names used for the primary key.
     * @var array
     */
    var $m_keyColumnNames = array('item_id');
    
    var $m_keyIsAutoIncrement = true;

    var $m_dbTableName = 'plugin_interview_items';

    var $m_columnNames = array(
        // int - interview id
        'item_id',
    
        // int - interview id
        'fk_interview_id',

        // int - questioneer user id
        'fk_questioneer_user_id',
        
        // string - question text
        'question',
             
        // string - status
        'status',
        
        // string - answer
        'answer',
        
        // int - order
        'item_order',
        
        // timestamp - last_modified
        'last_modified'
        );
        

    /**
     * Construct by passing in the primary key to access the interview in
     * the database.
     *
     * @param int $p_fk_interview_id
     * @param int $p_item_id -  not required on creating item
     */
    public function __construct($p_fk_interview_id = null, $p_item_id = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        
        $this->m_data['fk_interview_id'] = $p_fk_interview_id;
        $this->m_data['item_id'] = $p_item_id;
        
        if ($this->keyValuesExist()) {
            $this->fetch();
        }
    } // constructor


    /**
     * A way for internal functions to call the superclass create function.
     * @param array $p_values
     */
    private function __create($p_values = null) { return parent::create($p_values); }
    
    
    /**
     * Create an interview in the database. Use the SET functions to
     * change individual values.
     *
     * @param date $p_date_begin
     * @param date $p_date_end
     * @param int $p_nr_of_answers
     * @param bool $p_is_show_after_expiration
     * @return void
     */
    public function create($p_fk_questioneer_user_id = null, $p_question = null, $p_status = 'new')
    {
        global $g_ado_db;
       
        /* 
        if (!strlen($p_title) || !strlen($p_question) || !$p_date_begin || !$p_date_end || !$p_nr_of_answers) {
            return false;   
        }
        */
        
        // Create the record
        $values = array(
            'fk_questioneer_user_id' => $p_fk_questioneer_user_id,
            'question' => $p_question,
            'status' => $p_status
        );


        $success = parent::create($values);
        if (!$success) {
            return false;
        }
        
        $query = "  SELECT  MAX(item_order) + 1 AS next
                    FROM    {$this->m_dbTableName}
                    WHERE   fk_interview_id = {$this->m_data['fk_interview_id']}";
        $max = $g_ado_db->getRow($query);
        
        // Set item_order
        $query = "  UPDATE  {$this->m_dbTableName}
                    SET     item_order = {$max['next']}
                    WHERE   item_id = {$this->m_data['item_id']}";
        $res = $g_ado_db->execute($query); 

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
     * Change the items position in the order sequence
     * relative to its current position.
     *
     * @param string $p_direction -
     *         Can be "up" or "down".  "Up" means towards the beginning of the list,
     *         and "down" means towards the end of the list.
     *
     * @param int $p_spacesToMove -
     *        The number of spaces to move the item.
     *
     * @return boolean
     */
    function positionRelative($p_direction, $p_spacesToMove = 1)
    {
        global $g_ado_db;

        // Get the item that is in the final position where this
        // article will be moved to.
        $compareOperator = ($p_direction == 'up') ? '<' : '>';
        $order = ($p_direction == 'up') ? 'desc' : 'asc';
        $queryStr = "   SELECT  item_order
                        FROM    {$this->m_dbTableName}
                        WHERE   fk_interview_id = {$this->m_data['fk_interview_id']}
                                AND item_order $compareOperator {$this->m_data['item_order']}
                        ORDER BY item_order $order
                        LIMIT ".($p_spacesToMove-1).", 1";
        $destRow = $g_ado_db->GetRow($queryStr);
        
        // Shift all items one space between the source and destination item.
        $operator = ($p_direction == 'up') ? '+' : '-';
        $minArticleOrder = min($destRow['item_order'], $this->m_data['item_order']);
        $maxArticleOrder = max($destRow['item_order'], $this->m_data['item_order']);
        $queryStr2 = "  UPDATE  {$this->m_dbTableName} 
                        SET     item_order = item_order $operator 1
                        WHERE   fk_interview_id = {$this->m_data['fk_interview_id']}
                                AND item_order >= $minArticleOrder
                                AND item_order <= $maxArticleOrder";
        $g_ado_db->Execute($queryStr2);

        // Change position of this item to the destination position.
        $queryStr3 = "  UPDATE  {$this->m_dbTableName}
                        SET     item_order = {$destRow['item_order']}
                        WHERE   item_id = {$this->m_data['item_id']}";
        $g_ado_db->Execute($queryStr3);

        // Re-fetch this item to get the updated order.
        $this->fetch();
        return true;
    } // fn positionRelative


    /**
     * Move the article to the given position (i.e. reorder the article).
     * @param int $p_moveToPosition
     * @return boolean
     */
    function positionAbsolute($p_moveToPosition = 1)
    {
        global $g_ado_db;
        // Get the item that is in the location we are moving
        // this one to.
        $queryStr = "   SELECT  item_order, item_id
                        FROM    {$this->m_dbTableName}
                        WHERE   fk_interview_id = {$this->m_data['fk_interview_id']}
                        ORDER BY item_order ASC 
                        LIMIT   ".($p_moveToPosition - 1).', 1';
        $destRow = $g_ado_db->GetRow($queryStr);
        if (!$destRow) {
            return false;
        }
        if ($destRow['item_order'] == $this->m_data['item_order']) {
            // Move the destination down one.
            $destItem =& new InterviewItem(null, $destRow['item_id']);
            $destItem->positionRelative("down", 1);
            return true;
        }
        if ($destRow['item_order'] > $this->m_data['item_order']) {
            $operator = '-';
        } else {
            $operator = '+';
        }
        // Reorder all the other items
        $minItemOrder = min($destRow['item_order'], $this->m_data['item_order']);
        $maxItemOrder = max($destRow['item_order'], $this->m_data['item_order']);
        $queryStr = "   UPDATE  {$this->m_dbTableName}
                        SET     item_order = item_order $operator 1
                        WHERE   fk_interview_id = {$this->m_data['fk_interview_id']}
                                AND item_order >= $minItemOrder
                                AND item_order <= $maxItemOrder";
        $g_ado_db->Execute($queryStr);

        // Reposition this item.
        $queryStr = "   UPDATE  {$this->m_dbTableName}
                        SET     item_order = {$destRow['item_order']}
                        WHERE   item_id = {$this->m_data['item_id']}";
        $g_ado_db->Execute($queryStr);

        $this->fetch();
        return true;
    } // fn positionAbsolute



    /**
     * Delete interview item from database.
     *
     * @return boolean
     */
    public function delete()
    {      
        // finally delete from main table
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


    /**
     * Construct query to recive polls from database
     *
     * @param int $p_fk_language
     * @return string
     */
    static private function GetQuery($p_fk_interview_id, array $p_order_by = null)
    {   
        $InterviewItem = new InterviewItem();
        
        $query = "SELECT    item_id, fk_interview_id 
                  FROM      {$InterviewItem->m_dbTableName} ";
        
        if (!count($p_order_by)) {
            $p_order_by = array('item_order' => 'ASC');   
        }
        
        foreach ($p_order_by as $col => $dir) {
            if (in_array($col, $InterviewItem->m_columnNames)) {
                $order .= "$col $dir , ";   
            }
        }
        
        $query .= "ORDER BY ".substr($order, 0, -2);   
        
        return $query;
    }
    
    /**
     * Get an array of interview objects 
     * You may specify the language
     *
     * @param unknown_type $p_fk_language_id
     * @param unknown_type $p_offset
     * @param unknown_type $p_limit
     * @return array
     */
    static public function GetInterviewItems($p_fk_interview_id, $p_offset = null, $p_limit = null, array $p_order_by = null)
    {
        global $g_ado_db;
        
        if (empty($p_offset)) {
            $p_offset = 0;   
        }
        
        if (empty($p_limit)) {
            $p_limit = 20;   
        }
        
        $query = InterviewItem::GetQuery($p_fk_interview_id, $p_order_by);
        
        $res = $g_ado_db->SelectLimit($query, $p_limit, $p_offset);        
        $interviewItems = array();
        
        while ($row = $res->FetchRow()) { 
            $tmp_interviewItem = new InterviewItem($row['fk_interview_id'], $row['item_id']);
            $interviewItems[] = $tmp_interviewItem;  
        }
        
        return $interviewItems;
    }

    
    /**
     * Get the count for available interviews
     *
     * @return int
     */
    public static function countInterviewItemss()
    {
        global $g_ado_db;;
        
        $query   = InterviewItem::getQuery(); 
        $res     = $g_ado_db->Execute($query);
        
        return $res->RecordCount();  
    }
    
    /**
     * Get the item id
     *
     * @return int
     */
    public function getId()
    {
        return $this->getProperty('item_id');   
    }
         
    /**
     * Get the interview id
     *
     * @return int
     */
    public function getInterviewId()
    {
        return $this->getProperty('interview_id');   
    }
    
    /**
     * Get the name/title
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->getProperty('question');   
    }
    
    /**
     * Get the name/title
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->getProperty('answer');   
    }
    
    /**
     * Get the language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        return $this->getProperty('fk_language_id');   
    }
    
    /**
     * Get the english language name
     *
     * @return string
     */
    public function getLanguageName()
    {
        $language = new Language($this->m_data['fk_language_id']);
        
        return $language->getName(); 
    }
    
    
    public function getQuestionForm($p_target='index.php', $p_add_hidden_vars=array(), $p_html=false)
    {
        require_once 'HTML/QuickForm.php';
              
        $mask = InterviewItem::getQuestionFormMask();

        if (is_array($p_add_hidden_vars)) {
            foreach ($p_add_hidden_vars as $k => $v) {       
                $mask[] = array(
                    'element'   => $k,
                    'type'      => 'hidden',
                    'constant'  => $v
                );   
            } 
        }
        
        $form =& new html_QuickForm('blog', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form(&$form, &$mask); 
        
        if ($p_html) {
            return $form->toHTML();    
        } else {
            require_once 'HTML/QuickForm/Renderer/Array.php';
            
            $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            
            return $renderer->toArray();
        } 
    }
    
    private function getQuestionFormMask()
    {
        $data = $this->m_data;
        $exists =  $this->exists();
        
        $mask = array(
            'action'    => array(
                'element'   => 'action',
                'type'      => 'hidden',
                'constant'  => $exists ? 'question_edit' : 'question_create'
            ),
            'fk_interview_id'  => array(
                    'element'   => 'fk_interview_id',
                    'type'      => 'hidden',
                    'constant'  => $data['fk_interview_id']
            ),
            'item_id'  => array(
                    'element'   => 'item_id',
                    'type'      => 'hidden',
                    'constant'  => $data['item_id']
            ),
            'fk_questioneer_user_id' => array(
                'element'   => 'interviewitem[fk_questioneer_user_id]',
                'type'      => 'select',
                'label'     => 'fk_questioneer_user_id',
                'default'   => $data['fk_questioneer_user_id'],
                'options'   => array(10 => 'user1', 20 => 'user2'),
                'required'  => true,
            ),
            'question' => array(
                'element'   => 'interviewitem[question]',
                'type'      => 'textarea',
                'label'     => 'question',
                'default'   => $data['question'],
                'attributes'=> array('cols' => 40, 'rows' => 5),
                'required'  => true,
            ),

            'reset'     => array(
                'element'   => 'reset',
                'type'      => 'reset',
                'label'     => 'Zurücksetzen',
                'groupit'   => true
            ),
            'xsubmit'     => array(
                'element'   => 'xsubmit',
                'type'      => 'button',
                'label'     => 'Abschicken',
                'attributes'=> array('onclick' => 'this.form.submit()'),
                'groupit'   => true
            ),
            'cancel'     => array(
                'element'   => 'cancel',
                'type'      => 'button',
                'label'     => 'Cancel',
                'attributes' => array('onClick' => 'history.back()'),
                'groupit'   => true
            ), 
            'buttons'   => array(
                'group'     => array('xsubmit', 'reset')
            )       
        );
        
        return $mask;   
    }
    
    public function storeQuestion()
    {
        require_once 'HTML/QuickForm.php';
              
        $mask = InterviewItem::getQuestionFormMask($p_owner, $p_admin);        
        $form = new html_QuickForm('blog', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form(&$form, &$mask);   
           
        if ($form->validate()) {
            $submit = $form->getSubmitValues();
            $data = $submit['interviewitem']; 

            if ($this->exists()) {
                // edit existing interview    
                $this->setProperty('fk_questioneer_user_id', $data['fk_questioneer_user_id']);
                $this->setProperty('question', $data['question']);
                
            } else {
                // create new interview
                $this->create($date['fk_questioneer_user_id'], $data['question']);   
                
            }
            
        }
    }
    
    public function getAnswerForm($p_target='index.php', $p_add_hidden_vars=array(), $p_owner=false, $p_admin=false, $p_html=false)
    {
        require_once 'HTML/QuickForm.php';
              
        $mask = InterviewItem::getAnswerFormMask();

        if (is_array($p_add_hidden_vars)) {
            foreach ($p_add_hidden_vars as $k => $v) {       
                $mask[] = array(
                    'element'   => $k,
                    'type'      => 'hidden',
                    'constant'  => $v
                );   
            } 
        }
        
        $form =& new html_QuickForm('blog', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form(&$form, &$mask); 
        
        if ($p_html) {
            return $form->toHTML();    
        } else {
            require_once 'HTML/QuickForm/Renderer/Array.php';
            
            $renderer =& new HTML_QuickForm_Renderer_Array(true, true);
            $form->accept($renderer);
            
            return $renderer->toArray();
        } 
    }
    
    private function getAnswerFormMask()
    {
        $data = $this->m_data;
        if ($this->getProperty('Status' != 'new')) {
            $exists = true;       
        }
        
        $mask = array(
            'action'    => array(
                'element'   => 'action',
                'type'      => 'hidden',
                'constant'  => $exists ? 'answer_edit' : 'answer_create'
            ),
            'fk_interview_id'  => array(
                    'element'   => 'fk_interview_id',
                    'type'      => 'hidden',
                    'constant'  => $data['fk_interview_id']
            ),
            'item_id'  => array(
                    'element'   => 'item_id',
                    'type'      => 'hidden',
                    'constant'  => $data['item_id']
            ),
            'answer' => array(
                'element'   => 'interviewitem[answer]',
                'type'      => 'textarea',
                'label'     => 'answer',
                'default'   => $data['answer'],
                'attributes'=> array('cols' => 40, 'rows' => 5),
                'required'  => true,
            ),

            'reset'     => array(
                'element'   => 'reset',
                'type'      => 'reset',
                'label'     => 'Zurücksetzen',
                'groupit'   => true
            ),
            'xsubmit'     => array(
                'element'   => 'xsubmit',
                'type'      => 'button',
                'label'     => 'Abschicken',
                'attributes'=> array('onclick' => 'this.form.submit()'),
                'groupit'   => true
            ),
            'cancel'     => array(
                'element'   => 'cancel',
                'type'      => 'button',
                'label'     => 'Cancel',
                'attributes' => array('onClick' => 'history.back()'),
                'groupit'   => true
            ), 
            'buttons'   => array(
                'group'     => array('xsubmit', 'reset')
            )       
        );
        
        return $mask;   
    }
    
    public function storeAnswer()
    {
        require_once 'HTML/QuickForm.php';
              
        $mask = InterviewItem::getAnswerFormMask($p_owner, $p_admin);        
        $form = new html_QuickForm('blog', 'post', $p_target, null, null, true);
        FormProcessor::parseArr2Form(&$form, &$mask);   
           
        if ($form->validate()) {
            $submit = $form->getSubmitValues();
            $data = $submit['interviewitem']; 
    
            $this->setProperty('answer', $data['answer']);
            $this->setProperty('status', 'public');

            
        }
    }

    /////////////////// Special template engine methods below here /////////////////////////////
    
    /**
     * Gets an issue list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparisonOperation objects
     * @param string item
     *    An indentifier which assignment should be used (publication/issue/section/article)
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     *
     * @return array $issuesList
     *    An array of Issue objects
     */
    public static function GetList($p_parameters, $p_item = null, $p_order = null,
                                   $p_start = 0, $p_limit = 0, &$p_count)
    {
        global $g_ado_db;
        
        if (!is_array($p_parameters)) {
            return null;
        }
        
        $selectClauseObj = new SQLSelectClause();

        // sets the where conditions
        foreach ($p_parameters as $param) {
            $comparisonOperation = self::ProcessListParameters($param);
            if (empty($comparisonOperation)) {
                continue;
            }
            
            if (strpos($comparisonOperation['left'], 'interview_id') !== false) {
                $interview_id = $comparisonOperation['right'];
            } else {
                $whereCondition = $comparisonOperation['left'] . ' '
                . $comparisonOperation['symbol'] . " '"
                . $comparisonOperation['right'] . "' ";
                $selectClauseObj->addWhere($whereCondition);
            }
        }
        
        // sets the columns to be fetched
        $tmpInterviewItem = new InterviewItem();
		$columnNames = $tmpInterviewItem->getColumnNames(true);
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }

        // sets the main table for the query
        $mainTblName = $tmpInterviewItem->getDbTableName();
        $selectClauseObj->setTable($mainTblName);
        unset($tmpInterviewItem);
        
        // set constraints which ever have to care of
        $selectClauseObj->addWhere("$mainTblName.fk_interview_id = $interview_id");
        #$selectClauseObj->addWhere("$mainTblName.is_online = 1");

       
        if (is_array($p_order)) {
            $order = InterviewItem::ProcessListOrder($p_order);
            // sets the order condition if any
            foreach ($order as $orderField=>$orderDirection) {
                $selectClauseObj->addOrderBy($orderField . ' ' . $orderDirection);
            }
        }
       
        $sqlQuery = $selectClauseObj->buildQuery();
        
        // count all available results
        $countRes = $g_ado_db->Execute($sqlQuery);
        $p_count = $countRes->recordCount();
        
        //get tlimited rows
        $interviewItemRes = $g_ado_db->SelectLimit($sqlQuery, $p_limit, $p_start);
        
        // builds the array of interview objects
        $interviewItemsList = array();
        while ($interviewItem = $interviewItemRes->FetchRow()) {
            $interviewItemObj = new InterviewItem($interviewItem['interview_id'], $interviewItem['item_id']);
            if ($interviewItemObj->exists()) {
                $interviewItemsList[] = $interviewItemObj;
            }
        }

        return $interviewItemsList;
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

        $comparisonOperation['left'] = InterviewItemsList::$s_parameters[strtolower($p_param->getLeftOperand())]['field'];

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
                    $dbField = 'interview_nr';
                    break;
                case 'byname':
                    $dbField = 'title';
                    break;
                case 'bybegin':
                    $dbField = 'date_begin';
                    break;
                case 'byend':
                    $dbField = 'date_end';
                    break;
                case 'byvotes':
                    $dbField = 'nr_of_votes';
                    break;
            }
            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
            }
            $order[$dbField] = $direction;
        }
        return $order;
    }
} // class InterviewItem

?>
