<?php
/**
 * @package Newscoop
 * @subpackage Subscriptions
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 *
 *
 */
use Newscoop\Entity\Comment\Commenter;

//function to get the ip address
function getIp()
{
    if(!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    else
        return $_SERVER['REMOTE_ADDR'];
}

class Admin_CommentCommenterController extends Zend_Controller_Action
{

    /**
     * @var ICommenter
     */
    private $repository;

    /**
     * @var IAcceptance
     */
    private $acceptanceRepository;

    /**
     * @var IArticle
     */
    private $articleRepository;

    /**
     * @var IPublication
     */
    private $publicationRepository;

    /**
     *
     * @var Admin_Form_Commenter
     */
    private $form;


    public function init()
    {
        // get commenter repository
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Commenter');

        // get acceptance repository
        $this->acceptanceRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment\Acceptance');

        // get article repository
        $this->articleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Article');

        // get publication repository
        $this->publicationRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Publication');

        $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        // set the default form for comment commenter and set method to post
        $this->form = new Admin_Form_Commenter;
        $this->form->setMethod('post');
        return $this;

    }

    public function indexAction()
    {
       $this->getHelper('contextSwitch')
            ->addActionContext('index', 'json')
            ->initContext();
        $table = $this->getHelper('datatable');

        $table->setDataSource($this->repository);

        $table->setCols(array(
            'time_created' => getGS('Date Created'),
            'name' => getGS('Name'),
            'user' => getGS('Username'),
            'email' => getGS('Email'),
            'url'   => getGS('Website'),
            'ip'   => getGS('Ip'),
            'edit' => getGS('Edit'),
            'ban' => getGS('Ban'),
            'delete' => getGS('Delete')
        ));

        $view = $this->view;
        $table->setHandle(function($commenter) use ($view) {
            $urlParam = array('commenter' => $commenter->getId());
            return array(
                $commenter->getTimeCreated()->format('Y-i-d H:i:s'),
                $commenter->getName(),
                $commenter->getUsername(),
                $commenter->getEmail(),
                $commenter->getUrl(),
                $commenter->getIp(),
                $view->linkEdit($urlParam),
                $view->linkBan($urlParam),
                $view->linkDelete($urlParam),
            );
        });

        $table->dispatch();
    }

    /**
     * Action for Adding a Commenter
     */
    public function addAction()
    {
        $commenter = new Commenter;

        $this->handleForm($this->form, $commenter);

        $this->view->form = $this->form;
        $this->view->commenter = $commenter;
    }

    /**
     * Action for Editing a Commenter
     */
    public function editAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['commenter'])) {
            throw new InvalidArgumentException;
        }
        $commenter = $this->repository->find($params['commenter']);
        if($commenter)
        {
            $this->form->setFromEntity($commenter);
            $this->handleForm($this->form, $commenter);
            $this->view->form = $this->form;
            $this->view->commenter = $commenter;
        }
    }

    /**
     * Action for Deleteing a Commenter
     */
    public function deleteAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['commenter'])) {
            throw new InvalidArgumentException;
        }
        $commenter = $this->repository->find($params['commenter']);
        if($commenter)
        {
            $this->repository->delete($commenter);
            $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Commenter "$1" deleted.',$commenter->getName()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

    public function toggleBanAction()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['commenter']) && (!isset($params['article']) || !isset($params['forum']) )) {
            throw new InvalidArgumentException;
        }
        if(isset($params['thread']))
            $publication = $this->articleRepository->find($params['thread'])->getPublication();
        if(isset($params['forum']))
            $publication = $this->publicationRepository->find($params['forum']);

        $commenter = $this->repository->find($params['commenter']);

        $form = new Admin_Form_Ban;
        $this->handleBanForm($form, $commenter, $publication);
        $form->setValues($commenter, $this->acceptanceRepository->isBanned($commenter, $publication));
        $this->view->form = $form;
    }

    /**
     * Method for saving a banned
     *
     * @param ZendForm $p_form
     * @param ICommenter $p_commenter
     */
    private function handleBanForm(Zend_Form $p_form, $p_commenter, $p_publication)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $this->acceptanceRepository->saveBanned($p_commenter, $p_publication, $values);
            $this->acceptanceRepository->flush();
            $this->_helper->flashMessenger(getGS('Ban for commenter "$1" saved.',$p_commenter->getName()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

    /**
     * Method for saving a commenter
     *
     * @param ZendForm $p_form
     * @param ICommenter $p_commenter
     */
    private function handleForm(Zend_Form $p_form, $p_commenter)
    {
        if ($this->getRequest()->isPost() && $p_form->isValid($_POST)) {
            $values = $p_form->getValues();
            $values['ip'] = getIp();
            $values['time_created'] = new DateTime;
            $this->repository->save($p_commenter, $values);
            $this->repository->flush();
            $this->_helper->flashMessenger(getGS('Commenter "$1" saved.',$p_commenter->getName()));
            $this->_helper->redirector->gotoSimple('index');
        }
    }

}