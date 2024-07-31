<?php
class UsersController extends AppController {
    public $uses = ['User'];

	public function beforeFilter() {
        parent::beforeFilter();

        // always restrict your whitelists to a per-controller basis
        $this->Auth->allow("login","register");
    }

    public function index() {
        $id = $this->Auth->user('id'); 
        $currentUser = $this->User->findById($id);
        if (!$currentUser['User']['image']) {
            $path = '/app/webroot/img/pfp.jpg';
            $currentUser['User']['image'] = $path;
        } else {
            $currentUser['User']['image'] = '/'.$currentUser['User']['image'];
        }
        $currentUser['User']['age'] = ($currentUser['User']['birthdate'])?$this->getAgeToday($currentUser['User']['birthdate']):0;
        $this->set('user', $currentUser['User']);
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

    public function edit() {
        try {
            //code...
            $this->User->validates();
            $id = $this->Auth->user('id'); 
            $currentUser = $this->User->findById($id);
            if (!$currentUser['User']['image']) {
                $path = '/app/webroot/img/pfp.jpg';
                $currentUser['User']['image'] = $path;
            }
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->request->data['User']['id'] = $currentUser['User']['id'];
                if ($this->request->data['User']['submittedfile']['name']){
                    $imagePath = $this->uploadFile($this->request->data['User']['submittedfile']);
                    $this->request->data['User']['image'] = $imagePath ?? $currentUser['User']['image'];
                }
                
                $this->request->data['User']['birthdate'] = trim($this->request->data['User']['dob']);
                if ($this->User->save($this->request->data)) {
                    $user = $this->Auth->user();
                    // Update the 'name' field with data from the form
                    $user['name'] = $this->request->data['User']['name'];
                    // Update the user session data
                    $this->Auth->login($user);
                    $this->Flash->success(__('The user has been updated'));
                    return $this->redirect(array('action' => 'index'));
                }
                $this->Flash->error(
                    __('The user could not be saved. Please, try again.')
                );
            } else {
            // //     $this->request->data = $this->User->findById($id);
            // //     unset($this->request->data['User']['password']);
            }
            $this->set('user',$currentUser['User']);
        } catch (\Exception $th) {
            //throw $th;
            $this->Flash->error(
                __($th->getMessage())
            );
            $id = $this->Auth->user('id'); 
            $currentUser = $this->User->findById($id);
            $this->set('user',$currentUser['User']);
        }
    }

    public function changePassword() {
        if ($this->request->is('post')) {
            unset($this->User->validate['password']);
    		$this->User->recursive = -1;
            $data = $this->request->data['User'];
            $currentUser = $this->Auth->user();
            $userPassword = $this->User->find('first', array(
                'conditions' => array(
                'User.id' => $currentUser['id']),
                'fields' => array('password'),
            ));
            $isEquals = $this->isPasswordEquals($userPassword['User']['password'],$data['password']);
            if ($isEquals) {
                $status = $this->User->save(array(
                    'id' => $currentUser['id'],
                    'password' => $data['newPassword'],
                ));
                if ($status) {
                    $this->Flash->success(__('The password has been updated'));
                } else {
                    $this->Flash->error(__('Changing of password failed'));
                }
            } else {
                $this->Flash->error(__('Passwords does not match'));
            }
        }
    }

    public function isPasswordEquals($password, $newPassword) {
        $newHash = Security::hash($newPassword, 'blowfish', $password);
        return $password == $newHash;
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function uploadFile($img) {
        
        if(isset($img) && $img['name']){
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($img['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Cannot upload file');
            }
    
            $relativePath = 'app/webroot/img/'.$img['name'];
            $path = $_SERVER['DOCUMENT_ROOT'] . '/cakephp/'.$relativePath;
            if(move_uploaded_file($img['tmp_name'],$path)){
                return $relativePath;
            }
        }
        return '';
    }

    public function thankyou() {
        
    }

    public function getAgeToday($birthdate) {
        $date = new DateTime($birthdate);
        $now = new DateTime();
        $interval = $now->diff($date);
        return $interval->y;
    }
}