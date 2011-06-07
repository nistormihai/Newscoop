<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User\Staff;

/**
 * @Acl(resource="user", action="manage")
 */
class Admin_StaffController extends Zend_Controller_Action
{
    /** @var Newscoop\Entity\Repository\User\StaffRepository */
    private $repository;

    /** @var Admin_Form_Staff */
    private $form;

    public function init()
    {
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');

        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Staff');

        $this->form = new Admin_Form_Staff($this->_helper->acl->isAllowed('user', 'manage'));
        $this->form->setAction('')->setMethod('post');

        if ($this->_helper->acl->isAllowed('user', 'manage')) { // set form user groups
            $groups = array();
            $groupRepository = $this->_helper->entity->getRepository('Newscoop\Entity\User\Group');
            foreach ($groupRepository->findAll() as $group) {
                $groups[$group->getId()] = $group->getName();
            }
            $this->form->getElement('groups')->setMultioptions($groups);
        }

        // set form countries
        $countries = array('' => getGS('Select country'));
        foreach (Country::GetCountries(1) as $country) {
            $countries[$country->getCode()] = $country->getName();
        }
        $this->form->getElement('country')->setMultioptions($countries);
    }

    public function indexAction()
    {
        $this->_forward('table');
    }

    public function addAction()
    {
        try {
            $staff = new Staff();
            $this->handleForm($this->form, $staff);
        } catch (PDOException $e) {
            $this->form->getElement('username')->addError(getGS('That user name already exists, please choose a different login name.'));
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError(getGS("That $1 already exists, please choose a different $2.", $field, $field));
        }

        $this->view->form = $this->form;
    }

    /**
     * @Acl(ignore="1")
     */
    public function editAction()
    {
        $staff = $this->_helper->entity->get(new Staff, 'user');

        // check permission
        $auth = Zend_Auth::getInstance();
        $self = $staff->getId() == $auth->getIdentity();
        if (!$self) { // check if user != current
            $this->_helper->acl->check('user', 'manage');
        }

        try {
            $this->form->setDefaultsFromEntity($staff);
            $this->handleForm($this->form, $staff);
        } catch (InvalidArgumentException $e) {
            $field = $e->getMessage();
            $this->form->getElement($field)->addError(getGS("That $1 already exists, please choose a different $2.", $field, $field));
        }

        $this->view->form = $this->form;

        if ($this->_helper->acl->isAllowed('user', 'manage')) {
            $this->view->actions = array(array(
                'label' => getGS('Edit access'),
                'module' => 'admin',
                'controller' => 'staff',
                'action' => 'edit-access',
                'resource' => 'user',
                'privilege' => 'manage',
                'params' => array(
                    'user' => $staff->getId(),
                ),
            ));
        } else {
            $this->view->actions = array(array(
                'label' => getGS('View access'),
                'module' => 'admin',
                'controller' => 'profile',
                'action' => 'access',
                'params' => array(
                    'user' => $staff->getId(),
                    'role' => $staff->getRoleId(),
                ),
            ));
        }
    }

    public function editAccessAction()
    {
        $staff = $this->_helper->entity(new Staff, 'user');
        $this->view->staff = $staff;

        $this->_helper->actionStack('edit', 'acl', 'admin', array(
            'role' => $staff->getRoleId(),
        ));
    }

    /**
     * @Acl(action="delete")
     */
    public function deleteAction()
    {
        $this->_helper->acl->check('user', 'delete');
        
        $staff = $this->_helper->entity->get(new Staff, 'user');
        
        if (Zend_Auth::getInstance()->getIdentity() == $staff->getId()) $permitted = false;
        else $permitted = true;
        
        if ($permitted) {
            $this->repository->delete($staff);
    
            $this->_helper->entity->getManager()->flush();
    
            $this->_helper->flashMessenger(getGS('Staff member deleted.'));
            $this->_helper->redirector->gotoSimple('index');
        }
        else {
            $this->_helper->flashMessenger(getGS('Self-delete is not permitted.')); // should be translateable
            $this->_helper->redirector->gotoSimple('index');
        }
    }

    public function tableAction()
    {
        $table = $this->getHelper('datatable');

        $table->setEntity('Newscoop\Entity\User\Staff');

        $table->setCols(array(
            'name' => getGS('Full Name'),
            'username' => getGS('Accout Name'),
            'email' => getGS('E-Mail'),
            'timeCreated' => getGS('Creation Date'),
            getGS('Delete'),
        ));

        $view = $this->view;
        $table->setHandle(function(Staff $staff) use ($view) {
            $editLink = sprintf('<a href="%s" class="edit" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'edit',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                getGS('Edit staff member $1', $staff->getName()),
                $staff->getName()
            );

            $deleteLink = sprintf('<a href="%s" class="delete confirm" title="%s">%s</a>',
                $view->url(array(
                    'action' => 'delete',
                    'user' => $staff->getId(),
                    'format' => NULL,
                )),
                getGS('Delete staff member $1', $staff->getName()),
                getGS('Delete')
            );

            return array(
                $editLink,
                $staff->getUsername(),
                $staff->getEmail(),
                $staff->getTimeCreated()->format('Y-m-d H:i:s'),
                $deleteLink,
            );
        });

        $table->dispatch();

        $this->view->actions = array(
            array(
                'label' => getGS('Add new staff member'),
                'module' => 'admin',
                'controller' => 'staff',
                'action' => 'add',
                'resource' => 'user',
                'privilege' => 'manage',
            ),
        );
    }

    private function handleForm(Zend_Form $form, Staff $staff)
    {
        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repository->save($staff, $form->getValues());
            $this->_helper->entity->getManager()->flush();

            // add default widgets for new staff
            if ($this->_getParam('action') == 'add') {
                WidgetManager::SetDefaultWidgets($staff->getId());
            }

            $this->_helper->flashMessenger(getGS('Staff member saved.'));
            $this->_helper->redirector->gotoSimple('edit', 'staff', 'admin', array(
                'user' => $staff->getId(),
            ));
        }
    }
}
