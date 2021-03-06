<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <sebastian.goebel@web.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

class BlogEntriesList extends ListObject 
{   
    public static $s_parameters = array('identifier' => array('field' => 'entry_id', 'type' => 'integer'),
                                        'blog_id' => array('field' => 'fk_blog_id', 'type' => 'integer'),
                                        'language_id' => array('field' => 'fk_language_id', 'type' => 'integer'),
                                        'user_id' => array('field' => 'fk_user_id', 'type' => 'integer'),
                                        'date' => array('field' => 'date', 'type' => 'datetime'),
                                        'date_year' => array('field' => 'YEAR(date)', 'type' => 'integer'),
                                        'date_month' => array('field' => 'MONTH(date)', 'type' => 'integer'),
                                        'date_mday' => array('field' => 'DAYOFMONTH(date)', 'type' => 'integer'),
                                        'date_wday' => array('field' => 'DAYOFWEEK(date)', 'type' => 'integer'),
                                        'date' => array('field' => 'date', 'type' => 'datetime'),
                                        'title' => array('field' => 'title', 'type' => 'string'),
                                        'content' => array('field' => 'content', 'type' => 'string'),
                                        'mood' => array('field' => 'mood', 'type' => 'string'),
                                        'status' => array('field' => 'status', 'type' => 'string'),
                                        'admin_status' => array('field' => 'admin_status', 'type' => 'string'),
                                        'comments_online' => array('field' => 'comments_online', 'type' => 'integer'),
                                        'comments_offline' => array('field' => 'comments_offline', 'type' => 'integer'),
                                        'comments' => array('field' => 'comments_online + comments_offline', 'type' => 'integer'),
                                        'feature' => array('field' => 'feature', 'type' => 'string'),
                                        'matchalltopics'=>array('field' => null, 'type'=>'void'),
                                        'matchanytopic'=>array('field' => null, 'type'=>'void'),
                                        'topic'=>array('field' => null,'type'=>'topic'),
                               );
                                   
    private static $s_orderFields = array(
                                      'byidentifier',
                                      'byblog_id',
                                      'byuser_id',
                                      'bydate',
                                      'bydate_year',
                                      'bydate_month',
                                      'bydate_mday',
                                      'bydate_wday',
                                      'bytitle',
                                      'bycontent',
                                      'bymood',
                                      'bystatus',
                                      'byadmin_status',
                                      'bycomments_online',
                                      'bycomments_offline',
                                      'bycomments',
                                      'byfeature',
                                );
                                   
    /**
     * Creates the list of objects. Sets the parameter $p_hasNextElements to
     * true if this list is limited and elements still exist in the original
     * list (from which this was truncated) after the last element of this
     * list.
     *
     * @param int $p_start
     * @param int $p_limit
     * @param bool $p_hasNextElements
     * @return array
     */
    protected function CreateList($p_start = 0, $p_limit = 0, array $p_parameters, &$p_count)
    {
        if (!defined('PLUGIN_BLOG_ADMIN_MODE')) {
            $operator = new Operator('is', 'integer');
            $context = CampTemplate::singleton()->context();
            
            if (!$p_parameters['ignore_status']) {
                $comparisonOperation = new ComparisonOperation('status', $operator, 'online');
                $this->m_constraints[] = $comparisonOperation;
            }
            if (!$p_parameters['ignore_admin_status']) {
                $comparisonOperation = new ComparisonOperation('admin_status', $operator, 'online');
                $this->m_constraints[] = $comparisonOperation;
            }                
            if ($context->blog->defined && !$p_parameters['ignore_blog']) {
                $comparisonOperation = new ComparisonOperation('blog_id', $operator, $context->blog->identifier);
                $this->m_constraints[] = $comparisonOperation;
            }
            if ($context->language->defined && !$p_parameters['ignore_language']) {
        	    $comparisonOperation = new ComparisonOperation('language_id', $operator, $context->language->number);
                $this->m_constraints[] = $comparisonOperation;
    	    }
            if ($context->topic->defined && !$p_parameters['ignore_topic']) {
    	        $comparisonOperation = new ComparisonOperation('topic', $operator, $context->topic->identifier);
    	        $this->m_constraints[] = $comparisonOperation;
    	    }
        }
        $blogEntriesList = BlogEntry::GetList($this->m_constraints, $this->m_order, $p_start, $p_limit, $p_count);
        $metaBlogEntriesList = array();
        foreach ($blogEntriesList as $blogEntry) {
            $metaBlogEntriesList[] = new MetaBlogEntry($blogEntry->getId());
        }
        return $metaBlogEntriesList;
    }

	/**
	 * Processes list constraints passed in an array.
	 *
	 * @param array $p_constraints
	 * @return array
	 */
	protected function ProcessConstraints(array $p_constraints)
	{
	    $parameters = array();
	    $state = 1;
	    $attribute = null;
	    $operator = null;
	    $value = null;
	    foreach ($p_constraints as $index=>$word) {
	        switch ($state) {
	            case 1: // reading the parameter name
	                $attribute = strtolower($word);
	                if (!array_key_exists($attribute, self::$s_parameters)) {
	                    CampTemplate::singleton()->trigger_error("invalid attribute $word in statement list_blogentries, constraints parameter");
	                    return false;
	                }
	                if ($attribute == 'keyword') {
	                    $operator = new Operator('is', 'string');
	                    $state = 3;
	                } elseif ($attribute == 'matchalltopics' || $attribute == 'matchanytopic') {
	                    if ($attribute == 'matchalltopics') {
	                        $operator = new Operator('is', 'boolean');
	                        $comparisonOperation = new ComparisonOperation($attribute, $operator, 'true');
	                        $parameters[] = $comparisonOperation;
	                    }
	                    $state = 1;
	                } else {
                        $state = 2;
	                }
	                if ($attribute == 'onfrontpage' || $attribute == 'onsection') {
	                    if (($index + 1) < count($p_constraints)) {
	                        try {
	                            $operator = new Operator($p_constraints[$index+1], 'switch');
	                        }
	                        catch (InvalidOperatorException $e) {
        	                    $operator = new Operator('is', 'switch');
        	                    $comparisonOperation = new ComparisonOperation($attribute, $operator, 'on');
                	            $parameters[] = $comparisonOperation;
                	            $state = 1;
	                        }
	                    } else {
    	                    $operator = new Operator('is', 'switch');
                            $comparisonOperation = new ComparisonOperation($attribute, $operator, 'on');
                            $parameters[] = $comparisonOperation;
                            $state = 1;
	                    }
	                }
	                break;
	            case 2: // reading the operator
	                $type = self::$s_parameters[$attribute]['type'];
	                try {
	                    $operator = new Operator($word, $type);
	                }
	                catch (InvalidOperatorException $e) {
    	                CampTemplate::singleton()->trigger_error("invalid operator $word of parameter constraints.$attribute in statement list_blogentries");
	                    return false;
	                }
	                $state = 3;
	                break;
	            case 3: // reading the value to compare against
	                $type = self::$s_parameters[$attribute]['type'];
	                $metaClassName = 'Meta'.ucfirst($type);
	                try {
    	                $valueObj = new $metaClassName($word);
	                } catch (InvalidValueException $e) {
                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_blogentries");
	                    return false;
	                }
       	            if ($attribute == 'type') {
                        $word = trim($word);
       	                $blogType = new BlogType($word);
       	                if (!$blogType->exists()) {
	                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_blogentries");
	                        return false;
       	                }
       	                $value = $word;
       	            } elseif ($attribute == 'topic') {
       	                $topicObj = new Topic($word);
       	                if (!$topicObj->exists()) {
	                        CampTemplate::singleton()->trigger_error("invalid value $word of parameter constraints.$attribute in statement list_blogentries");
	                        return false;
       	                } else {
       	                    $value = $topicObj->getTopicId();
       	                }
       	            } elseif ($attribute == 'author') {
                        if (strtolower($word) == '__current') {
                        	$context = CampTemplate::singleton()->context();
                        	$value = $context->blog->author->name;
                        } else {
                        	$value = $word;
                        }
       	            } else {
       	                $value = $word;
       	            }
       	            $comparisonOperation = new ComparisonOperation($attribute, $operator, $value);
    	            $parameters[] = $comparisonOperation;
	                $state = 1;
	                break;
	        }
	    }
	    if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of constraints parameter in list_blogentries");
            return false;
	    }

		return $parameters;
	}


    /**
     * Processes order constraints passed in an array.
     *
     * @param array $p_order
     * @return array
     */
    protected function ProcessOrder(array $p_order)
    {
        if (!is_array($p_order)) {
            return null;
        }

        $order = array();
        $state = 1;
        foreach ($p_order as $word) {
            switch ($state) {
                case 1: // reading the order field
                    if (array_search(strtolower($word), BlogEntriesList::$s_orderFields) === false) {
                        CampTemplate::singleton()->trigger_error("invalid order field $word in list_entries, order parameter");
                    } else {
                        $orderField = $word;
                    }
                    $state = 2;
                    break;
                case 2: // reading the order direction
                    if (MetaOrder::IsValid($word)) {
                        $order[$orderField] = $word;
                    } else {
                        CampTemplate::singleton()->trigger_error("invalid order $word of attribute $orderField in list_blogentries, order parameter");
                    }
                    $state = 1;
                    break;
            }
        }
        if ($state != 1) {
            CampTemplate::singleton()->trigger_error("unexpected end of order parameter in list_blogentries");
        }

        return $order;
    }

    /**
     * Processes the input parameters passed in an array; drops the invalid
     * parameters and parameters with invalid values. Returns an array of
     * valid parameters.
     *
     * @param array $p_parameters
     * @return array
     */
    protected function ProcessParameters(array $p_parameters)
    {
        $parameters = array();
        foreach ($p_parameters as $parameter=>$value) {
            $parameter = strtolower($parameter);
            switch ($parameter) {
                case 'length':
                case 'columns':
                case 'name':
                case 'constraints':
                case 'order':
                case 'ignore_status':
      			case 'ignore_adminstatus':
      			case 'ignore_blog':
      			case 'ignore_language':
      			case 'ignore_topic':
                    if ($parameter == 'length' || $parameter == 'columns') {
                        $intValue = (int)$value;
                        if ("$intValue" != $value || $intValue < 0) {
                            CampTemplate::singleton()->trigger_error("invalid value $value of parameter $parameter in statement list_blogentries");
                        }
                        $parameters[$parameter] = (int)$value;
                    } else {
                        $parameters[$parameter] = $value;
                    }
                    break;
                default:
                    CampTemplate::singleton()->trigger_error("invalid parameter $parameter in list_blogentries", $p_smarty);
            }
        }
         
        return $parameters;
    }
    

    /**
     * Overloaded method call to give access to the list properties.
     *
     * @param string $p_element - the property name
     * @return mix - the property value
     */
    public function __get($p_property)
    {
        return parent::__get($p_property); 
    }
}

?>