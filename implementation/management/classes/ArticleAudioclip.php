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

require_once($g_documentRoot.'/classes/DatabaseObject.php');
require_once($g_documentRoot.'/classes/AudioclipLocalMetadata.php');
//require_once($g_documentRoot.'/classes/Audioclip.php');
//require_once($g_documentRoot.'/classes/Article.php');

/**
 * @package Campsite
 */
class ArticleAudioclip extends DatabaseObject {
    var $m_keyColumnNames = array('fk_article_number', 'fk_audioclip_gunid');
    var $m_dbTableName = 'ArticleAudioclips';
    var $m_columnNames = array('fk_article_number',
                               'fk_audioclip_gunid',
                               'order_no');

    /**
     * The article audioclip table links together articles with Audioclips.
     *
     * @param int $p_articleNumber
     * @param int $p_audioclipId
     * @return object ArticleAudioclip
     */
    function ArticleAudioclip($p_articleNumber = null, $p_audioclipId = null)
    {
        if (is_numeric($p_articleNumber)) {
            $this->m_data['fk_article_number'] = $p_articleNumber;
        }
        if (is_numeric($p_audioclipId)) {
            $this->m_data['fk_audioclip_gunid'] = $p_audioclipId;
        }
    } // constructor


    /**
     * @return string
     */
    function getAudioclipId()
    {
        return $this->m_data['fk_audioclip_gunid'];
    } // fn getAudioclipId


    /**
     * @return int
     */
    function getArticleNumber()
    {
        return $this->m_data['fk_article_number'];
    } // fn getArticleNumber


    /*
     * @return int
     */
    function getAudioclipOrder()
    {
        return $this->m_data['order_no'];
    } // fn getAudioclipOrder


    /**
     * Link the given audioclip file with the given article.
     *
     * @param int $p_audioclipId
     * @param int $p_articleNumber
     * @param int $p_order (optional)
     *
     * @return void
     */
    function AddAudioclipToArticle($p_audioclipId, $p_articleNumber, $p_order = null)
    {
        global $g_ado_db;

        if (is_null($p_order)) {
            $p_order = 0;
        }

        $queryStr = "INSERT IGNORE INTO ArticleAudioclips 
                     (fk_article_number, fk_audioclip_gunid, order_no) 
                     VALUES ('$p_articleNumber', '$p_audioclipId', '$p_order.')";
        $g_ado_db->Execute($queryStr);
    } // fn AddAudioclipToArticle


    /**
     * Get all the audioclips that belong to this article.
     *
     * @param int $p_articleNumber
     * @param int $p_languageId
     * @return array
     */
    function GetAudioclipsByArticleNumber($p_articleNumber, $p_languageId = null)
    {
        global $g_ado_db;

		//if (is_null($p_languageId)) {
		//	$langConstraint = "FALSE";
		//} else {
		//	$langConstraint = "Attachments.fk_language_id=$p_languageId";
		//}
        $queryStr = "SELECT fk_audioclip_gunid
                     FROM ArticleAudioclips
                     WHERE fk_article_number = '$p_articleNumber'";
        $rows = $g_ado_db->GetAll($queryStr);
        $returnArray = array();
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $returnArray[] = AudioclipLocalMetadata::fetchAllMetadataByGunid($row['fk_audioclip_gunid']);
            }
        }

		return $returnArray;
    } // fn GetAudioclipsByArticleNumber


    /**
     * This is called when an audioclip file is deleted.
     * It will disassociate the audioclip from all articles.
     *
     * @param int $p_audioclipId
     * @return void
     */
    function OnAudioclipDelete($p_audioclipId)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAudioclips 
                     WHERE fk_audioclip_gunid = '$p_audioclipId'";
        $g_ado_db->Execute($queryStr);
    } // fn OnAudioclipDelete


    /**
     * Remove audioclip pointers for the given article.
     *
     * @param int $p_articleNumber
     * @return void
     */
    function OnArticleDelete($p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAudioclips 
                     WHERE fk_article_number='$p_articleNumber'";
        $g_ado_db->Execute($queryStr);
    } // fn OnArticleDelete


    /**
     * Copy all the pointers for the given article.
     *
     * @param int $p_srcArticleNumber
     * @param int $p_destArticleNumber
     * @return void
     */
    function OnArticleCopy($p_srcArticleNumber, $p_destArticleNumber)
    {
        global $g_ado_db;

        $queryStr = "SELECT fk_audioclip_gunid, order_no 
                     FROM ArticleAudioclips 
                     WHERE fk_article_number='$p_srcArticleNumber'";
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $queryStr = "INSERT IGNORE INTO ArticleAudioclips 
                         (fk_article_number, fk_audioclip_gunid, order_no) 
                         VALUES ('$p_destArticleNumber', '"
                        .$row['fk_audioclip_gunid']."', '"
                        .$row['order_no']."')";
            $g_ado_db->Execute($queryStr);
        }
    } // fn OnArticleCopy


    /**
     * Remove the linkage between the given audioclip and the given article.
     *
     * @param int $p_audioclipId
     * @param int $p_articleNumber
     *
     * @return void
     */
    function RemoveAudioclipFromArticle($p_audioclipId, $p_articleNumber)
    {
        global $g_ado_db;

        $queryStr = "DELETE FROM ArticleAudioclips 
                     WHERE fk_article_number='$p_articleNumber' 
                     AND fk_audioclip_gunid='$p_attachmentId' 
                     LIMIT 1";
        $g_ado_db->Execute($queryStr);
    } // fn RemoveAudioclipFromArticle

} // class ArticleAudioclip

?>