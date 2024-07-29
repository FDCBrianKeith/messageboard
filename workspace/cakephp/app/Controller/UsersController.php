<?php
class UsersController extends AppController {
    public $uses = ['User'];

	public function beforeFilter() {
        parent::beforeFilter();

        // always restrict your whitelists to a per-controller basis
        $this->Auth->allow("login","register");
    }

    public function login() {
        
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error( __('Username or password incorrect'));
            }
        }
    }

    public function register() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($result = $this->User->save($this->request->data)) {
                $authUser = $this->User->findById($result['User']['id']);
                $this->Auth->login($authUser['User']);
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'thankyou'));
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        }
    }

    public function thankyou() {
        
    }
}