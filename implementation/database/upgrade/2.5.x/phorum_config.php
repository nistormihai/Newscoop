<?php
/*

This file holds the configuration for letting Phorum connect to your
database server. A detailed description of the used configuration fields
can be found below.

If you are running your site with a hosting provider and you do not
know what to fill in here, please contact your hosting provider for
advice.

*/
require_once("database_conf.php");
require_once("install_conf.php");
if (!is_array($Campsite)) {
	echo "Invalid configuration file(s)";
	exit(1);
}

$db_name = $Campsite['DATABASE_NAME'];
$db_user = $Campsite['DATABASE_USER'];
$db_passwd = $Campsite['DATABASE_PASSWORD'];
$db_host = $Campsite['DATABASE_SERVER_ADDRESS'];

if(!defined('PHORUM')) return;

$PHORUM['DBCONFIG']=array(

    // Database connection.
    'type'          =>  'mysql',
    'name'          =>  $db_name,
    'server'        =>  $db_host,
    'user'          =>  $db_user,
    'password'      =>  $db_passwd,
    'table_prefix'  =>  'phorum',

    // 'down_page'     => 'http://www.example.com/phorum/down.html',

    // 1=enabled, 0=disabled 
    // (always disable this option for MySQL versions prior to 4.0.18!)
    'mysql_use_ft'  =>  '1'
);

/* 

DETAILED CONFIGURATION FIELD DESCRIPTION
----------------------------------------

* type: 

  The type of database. Typically 'mysql' (the only database type which
  is officially supported by the Phorum distribution). Do not change this
  value, unless you know what you are doing.

* name:

  The name of the database.

* server:

  The hostname or IP-address of the database server. You only need to
  change this if the database server is running on a different system.

* user:

  The username which is used for accessing the database server. The
  user must have full access rights to the database, for creating and
  maintaining the needed tables.

* password:

  The password for the database user.

* table_prefix:

  This table prefix will be at the front of all table names that are
  created and used by Phorum. You only need to change this in case you
  are using the same database for multiple Phorum installations.
  By changing the table prefix you can prevent the tables from the
  installations from colliding. E.g. set the table prefix for one
  installation to "phorum1" and the other to "phorum2".
  Important: never change the table prefix for a running system.

* down_page:

  An optional URL for redirecting the user when database is down.    
  If you want to use this field, remove the "//" in front of it.

* mysql_use_ft (MySQL-only option):

  This field determines whether Phorum will use MySQL's full text 
  algorithm for searching postings. If enabled, your database will
  grow larger (because of an extra table for storing search data),
  but searching for postings will be much faster in return.
  You will have to disable this feature in case you are running a
  database version prior to MySQL 4.0.18.

*/

?>
