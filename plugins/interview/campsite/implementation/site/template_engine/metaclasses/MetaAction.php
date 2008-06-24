<?php

define('ACTION_OK', 0);


class MetaAction
{
    /**
     * True if an action was set up; this member is set to false by the
     * base class. The specialized class must set it to true.
     *
     * @var bool
     */
    protected $m_defined = false;

    /**
     * Action properties
     *
     * @var array
     */
    protected $m_properties = null;

    /**
     * Stores the error data
     *
     * @var PEAR_Error
     */
    protected $m_error = null;

    /**
     * Stores the action type name
     *
     * @var string
     */
    protected $m_name = null;

    /**
     * Stores the available actions
     *
     * @var array
     */
    private static $m_availableActions = null;


    /**
     * Base initializations
     *
     * @param array $p_input
     */
    public function __construct($p_name = 'default_action')
    {
        $this->m_name = $p_name;
        if (!is_array($this->m_properties)) {
            $this->m_properties = array();
        }
    }


    /**
     * Performs the action; returns true on success, false on error.
     *
     * @param $p_context - the current context object
     * @return bool
     */
    public function takeAction(CampContext &$p_context)
    {
        return false;
    }


    /**
     * Returns the error code of the action. Returns 0 on success,
     * PEAR_Error object on error.
     *
     * @return mixed
     */
    protected function getError()
    {
        return $this->m_error;
    }


    /**
     * Factory method; creates an object specialized from MetaAction
     * based on the given input.
     *
     * @param array $p_input
     * @return MetaAction
     */
    public static function CreateAction(array $p_input)
    {
        if (count($p_input) == 0) {
            return new MetaAction();
        }

        $actions = MetaAction::ReadAvailableActions();

        foreach ($p_input as $parameter=>$value) {
            $parameter = strtolower($parameter);
            if (strncmp($parameter, 'f_', 2) != 0) {
                continue;
            }

            $actionLowerCase = strtolower(substr($parameter, 2));
            if (isset($actions[$actionLowerCase])) {
                require_once($actions[$actionLowerCase]['file']);
                $className = 'MetaAction'.$actions[$actionLowerCase]['name'];
                return new $className($p_input);
            }
        }

        return new MetaAction();
    }


    /**
     * Searches for classes that process actions. Returns an array of
     * action names.
     *
     * @return array
     */
    public static function ReadAvailableActions()
    {
        if (is_array(MetaAction::$m_availableActions)) {
            return MetaAction::$m_availableActions;
        }

        require_once('File/Find.php');

        $actions = array();
        
        $basePaths = array('.');
        foreach (CampPlugin::getEnabled() as $CampPlugin) {
            $basePaths[] = $CampPlugin->getBasePath();  
        }
        foreach ($basePaths as $basePath) {
            $directoryPath = $_SERVER['DOCUMENT_ROOT'].'/'.$basePath.'/template_engine/metaclasses';
            $actionIncludeFiles = File_Find::search('/^MetaAction[^.]*\.php$/',
            $directoryPath, 'perl', false);
    
            foreach ($actionIncludeFiles as $includeFile) {
                if (preg_match('/MetaAction([^.]+)\.php/', $includeFile, $matches) == 0
                || strtolower($matches[1]) == 'request') {
                    continue;
                }
    
                require_once($includeFile);
                $actionName = $matches[1];
                if (class_exists('MetaAction'.$actionName)) {
                    $actions[strtolower($actionName)] = array('name'=>$actionName,
                    'file'=>"$directoryPath/MetaAction$actionName.php");
                }
            }
        }    
        MetaAction::$m_availableActions = $actions;
        return MetaAction::$m_availableActions;
    }


    /**
     * Returns the value of the given property; throws
     * InvalidPropertyHandlerException if the property didn't exist.
     *
     * @param string $p_property
     * @return mixed
     */
    public function __get($p_property)
    {
        $p_property = MetaAction::TranslateProperty($p_property);

        if ($p_property == 'defined') {
            return $this->defined();
        }
        if ($p_property == 'is_error') {
            return PEAR::isError($this->m_error);
        }
        if ($p_property == 'error_code') {
            return PEAR::isError($this->m_error) ? $this->m_error->getCode() : null;
        }
        if ($p_property == 'error_message') {
            return PEAR::isError($this->m_error) ? $this->m_error->getMessage() : null;
        }
        if ($p_property == 'ok') {
            return $this->getError() === ACTION_OK;
        }
        if ($p_property == 'name') {
            return $this->m_name;
        }

        if (!is_array($this->m_properties)
        || !array_key_exists($p_property, $this->m_properties)) {
            $this->trigger_invalid_property_error($p_property);
        }
        if (method_exists($this, $this->m_properties[$p_property])) {
            $methodName = $this->m_properties[$p_property];
            return $this->$methodName();
        }
        return $this->m_properties[$p_property];
    } // fn __get


    /**
     * Throws InvalidFunctionException; action properties can not be modified.
     *
     * @param string $p_property
     * @param mixed $p_value
     */
    final public function __set($p_property, $p_value)
    {
        throw new InvalidFunctionException(get_class($this), '__set');
    } // fn __set


    /**
     * Throws InvalidFunctionException; action properties can not be modified.
     *
     * @param string $p_property
     */
    final public function __unset($p_property)
    {
        throw new InvalidFunctionException(get_class($this), '__unset');
    } // fn __set


    /**
     * Returns true if an action was set up.
     *
     * @return bool
     */
    final public function defined()
    {
        return $this->m_defined;
    } // fn defined


    /**
     * Returns true and sets the error to null if the input field was defined
     * and it's size respected the minimum size constraint. Returns false and
     * initializes the error to a pear error object with the given error message
     * and code if the input field was not defined or did not respect the minimum
     * size constraint. The minimum size constraint may be null.
     *
     * @param array $p_input
     * @param string $p_fieldName
     * @param int $p_minSize
     * @param mixed $p_error
     * @param string $p_errorMessage
     * @param mixed $p_errorCode
     * @return bool
     */
    public static function ValidateInput(array $p_input, $p_fieldName, $p_minSize = null,
    &$p_error, $p_errorMessage, $p_errorCode) {
        if (isset($p_input[$p_fieldName])
        && (is_null($p_minSize) || strlen($p_input[$p_fieldName]) >= $p_minSize)) {
            $p_error = null;
            return true;
        }
        $p_error = new PEAR_Error($p_errorMessage, $p_errorCode);
        return false;
    }


    /**
     * Converts the property name to the standard way of naming properties.
     *
     * @param string $p_property
     * @return string
     */
    public static function TranslateProperty($p_property)
    {
        return strtolower($p_property);
    }


    /**
     * Registers an error message in the CampTemplate singleton object.
     *
     * @param string $p_property
     * @param mixed $p_smarty
     */
    protected function trigger_invalid_property_error($p_property, $p_smarty = null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        . OF_OBJECT_STRING . ' ' . $this->m_name;
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    }

}

?>