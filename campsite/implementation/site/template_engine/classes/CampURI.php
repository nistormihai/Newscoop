<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

define('URLTYPE_TEMPLATE_PATH', 1);
define('URLTYPE_SHORT_NAMES', 2);

/**
 * Class CampURI
 */
abstract class CampURI {
    /**
     * The URI type
     * It can be either:
     * Template Path = 1
     * Short Names = 2
     *
     * @var integer
     */
    protected $m_type = null;

    /**
     * The URI value
     *
     * @var string
     */
    protected $m_uri = null;

    /**
     * The URI parts
     *
     * @var array
     */
    protected $m_parts = array('scheme', 'user', 'password', 'host', 'port', 'path', 'query', 'fragment');

    /**
     * @var string
     */
    protected $m_scheme = null;

    /**
     * @var string
     */
    protected $m_host = null;

    /**
     * @var int
     */
    protected $m_port = null;

    /**
     * @var string
     */
    protected $m_user = null;

    /**
     * @var string
     */
    protected $m_password = null;

    /**
     * @var string
     */
    protected $m_path = null;

    /**
     * @var string
     */
    protected $m_query = null;

    /**
     * @var string
     */
    protected $m_fragment = null;

    /**
     * @var array
     */
    private $m_queryArray = array();

    /**
     * @var string
     */
    protected $m_buildPath = null;

    /**
     * @var string
     */
    protected $m_buildQuery = null;

    /**
     * @var array
     */
    protected $m_buildQueryArray = array();

    /**
     * Language object
     *
     * @var MetaLanguage
     */
    protected $m_language = null;

    /**
     * Publication object
     *
     * @var MetaPublication
     */
    protected $m_publication = null;

    /**
     * Issue object
     *
     * @var MetaIssue
     */
    protected $m_issue = null;

    /**
     * Section object
     *
     * @var MetaSection
     */
    protected $m_section = null;

    /**
     * Article object
     *
     * @var MetaArticle
     */
    protected $m_article = null;

    /**
     * Template object
     *
     * @var MetaTemplate
     */
    protected $m_template = null;

    /**
     *
     * @var bool
     */
    private $m_validCache = false;

    /**
     * Whether the URI is valid or not
     *
     * @var boolean
     */
    protected $m_validURI = false;
    
    /**
     * The list of parameters used in preview mode
     * @var array
     */
    static protected $m_previewParameters = array('LoginUserId',
    'LoginUserKey', 'AdminAccess', 'previewLang', 'preview');


    /**
     * Class constructor
     *
     * @param string $p_uri
     *    The full URI string
     */
    public function __construct($p_uri = 'SELF')
    {
        if (isset($p_uri) && $p_uri != 'SELF') {
            $uriString = $p_uri;
        } else {
            // ... otherwise we build the uri from the server itself.
            //
            // checks whether the site is being queried through SSL
            if (isset($_SERVER['HTTPS'])
            && !empty($_SERVER['HTTPS'])
            && (strtolower($_SERVER['HTTPS']) != 'off')) {
                $scheme = 'https://';
            } else {
                $scheme = 'http://';
            }

            // this works at least for apache, some research is needed
            // in order to support other web servers.
            if (!empty($_SERVER['PHP_SELF'])) {
            	$uriString = $scheme . $_SERVER['HTTP_HOST'];
            }
            if (isset($_SERVER['REQUEST_URI'])) {
                $uriString .= $_SERVER['REQUEST_URI'];
            }

            // some cleaning directives
            $uriString = urldecode($uriString);
            $uriString = str_replace('"', '&quot;', $uriString);
            $uriString = str_replace('<', '&lt;', $uriString);
            $uriString = str_replace('>', '&gt;', $uriString);
            $uriString = preg_replace('/eval\((.*)\)/', '', $uriString);
            $uriString = preg_replace('/[\\\"\\\'][\\s]*javascript:(.*)[\\\"\\\']/', '""', $uriString);
        }

        $this->parse($uriString);
        $this->m_queryArray = array_merge($this->m_queryArray, CampRequest::GetInput('POST'));
    } // fn __construct


    /**
     * Returns true if the given parameter is restricted and can not
     * be set from outside the URL object.
     *
     * @param string $p_parameterName
     * @return bool
     */
    abstract public function isRestrictedParameter($p_parameterName);


    /**
     * Parses the given URI.
     *
     * @param string $p_uri
     *      The URI string
     *
     * @return boolean
     *    true on success, false on failure
     */
    private function parse($p_uri)
    {
        $success = false;
        if (empty($p_uri)) {
            return $success;
        }

        $this->m_uri = $p_uri;
        $p_uri = urldecode($p_uri);

        if ($parts = parse_url($p_uri)) {
            $success = true;
        }

        // sets the value for every URI part
        foreach ($this->m_parts as $part) {
            $property = 'm_'.$part;
            if (property_exists($this, $property)) {
                $this->$property = (isset($parts[$part])) ? $parts[$part] : null;
            }
        }

        // populates the query array
        if (isset($parts['query']) && CampRequest::GetMethod() != 'POST') {
            parse_str($parts['query'], $this->m_queryArray);
        }

        return $success;
    } // fn parse


    /**
     * Builds a URI string from the given parts.
     *
     * @param array $p_parts
     *      The array of URI parts
     *
     * @return string $uriString
     *      The rendered URI
     */
    protected function render(array $p_parts = array())
    {
        if (empty($p_parts)) {
            $p_parts = $this->m_parts;
        }
        if (!$this->isValidCache()) {
            $this->m_path = $this->getURIPath();
            $this->m_query = $this->getQuery();
        }
        $uriString = '';
        foreach ($p_parts as $part) {
            $property = 'm_'.$part;
            if (!empty($this->$property)) {
                $uriString .= ($part == 'scheme') ? $this->$property.'://' : '';
                $uriString .= ($part == 'user') ? $this->$property : '';
                $uriString .= ($part == 'password') ? ':'.$this->$property.'@' : '';
                $uriString .= ($part == 'host') ? $this->$property : '';
                $uriString .= ($part == 'port') ? ':'.$this->$property : '';
                $uriString .= ($part == 'path') ? $this->$property : '';
                $uriString .= ($part == 'query') ? '?'.$this->$property : '';
                $uriString .= ($part == 'fragment') ? '#'.$this->$property : '';
            }
        }

        return $uriString;
    } // fn render


    /**
     * Gets the URL type.
     *
     * @return integer
     */
    public function getURLType()
    {
        return $this->m_type;
    } // fn getURLType


    /**
     * Gets the URL from the object attributes.
     *
     * @return string $url
     *      The full URL
     */
    public function getURL()
    {
        $url = $this->render(array(
                                    'scheme',
                                    'user',
                                    'password',
                                    'host',
                                    'port',
                                    'path',
                                    'query',
                                    'fragment'
                                    )
                                    );
                                    return $url;
    } // fn getURL


    /**
     * Gets the requested URI.
     *
     * @return string
     *      The requested URI string
     */
    public function getRequestURI()
    {
        if (empty($this->m_query)) {
            return $this->m_path;
        }

        return $this->render(array('path', 'query'));
    } // fn getRequestURI


    /**
     * Gets the URI base, it is the scheme, host and (if exists) port.
     *
     * @return string $base
     *      The URI base
     */
    public function getBase()
    {
        $base = $this->m_scheme.'://'.$this->m_host;
        if (is_numeric($this->m_port)) {
            $base .= ':'.$this->m_port;
        }

        return $base;
    } // fn getBase


    /**
     * Gets the base plus the path from the current URI.
     *
     * @return string
     *    The URI base path
     */
    public function getBasePath()
    {
        return $this->render(array('scheme','host','port','path'));
    } // fn getBasePath


    /**
     * Gets the query part from the current URI.
     *
     * @return string $m_query
     *      The query part
     */
    public function getQuery()
    {
        if (!$this->isValidCache()) {
            $this->m_query = CampURI::QueryArrayToString($this->getQueryArray());
        }
        return $this->m_query;
    } // fn getQuery


    /**
     * Gets the given variable from the URI query.
     *
     * @param string $p_varName
     *      The variable name
     *
     * @return string
     *      null on failure, otherwise the variable value
     */
    public function getQueryVar($p_varName)
    {
        $queryArray = $this->getQueryArray();
        if (!isset($queryArray[$p_varName])) {
            return null;
        }

        return $queryArray[$p_varName];
    } // fn getQueryVar


    /**
     * Gets the array containing the query variables.
     *
     * @return array $m_queryArray
     *      The array of query vars
     */
    public function getQueryArray(array $p_keepParameters = array(),
    array $p_removeParameters = array())
    {
    	$queryArray = $this->m_queryArray;
    	if (count($p_removeParameters) > 0) {
    		$removeKeys = array_combine($p_removeParameters,
    		array_fill(0, count($p_removeParameters, null)));
    		$queryArray = array_diff_key($queryArray, $removeKeys);
    	}
    	if (count($p_keepParameters)) {
    		$keepKeys = array_combine($p_keepParameters,
    		array_fill(0, count($p_keepParameters), null));
    		$queryArray = array_intersect_key($queryArray, $keepKeys);
    	}
        return $queryArray;
    } // fn getQueryArray


    /**
     * Gets the scheme part from the current URI.
     *
     * @return string $m_scheme
     *      The scheme value
     */
    public function getScheme()
    {
        return $this->m_scheme;
    } // fn getScheme


    /**
     * Gets the host part from the current URI.
     *
     * @return string $m_host
     *      The host value
     */
    public function getHost()
    {
        return $this->m_host;
    } // fn getHost


    /**
     * Gets the port part from the current URI.
     *
     * @return int $m_port
     *      The port value
     */
    public function getPort()
    {
        return $this->m_port;
    } // fn getPort


    /**
     * Returns the user part from the current URI.
     *
     * @return string
     *      The username value
     */
    public function getUser()
    {
        return $this->m_user;
    } // fn getUser


    /**
     * Gets the password part from the current URI.
     *
     * @return string $m_password
     *      The password value
     */
    public function getPassword()
    {
        return $this->m_password;
    } // fn getPassword


    /**
     * Gets the path part from the current URI.
     *
     * @return string $m_path
     *      The path value
     */
    public function getPath()
    {
        return $this->m_path;
    } // fn getPath


    /**
     * Gets the fragment part from the current URI.
     *
     * @return string $m_fragment
     *      The fragment value
     */
    public function getFragment()
    {
        return $this->m_fragment;
    } // fn getFragment


    /**
     * Returns the URI string based on given URL parameter.
     *
     * @param string $p_param
     *      The URL parameter
     * @param boolean $p_preview
     *      If true, will keep the preview parameters in the URL
     *
     * @return string
     *      The URI string requested
     */
    public function getURI($p_param = null, $p_preview = false)
    {
        if (!$this->m_validURI) {
            return null;
        }

        $this->m_buildPath = null;
        $this->m_buildQuery = null;
        $this->m_buildQueryArray = $this->getQueryArray();

        $params = preg_split("/[\s]+/", $p_param);
        $this->buildURI($params, $p_preview);
        if (!empty($this->m_buildQuery)) {
            return $this->m_buildPath . '?' . $this->m_buildQuery;
        }

        return $this->m_buildPath;
    } // fn getURI


    /**
     * Returns the URI path based on given URL parameter.
     *
     * @param string $p_param
     *      The URL parameter
     * @param boolean $p_preview
     *      If true, will keep the preview parameters in the URL
     *
     * @return string
     *      The URI path string requested
     */
    public function getURIPath($p_param = null, $p_preview = false)
    {
        if (!$this->m_validURI) {
            return null;
        }

        $this->m_buildPath = null;
        $this->m_buildQuery = null;
        $this->m_buildQueryArray = $this->getQueryArray();

        $params = preg_split("/[\s]+/", $p_param);
        $this->buildURI($params, $p_preview);
        return $this->m_buildPath;
    } // fn getURIPath


    /**
     * Returns the URI query parameters based on given URL parameter.
     *
     * @param string $p_param
     * @param boolean $p_preview
     *      If true, will keep the preview parameters in the URL
     *
     * @return string
     *      The URI query string requested
     */
    public function getURLParameters($p_param = null, $p_preview = false)
    {
        if (!$this->m_validURI) {
            return null;
        }

        $this->m_buildPath = null;
        $this->m_buildQuery = null;
        $this->m_buildQueryArray = $this->getQueryArray();

        $params = preg_split("/[\s]+/", $p_param);
        $this->buildURI($params, $p_preview);
        return $this->m_buildQuery;
    } // fn getURLParameters


    /**
     * Sets the URL type.
     *
     * @param integer $p_type
     *      The URL type number
     *
     * @return void
     */
    protected function setURLType($p_type)
    {
        $this->m_type = (int)$p_type;
    } // fn setURLType


    /**
     * Adds the given parameters to the query array
     */
    protected function addToQuery(&$p_query, array $p_parameters) {
        if (count($p_parameters) == 0) {
            return;
        }
        if (!empty($p_query)) {
            $this->m_query .= '&';
        }
        $p_query .= CampURI::QueryArrayToString($p_parameters);
    }


    /**
     * Gets the query part from the current URI.
     *
     * @return string $m_query
     *      The query value
     *
     * @return void
     */
    public function setQuery($p_query)
    {
        $this->m_query = $p_query;
        parse_str($p_query, $this->m_queryArray);
    } // fn setQuery


    /**
     * Sets the given URI query variable.
     *
     * @param string $p_varName
     *      The name of the URI query variable
     * @param string $p_value
     *      The value for the variable
     *
     * @return void
     */
    public function setQueryVar($p_varName, $p_value = null)
    {
        if (is_null($p_value)) {
            unset($this->m_queryArray[$p_varName]);
        } else {
            $this->m_queryArray[$p_varName] = $p_value;
        }
        $this->validateCache(false);
    } // fn setQueryVar


    /**
     * Sets the URI scheme.
     *
     * @param string $p_scheme
     *      The scheme value
     *
     * @return void
     */
    public function setScheme($p_scheme)
    {
        $this->m_scheme = $p_scheme;
    } // fn setScheme


    /**
     * Sets the URI host.
     *
     * @param string $p_host
     *      The host name
     *
     * @return void
     */
    public function setHost($p_host)
    {
        $this->m_host = $p_host;
    } // fn setHost


    /**
     * Sets the URI port.
     *
     * @param int $p_port
     *      The port number
     *
     * @return void
     */
    public function setPort($p_port)
    {
        $this->m_port = (int)$p_port;
    } // fn setPort


    /**
     * Sets the URI user part.
     *
     * @param string $p_user
     *      The user name
     *
     * @return void
     */
    public function setUser($p_user)
    {
        $this->m_user = $p_user;
    } // fn setUser


    /**
     * Sets the URI password part.
     *
     * @param string @p_password
     *      The user password
     *
     * @return void
     */
    public function setPassword($p_password)
    {
        $this->m_password = $p_password;
    } // fn setPassword


    /**
     * Sets the URI path.
     *
     * @param string $p_path
     *      The path
     *
     * @return void
     */
    public function setPath($p_path)
    {
        $this->m_path = $p_path;
    } // fn setPath


    /**
     * Sets the URI fragment.
     *
     * @param string $p_fragment
     *      The fragment part
     *
     * @return void
     */
    public function setFragment($p_fragment)
    {
        $this->m_fragment = $p_fragment;
    } // fn setFragment


    /**
     * Sets an object property
     *
     * @param string $p_property
     * @return bool
     */
    public function __get($p_property)
    {
        $p_property = strtolower($p_property);
        if (!property_exists($this, "m_$p_property")) {
            return null;
        }
        $memberName = "m_$p_property";
        return $this->$memberName;
    } // fn __get


    /**
     * Sets an object property
     *
     * @param string $p_property
     * @param mixed $p_value
     * @return bool
     */
    public function __set($p_property, $p_value)
    {
        $p_property = strtolower($p_property);
        $searchResult = array_search($p_property, $this->m_parts);
        if ($searchResult !== false) {
            return false;
        }
        if (!property_exists($this, "m_$p_property")) {
            return false;
        }
        if (!is_a($p_value, 'Meta'.$p_property) && !is_null($p_value)) {
            return false;
        }
        $memberName = "m_$p_property";
        $this->$memberName = $p_value;
        if ($p_property == 'publication') {
        	$this->m_host = $this->m_publication->site;
        }
        $this->validateCache(false);
        return true;
    } // fn __set


    /**
     * Sets the cache validation for URI rendering
     *
     * @param bool $p_valid
     */
    protected function validateCache($p_valid)
    {
        $this->m_validCache = $p_valid;
    } // fn validateCache


    /**
     * Returns the cache valid state
     *
     * @return bool
     */
    protected function isValidCache()
    {
        return false;
        // A proper cache scheme was not implemented yet.
        //        return $this->m_validCache;
    } // fn isValidCache


    /**
     * Returns whether the site is running over SSL or not.
     *
     * @return boolean
     *    true on success, false on failure
     */
    public function isSSL()
    {
        return ($this->m_scheme == 'https') ? true : false;
    } // fn isSSL


    /**
     * Sets the URI path and query values based on given parameters.
     *
     * @param array $p_params
     *      An array of valid URL parameters
     * @param boolean $p_preview
     *      If true, will keep the preview parameters in the URL
     *
     * @return void
     */
    protected function buildURI(array &$p_params = array(), $p_preview = false) {
        if ($this->isValidCache()) {
            return;
        }

        if (count($p_params) == 0) {
            return;
        }
        $parameter = strtolower(array_shift($p_params));

        switch ($parameter) {
            case 'root_level':
                $this->m_buildPath = '/';
                if ($p_preview) {
                    $this->m_buildQueryArray = $this->getQueryArray(CampURI::$m_previewParameters);
                } else {
                    $this->m_buildQueryArray = array();
                }
                $p_params = array();
                break;
            case 'articleattachment':
                $context = CampTemplate::singleton()->context();
                $attachment = new Attachment($context->attachment->identifier);
                $this->m_buildPath = '/attachment/'.basename($attachment->getStorageLocation());
                $this->m_buildQueryArray = array();
                $p_params = array();
                break;
            case 'audioattachment':
                $context = CampTemplate::singleton()->context();
                $this->m_buildPath = '/audioclip/';
                $this->m_buildQueryArray = array();
                $p_params = array();
                break;
            case 'articlecomment':
                $context = CampTemplate::singleton()->context();
                if ($context->comment->defined) {
                    $this->m_buildQueryArray['acid'] = $context->comment->identifier;
                }
                break;
            case 'image':
                $option = isset($p_params[0]) ? array_shift($p_params) : null;
                $context = CampTemplate::singleton()->context();
                if (!is_null($option)) {
                    $oldImage = $context->image;
                    $articleImage = new ArticleImage($context->article->number, null, $option);
                    $context->image = new MetaImage($articleImage->getImageId());
                }
                if ($context->image->article_index !== null) {
                    $this->m_buildPath = '/get_img';
                    $this->m_buildQueryArray = array();
                    $this->m_buildQueryArray['NrImage'] = $context->image->article_index;
                    $this->m_buildQueryArray['NrArticle'] = $context->article->number;
                    if(isset($p_params[0]))
                        $this->m_buildQueryArray['ImageRatio'] = $p_params[0];

                }
                if (!is_null($option)) {
                    $context->image = $oldImage;
                }
                $p_params = array();
                break;
            case 'previous_subtitle':
            case 'next_subtitle':
            case 'all_subtitles':
                $option = isset($p_params[0]) ? array_shift($p_params) : null;
                $article = CampTemplate::singleton()->context()->article;
                $subtitleNo = $article->current_subtitle_no($option);
                if (!$article->defined || (!is_null($subtitleNo) && !is_numeric($subtitleNo))) {
                    return;
                }
                $fieldObj = $article->$option;
                if (($parameter == 'previous_subtitle' && !$fieldObj->has_previous_subtitles)
                || ($parameter == 'next_subtitle' && !$fieldObj->has_next_subtitles)) {
                    return;
                }
                $subtitleURLId = $article->subtitle_url_id($option);
                if ($parameter == 'all_subtitles') {
                    $newSubtitleNo = 'all';
                } else {
                    $newSubtitleNo = $subtitleNo + ($parameter == 'previous_subtitle' ? -1 : 1);
                }
                $this->m_buildQueryArray[$subtitleURLId] = $newSubtitleNo;
                break;
            case 'previous_items':
            case 'next_items':
                $context = CampTemplate::singleton()->context();
                if ($context->current_list == null) {
                    return;
                }
                $listId = $context->current_list->id;
                $this->m_buildQueryArray[$listId] = ($parameter == 'previous_items' ?
                $context->current_list->previous_start : $context->current_list->next_start);
                break;
            case 'reset_issue_list':
                $context = CampTemplate::singleton()->context();
                $listIdPrefix = $context->list_id_prefix('IssuesList');
                $this->resetList($listIdPrefix);
                break;
            case 'reset_section_list':
                $context = CampTemplate::singleton()->context();
                $listIdPrefix = $context->list_id_prefix('SectionsList');
                $this->resetList($listIdPrefix);
                break;
            case 'reset_article_list':
                $context = CampTemplate::singleton()->context();
                $listIdPrefix = $context->list_id_prefix('ArticlesList');
                $this->resetList($listIdPrefix);
                break;
            case 'reset_searchresult_list':
                $context = CampTemplate::singleton()->context();
                $listIdPrefix = $context->list_id_prefix('SearchResultsList');
                $this->resetList($listIdPrefix);
                break;
            case 'reset_subtitle_list':
                $context = CampTemplate::singleton()->context();
                $listIdPrefix = $context->list_id_prefix('SubtitlesList');
                $this->resetList($listIdPrefix);
                break;
            default:
                ;
        }
    }


    protected function resetList($listIdPrefix) {
        foreach ($this->getQueryArray() as $parameter=>$value) {
            if (strncasecmp($parameter, $listIdPrefix, strlen($listIdPrefix)) == 0) {
                unset($this->m_buildQueryArray[$parameter]);
            }
        }
    }


    protected function clearParams(array $parameters) {
        foreach ($parameters as $parameter) {
            $this->setQueryVar($parameter);
            unset($this->m_buildQueryArray[$parameter]);
        }
    }


    public static function GetPreviewParameters() {
    	return CampURI::$m_previewParameters;
    }


    /**
     * Builds a URI query string from the given query array.
     *
     * @param array $p_queryArray
     *      An array of query variables
     *
     * @return string $queryString
     *      The generated query string
     */
    protected static function QueryArrayToString(array $p_queryArray,
                                                 $p_HTMLEscape = true)
    {
        if (!is_array($p_queryArray) || sizeof($p_queryArray) < 1) {
            return false;
        }

        $queryString = '';
        $queryVars = array();
        foreach ($p_queryArray as $var => $value) {
        	if (is_array($value)) {
        		foreach ($value as $item) {
        			$queryVars[] = $var.'[]='.urlencode($item);
        		}
        	} else {
        		$queryVars[] = $var.'='.urlencode($value);
        	}
        }
        $separator = $p_HTMLEscape ? '&amp;' : '&';
        $queryString = implode($separator, $queryVars);

        return $queryString;
    } // fn QueryArrayToString

} // class CampURI

?>