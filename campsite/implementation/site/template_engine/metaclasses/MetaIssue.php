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

require_once($g_documentRoot.'/classes/Issue.php');
require_once($g_documentRoot.'/template_engine/metaclasses/MetaDbObject.php');

/**
 * @package Campsite
 */
final class MetaIssue extends MetaDbObject {

    private function InitProperties()
    {
        if (!is_null($this->m_properties)) {
            return;
        }
        $this->m_properties['name'] = 'Name';
        $this->m_properties['number'] = 'Number';
        $this->m_properties['date'] = 'PublicationDate';
        $this->m_properties['publish_date'] = 'PublicationDate';
        $this->m_properties['url_name'] = 'ShortName';
    }


    public function __construct($p_publicationId = null, $p_languageId = null,
    $p_issueNumber = null)
    {
        $this->m_dbObject = new Issue($p_publicationId, $p_languageId, $p_issueNumber);

        $this->InitProperties();
        $this->m_customProperties['year'] = 'getPublishYear';
        $this->m_customProperties['mon'] = 'getPublishMonth';
        $this->m_customProperties['wday'] = 'getPublishWeekDay';
        $this->m_customProperties['mday'] = 'getPublishMonthDay';
        $this->m_customProperties['yday'] = 'getPublishYearDay';
        $this->m_customProperties['hour'] = 'getPublishHour';
        $this->m_customProperties['min'] = 'getPublishMinute';
        $this->m_customProperties['sec'] = 'getPublishSecond';
        $this->m_customProperties['mon_name'] = 'getPublishMonthName';
        $this->m_customProperties['wday_name'] = 'getPublishWeekDayName';
        $this->m_customProperties['template'] = 'getTemplate';
        $this->m_customProperties['publication'] = 'getPublication';
        $this->m_customProperties['language'] = 'getLanguage';
        $this->m_customProperties['is_current'] = 'isCurrent';
        $this->m_customProperties['defined'] = 'defined';
    } // fn __construct


    protected function getPublishYear()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['year'];
    }


    protected function getPublishMonth()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['mon'];
    }


    protected function getPublishWeekDay()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['wday'];
    }


    protected function getPublishMonthDay()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['mday'];
    }


    protected function getPublishYearDay()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['yday'];
    }


    protected function getPublishHour()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['hours'];
    }


    protected function getPublishMinute()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['minutes'];
    }


    protected function getPublishSecond()
    {
        $publish_timestamp = strtotime($this->m_dbObject->getProperty('PublicationDate'));
        $publish_date_time = getdate($publish_timestamp);
        return $publish_date_time['seconds'];
    }


    protected function getPublishMonthName() {
        $dateTime = new MetaDatetime($this->m_dbObject->getProperty('PublicationDate'));
        return $dateTime->getMonthName();
    }


    protected function getPublishWeekDayName() {
        $dateTime = new MetaDatetime($this->m_dbObject->getProperty('PublicationDate'));
        return $dateTime->getWeekDayName();
    }


    protected function getTemplate()
    {
        return new MetaTemplate($this->m_dbObject->getIssueTemplateId());
    }


    protected function getPublication()
    {
        return new MetaPublication($this->m_dbObject->getPublicationId());
    }


    protected function getLanguage()
    {
        return new MetaLanguage($this->m_dbObject->getLanguageId());
    }


    protected function isCurrent() {
        $currentIssue = Issue::GetCurrentIssue($this->m_dbObject->getPublicationId(),
        $this->m_dbObject->getLanguageId());
        return !is_null($currentIssue)
        && $currentIssue->getIssueNumber() == $this->m_dbObject->getIssueNumber();
    }

} // class MetaIssue

?>