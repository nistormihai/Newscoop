<?php

namespace Newscoop\Entity\Proxy;

/**
 * THIS CLASS WAS GENERATED BY THE DOCTRINE ORM. DO NOT EDIT THIS FILE.
 */
class NewscoopEntityCommentProxy extends \Newscoop\Entity\Comment implements \Doctrine\ORM\Proxy\Proxy
{
    private $_entityPersister;
    private $_identifier;
    public $__isInitialized__ = false;
    public function __construct($entityPersister, $identifier)
    {
        $this->_entityPersister = $entityPersister;
        $this->_identifier = $identifier;
    }
    private function _load()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            if ($this->_entityPersister->load($this->_identifier, $this) === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            unset($this->_entityPersister, $this->_identifier);
        }
    }

    
    public function setId($p_id)
    {
        $this->_load();
        return parent::setId($p_id);
    }

    public function getId()
    {
        $this->_load();
        return parent::getId();
    }

    public function setTimeCreated(\DateTime $p_datetime)
    {
        $this->_load();
        return parent::setTimeCreated($p_datetime);
    }

    public function getTimeCreated()
    {
        $this->_load();
        return parent::getTimeCreated();
    }

    public function setTimeUpdated(\DateTime $p_datetime)
    {
        $this->_load();
        return parent::setTimeUpdated($p_datetime);
    }

    public function getTimeUpdated()
    {
        $this->_load();
        return parent::getTimeUpdated();
    }

    public function setSubject($p_subject)
    {
        $this->_load();
        return parent::setSubject($p_subject);
    }

    public function getSubject()
    {
        $this->_load();
        return parent::getSubject();
    }

    public function setMessage($p_message)
    {
        $this->_load();
        return parent::setMessage($p_message);
    }

    public function getMessage()
    {
        $this->_load();
        return parent::getMessage();
    }

    public function setIp($p_ip)
    {
        $this->_load();
        return parent::setIp($p_ip);
    }

    public function getIp()
    {
        $this->_load();
        return parent::getIp();
    }

    public function setCommenter(\Newscoop\Entity\Comment\Commenter $p_commenter)
    {
        $this->_load();
        return parent::setCommenter($p_commenter);
    }

    public function getCommenter()
    {
        $this->_load();
        return parent::getCommenter();
    }

    public function getCommenterName()
    {
        $this->_load();
        return parent::getCommenterName();
    }

    public function setStatus($p_status)
    {
        $this->_load();
        return parent::setStatus($p_status);
    }

    public function getStatus()
    {
        $this->_load();
        return parent::getStatus();
    }

    public function setForum(\Newscoop\Entity\Publication $p_forum)
    {
        $this->_load();
        return parent::setForum($p_forum);
    }

    public function getForum()
    {
        $this->_load();
        return parent::getForum();
    }

    public function setThread(\Newscoop\Entity\Article $p_thread)
    {
        $this->_load();
        return parent::setThread($p_thread);
    }

    public function getThread()
    {
        $this->_load();
        return parent::getThread();
    }

    public function setThreadLevel($p_level)
    {
        $this->_load();
        return parent::setThreadLevel($p_level);
    }

    public function getThreadLevel()
    {
        $this->_load();
        return parent::getThreadLevel();
    }

    public function setThreadOrder($p_order)
    {
        $this->_load();
        return parent::setThreadOrder($p_order);
    }

    public function getThreadOrder()
    {
        $this->_load();
        return parent::getThreadOrder();
    }

    public function setLanguage(\Newscoop\Entity\Language $p_language)
    {
        $this->_load();
        return parent::setLanguage($p_language);
    }

    public function getLanguage()
    {
        $this->_load();
        return parent::getLanguage();
    }

    public function setParent(\Newscoop\Entity\Comment $p_parent)
    {
        $this->_load();
        return parent::setParent($p_parent);
    }

    public function getParent()
    {
        $this->_load();
        return parent::getParent();
    }

    public function getLikes()
    {
        $this->_load();
        return parent::getLikes();
    }

    public function getDislikes()
    {
        $this->_load();
        return parent::getDislikes();
    }

    public function getRealName()
    {
        $this->_load();
        return parent::getRealName();
    }

    public function SameAs($p_comment)
    {
        $this->_load();
        return parent::SameAs($p_comment);
    }

    public function exists()
    {
        $this->_load();
        return parent::exists();
    }

    public function getProperty($p_key)
    {
        $this->_load();
        return parent::getProperty($p_key);
    }


    public function __sleep()
    {
        return array('__isInitialized__', 'id', 'commenter', 'forum', 'parent', 'thread', 'language', 'subject', 'message', 'thread_level', 'thread_order', 'status', 'ip', 'time_created', 'likes', 'dislikes');
    }

    public function __clone()
    {
        if (!$this->__isInitialized__ && $this->_entityPersister) {
            $this->__isInitialized__ = true;
            $class = $this->_entityPersister->getClassMetadata();
            $original = $this->_entityPersister->load($this->_identifier);
            if ($original === null) {
                throw new \Doctrine\ORM\EntityNotFoundException();
            }
            foreach ($class->reflFields AS $field => $reflProperty) {
                $reflProperty->setValue($this, $reflProperty->getValue($original));
            }
            unset($this->_entityPersister, $this->_identifier);
        }
        
    }
}