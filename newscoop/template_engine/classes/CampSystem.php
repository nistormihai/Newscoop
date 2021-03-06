<?php
/**
 * @package Campsite
 *
 * @author Holman Romero <holman.romero@gmail.com>
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Class CampSystem
 */
abstract class CampSystem
{
    /**
     *
     */
    abstract protected function dispatch();


    /**
     *
     */
    abstract protected function render();


    /**
     * Reads a configuration setting.
     *
     * @param string $p_varName
     *
     * @return mixed
     *      The value of the configuration variable
     */
    protected function getSetting($p_varName)
    {
        $config = CampSite::GetConfigInstance();
        return $config->getSetting($p_varName);
    } // fn getSetting


    /**
     * Sets the context language object.
     *
     * @param integer $p_lngId
     *      The language identifier
     *
     * @return void
     */
    protected function setLanguage($p_lngId)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->language)
                && $context->language->number == $p_lngId) {
            return;
        }
        $context->language = new MetaLanguage($p_lngId);
    } // fn setLanguage


    /**
     * Sets the context publication object.
     *
     * @param integer $p_pubId
     *      The publication identifier
     *
     * @return void
     */
    protected function setPublication($p_pubId)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->publication)
                && $context->publication->identifier == $p_pubId) {
            return;
        }
        $context->publication = new MetaPublication($p_pubId);
    } // fn setPublication


    /**
     * Sets the context issue object.
     *
     * @param integer $p_pubId
     *      The publication identifier
     * @param integer $p_lngId
     *      The language identifier
     * @param integer $p_issNr
     *      The issue number
     *
     * @return void
     */
    protected function setIssue($p_pubId, $p_lngId, $p_issNr)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->issue)
                && $context->issue->number == $p_issNr) {
            return;
        }
        $context->issue = new MetaIssue($p_pubId,$p_lngId, $p_issNr);
    } // fn setIssue


    /**
     * Sets the context section object.
     *
     * @param integer $p_pubId
     *      The publication identifier
     * @param integer $p_issNr
     *      The issue number
     * @param integer $p_lngId
     *      The language identifier
     * @param integer $p_sctNr
     *      The section number
     *
     * @return void
     */
    protected function setSection($p_pubId, $p_issNr, $p_lngId, $p_sctNr)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->section)
                && $context->section->number == $p_sctNr) {
            return;
        }
        $context->section = new MetaSection($p_pubId,$p_issNr, $p_lngId, $p_sctNr);
    } // fn setSection


    /**
     * Sets the context article object.
     *
     * @param integer $p_lngId
     *      The language identifier
     * @param integer $p_artNr
     *      The article number
     *
     * @return void
     */
    protected function setArticle($p_lngId, $p_artNr)
    {
        $context = CampTemplate::singleton()->context();
        if (is_object($context->article)
                && $context->article->number == $p_artNr) {
            return;
        }
        $context->article = new MetaArticle($p_lngId, $p_artNr);
    } // fn setArticle


    /**
     *
     */
    public static function GetTemplateNameById($p_tplId)
    {
        $template = new Template($p_tplId);
        if (!is_object($template) || !$template->exists()) {
            return null;
        }

        return $template->getName();
    } // fn GetTemplateNameById


    /**
     *
     */
    public static function GetTemplateIdByName($p_fileName)
    {
        $template = new Template($p_fileName);
        if (!is_object($template) || !$template->exists()) {
            return null;
        }

        return $template->getTemplateId();
    } // fn GetTemplateIdByName


    public static function GetInvalidURLTemplate($p_publicationId)
    {
    	$pub = new Publication($p_publicationId);
    	return $pub->getProperty('url_error_tpl_id');
    } // fn GetInvalidURLTemplate


    /**
     *
     */
    public static function GetTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr,
                                       $p_artNr, $p_isPublished = true)
    {
        global $g_ado_db;

        if ($p_lngId <= 0) {
            $publication = new Publication($p_pubId);
            if (!$publication->exists()) {
                return null;
            }
            $p_lngId = $publication->getLanguageId();
        }
        if ($p_artNr > 0) {
            if ($p_issNr <= 0 || $p_sctNr <= 0) {
                $article = new Article($p_lngId, $p_artNr);
                if (!$article->exists()
                || ($p_isPublished && !$article->isPublished())) {
                    return self::GetInvalidURLTemplate($p_pubId);
                }
                $p_issNr = $article->getIssueNumber();
                $p_sctNr = $article->getSectionNumber();
            }
            return self::GetArticleTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr);
        }
        if ($p_sctNr > 0) {
            if ($p_issNr <= 0) {
                $sql = 'SELECT MAX(i.Number) AS Number '
                    . 'FROM Sections as s, Issues as i '
                    . 'WHERE s.IdPublication = i.IdPublication'
                    . ' AND s.IdLanguage = i.IdLanguage'
                    . ' AND s.IdPublication = ' . $p_pubId
                    . ' AND s.IdLanguage = ' . $p_lngId;
                if ($p_isPublished == true) {
                    $sql .= " AND i.Published = 'Y'";
                }
                $data = $g_ado_db->GetOne($sql);
                if (empty($data)) {
                    return null;
                }
                $p_issNr = $data;
            }
            return self::GetSectionTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr);
        }
        if ($p_issNr <= 0) {
            $sql = 'SELECT MAX(Number) AS Number FROM Issues '
                . 'WHERE IdPublication = ' . $p_pubId
                . ' AND IdLanguage = ' . $p_lngId;
            if ($p_isPublished == true) {
                $sql .= " AND Published = 'Y'";
            }
            $data = $g_ado_db->GetOne($sql);
            if (empty($data)) {
                return null;
            }
            $p_issNr = $data;
        }
        return self::GetIssueTemplate($p_lngId, $p_pubId, $p_issNr);
    } // fn GetTemplate


    public static function GetIssueTemplate($p_lngId, $p_pubId, $p_issNr)
    {
        global $g_ado_db;

        if (CampCache::IsEnabled()) {
            $paramString = $p_lngId . '_' . $p_pubId . '_' . $p_issNr;
            $cacheKey = __CLASS__ . '_IssueTemplate_' . $paramString;
            $issueTemplate = CampCache::singleton()->fetch($cacheKey);
            if ($issueTemplate !== false && !empty($issueTemplate)) {
                return $issueTemplate;
            }
        }

        $sql = 'SELECT t.Name FROM Issues as i, Templates as t '
            . 'WHERE i.IssueTplId = t.Id'
            . ' AND i.IdLanguage = ' . $p_lngId
            . ' AND i.IdPublication = ' . $p_pubId
            . ' AND i.Number = ' . $p_issNr;
        $data = $g_ado_db->GetOne($sql);
        if (empty($data)) {
            $data = self::GetInvalidURLTemplate($p_pubId);
        }
        if (CampCache::IsEnabled()) {
            CampCache::singleton()->store($cacheKey, $data);
        }

        return $data;
    } // fn GetIssueTemplate


    public static function GetSectionTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr)
    {
        global $g_ado_db;

        if (CampCache::IsEnabled()) {
            $paramString = $p_lngId . '_' . $p_pubId . '_' . $p_issNr . '_' . $p_sctNr;
            $cacheKey = __CLASS__ . '_SectionTemplate_' . $paramString;
            $sectionTemplate = CampCache::singleton()->fetch($cacheKey);
            if ($sectionTemplate !== false && !empty($sectionTemplate)) {
                return $sectionTemplate;
            }
        }

        if ($p_sctNr > 0) {
            $sql = 'SELECT t.Name FROM Sections as s, Templates as t '
                . 'WHERE s.SectionTplId = t.Id'
                . ' AND s.IdLanguage = ' . $p_lngId
                . ' AND s.IdPublication = ' . $p_pubId
                . ' AND s.NrIssue = ' . $p_issNr
                . ' AND s.Number = ' . $p_sctNr;
            $data = $g_ado_db->GetOne($sql);
            if (!empty($data)) {
                return $data;
            }
        }

        $sql = 'SELECT t.Name FROM Issues as i, Templates as t '
            . 'WHERE i.SectionTplId = t.Id'
            . ' AND i.IdLanguage = ' . $p_lngId
            . ' AND i.IdPublication = ' . $p_pubId
            . ' AND i.Number = ' . $p_issNr;
        $data = $g_ado_db->GetOne($sql);
        if (empty($data)) {
            $data = self::GetInvalidURLTemplate($p_pubId);
        }
        if (CampCache::IsEnabled()) {
            CampCache::singleton()->store($cacheKey, $data);
        }

        return $data;
    } // fn GetSectionTemplate


    public static function GetArticleTemplate($p_lngId, $p_pubId, $p_issNr, $p_sctNr)
    {
        global $g_ado_db;

        if (CampCache::IsEnabled()) {
            $paramString = $p_lngId . '_' . $p_pubId . '_' . $p_issNr . '_' . $p_sctNr;
            $cacheKey = __CLASS__ . '_ArticleTemplate_' . $paramString;
            $articleTemplate = CampCache::singleton()->fetch($cacheKey);
            if ($articleTemplate !== false && !empty($articleTemplate)) {
                return $articleTemplate;
            }
        }

        if ($p_sctNr > 0) {
            $sql = 'SELECT t.Name FROM Sections as s, Templates as t '
                . 'WHERE s.ArticleTplId = t.Id'
                . ' AND s.IdLanguage = ' . $p_lngId
                . ' AND s.IdPublication = ' . $p_pubId
                . ' AND s.NrIssue = ' . $p_issNr
                . ' and s.Number = ' . $p_sctNr;
            $data = $g_ado_db->GetOne($sql);
            if (!empty($data)) {
                return $data;
            }
        }

        $sql = 'SELECT t.Name FROM Issues as i, Templates as t '
            . 'WHERE i.ArticleTplId = t.Id '
            . ' AND i.IdLanguage = ' . $p_lngId
            . ' AND i.IdPublication = ' . $p_pubId
            . ' and i.Number = ' . $p_issNr;
        $data = $g_ado_db->GetOne($sql);
        if (empty($data)) {
            $data = self::GetInvalidURLTemplate($p_pubId);
        }
        if (CampCache::IsEnabled()) {
            CampCache::singleton()->store($cacheKey, $data);
        }

        return $data;
    } // fn GetArticleTemplate

} // class CampSystem

?>