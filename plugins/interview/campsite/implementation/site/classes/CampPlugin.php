<?php
define('PLUGINS_DIR', 'plugins');
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <devel@yellowsunshine.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


/**
 * Class CampPlugin
 */


class CampPlugin extends DatabaseObject {
    var $m_keyColumnNames = array('Name');

    var $m_dbTableName = 'Plugins';

    var $m_columnNames = array('Name', 'Version', 'Enabled');
    
    static protected $m_pluginInfos = null;

    public function CampPlugin($p_name = null, $p_version = null)
    {
        parent::DatabaseObject($this->m_columnNames);
        $this->m_data['Name'] = $p_name;

        if (!is_null($p_version)) {
            $this->m_data['Version'] = $p_version;
        }
        if (!is_null($p_name)) {
            $this->fetch();
        }
    } // constructor

    public function create($p_name, $p_version, $p_enabled = true)
    {
        // Create the record
        $values = array(
        'Name' => $p_name,
        'Version' => $p_version,
        'Enabled' => $p_enabled ? 1 : 0
        );


        $success = parent::create($values);
        if (!$success) {
            return false;
        }
    }

    public function getAll()
    {
        global $g_ado_db;

        $CampPlugin = new CampPlugin();
        $tblname = $CampPlugin->m_dbTableName;

        $query = "SELECT Name
                  FROM   $tblname";

        $res = $g_ado_db->execute($query);
        $plugins = array();

        while ($row = $res->FetchRow()) {
            $plugins[] = new CampPlugin($row['Name']);;
        }

        return $plugins;
    }

    public function getEnabled()
    {
        $plugins = array();

        foreach (self::getAll() as $CampPlugin) {
            if ($CampPlugin->isEnabled()) {
                $plugins[] = $CampPlugin;
            }
        }
        return $plugins;
    }

    public function getBasePath()
    {
        return PLUGINS_DIR.'/'.$this->getName();
    }

    public function getName()
    {
        return $this->getProperty('Name');
    }

    public function getVersion()
    {
        return $this->getProperty('Version');
    }


    public function isEnabled()
    {
        return $this->getProperty('Enabled') == 1 ? true : false;
    }

    public function isPluginEnabled($p_name, $p_version = null)
    {
        $plugin = new CampPlugin($p_name, $p_version);

        return $plugin->isEnabled();
    }

    public function enable()
    {
        $this->setProperty('Enabled', 1);

        $info = $this->getPluginInfo();
        if (function_exists($info['enable'])) {
            call_user_func($info['enable']);
        }
    }

    public function disable()
    {
        $this->setProperty('Enabled', 0);

        $info = $this->getPluginInfo();
        if (function_exists($info['disable'])) {
            call_user_func($info['disable']);
        }
    }


    public function getPluginInfos()
    {
        global $g_documentRoot;
        
        $directories = array(PLUGINS_DIR);

        if (!is_array(self::$m_pluginInfos)) {
            self::$m_pluginInfos = array();

            foreach ($directories as $dirName) {
                $dirName = "$g_documentRoot/$dirName";

                $handle=opendir($dirName);
                while ($entry = readdir($handle)) {
                    if ($entry != "." && $entry != ".." && $entry != '.svn' && is_dir("$dirName/$entry")) {
                        if (file_exists("$dirName/$entry/$entry.info.php")) {
                            include ("$dirName/$entry/$entry.info.php");
                            self::$m_pluginInfos[$entry] = $info;
                        }
                    }
                }
                closedir($handle);
            }
        }

        return self::$m_pluginInfos;
    }
    
    public function clearPluginInfos()
    {
        self::$m_pluginInfos = null;
    }

    public function getPluginInfo($p_plugin_name = '')
    {
        if (!empty($p_plugin_name)) {
            $name = $p_plugin_name;
        } elseif (isset($this) && is_a($this, 'CampPlugin')) {
            $name = $this->getName();
        } else {
            return false;
        }

        $infos = self::getPluginInfos();
        $info = $infos[$name];

        return $info;
    }

    public function initPlugins4TemplateEngine()
    {
        $context = CampTemplate::singleton()->context();
        $infos = self::getPluginInfos();

        foreach ($infos as $info) {
            if (CampPlugin::isPluginEnabled($info['name'])) {

                foreach ($info['template_engine']['objecttypes'] as $objecttype) {
                    $context->registerObjectType($objecttype);
                }
                foreach ($info['template_engine']['listobjects'] as $listobject) {
                    $context->registerListObject($listobject);
                }
                if (function_exists($info['template_engine']['init'])) {
                    call_user_func($info['template_engine']['init']);
                }
            }
        }
    }

    public function extendNoMenuScripts(&$p_no_menu_scripts)
    {
        foreach (self::getPluginInfos() as $info) {
            if (CampPlugin::isPluginEnabled($info['name'])) {
                $p_no_menu_scripts = array_merge($p_no_menu_scripts, $info['no_menu_scripts']);
            }
        }
    }

    public function createPluginMenu(&$p_menu_root, $p_iconTemplateStr)
    {
        global $ADMIN;
        global $g_user;

        $p_menu_root->addSplit();
        $menu_modules =& DynMenuItem::Create("Plugins", "",
        array("icon" => sprintf($p_iconTemplateStr, "plugin.png"), "id" => "plugins"));
        $p_menu_root->addItem($menu_modules);

        if ($g_user->hasPermission("plugin_manager")) {
            $menu_item =& DynMenuItem::Create(getGS('Manage Plugins'),
            "/$ADMIN/plugins/manage.php",
            array("icon" => sprintf($p_iconTemplateStr, "configure.png")));
            $menu_modules->addItem($menu_item);

        }

        $plugin_infos = self::getPluginInfos();

        foreach ($plugin_infos as $info) {
            if (CampPlugin::isPluginEnabled($info['name'])) {
                $menu_plugin = null;
                $parent_menu = false;

                if (isset($info['menu']['permission']) && $g_user->hasPermission($info['menu']['permission'])) {
                    $parent_menu = true;
                } elseif (is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($g_user->hasPermission($menu_info['permission'])) {
                            $parent_menu = true;
                        }
                    }
                }

                if ($parent_menu) {
                    $menu_plugin =& DynMenuItem::Create(getGS($info['menu']['label']),
                    is_null($info['menu']['path']) ? null : "/$ADMIN/".$info['menu']['path'],
                    array("icon" => sprintf($p_iconTemplateStr, $info['menu']['icon'])));
                }

                if (is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($g_user->hasPermission($menu_info['permission'])) {
                            $menu_item =& DynMenuItem::Create(getGS($menu_info['label']),
                            is_null($menu_info['path']) ? null : "/$ADMIN/".$menu_info['path'],
                            array("icon" => sprintf($p_iconTemplateStr, $menu_info['icon'])));
                            $menu_plugin->addItem($menu_item);
                        }
                    }
                }

                if (is_object($menu_plugin)) {
                    $menu_modules->addItem($menu_plugin);
                }
            }
        }
    }

    public function extractPackage($p_uploaded_package)
    {
        global $g_documentRoot;

        /*
        $rar_file = rar_open($p_uploaded_package) or die("Can't open Rar archive");
        $entries = rar_list($rar_file);

        foreach ($entries as $entry) {
        $log .= '<b>Filename:</b> ' . $entry->getName();
        #$log .= '  <b>Packed size:</b> ' . $entry->getPackedSize();
        #$log .= '  <b>Unpacked size:</b> ' . $entry->getUnpackedSize();

        if ($entry->extract($g_documentRoot.DIR_SEP.PLUGINS_DIR)) {
        $log .= '<font color="green">OK</font><p>';
        } else {
        $log .= '<font color="red">FAILED</font><p>';
        }
        }

        rar_close($rar_file);

        return $log;
        */

        /*
        // Open archives/test.tar
        require_once($g_documentRoot.'/include/archive/archive.php');
        $tar = new tar_file($p_uploaded_package);
        // Extract in memory
        $tar->set_options(array('overwrite' => 1, 'basedir' => $g_documentRoot.DIR_SEP.PLUGINS_DIR));
        // Extract contents of archive to disk
        $tar->extract_files();

        return array('error' => $tar->error, 'files' => $tar->files);
        */

        require_once('Archive/Tar.php');
        $tar = new Archive_Tar($p_uploaded_package);
        if (($file_list = $tar->ListContent()) != 0) {
            foreach ($file_list as $v) {
                $log .= sprintf("Name: %s  Size: %d   modtime: %s mode: %s<br>",
                $v['filename'],$v['size'],$v['mtime'],$v['mode']);
            }
        }
        $tar->extract($g_documentRoot.DIR_SEP.PLUGINS_DIR);
        return $log;
    }
}

?>
