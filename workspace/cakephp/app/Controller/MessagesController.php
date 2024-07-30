<?php
class MessagesController extends AppController {
    public $uses = ['User', 'Message'];

	public function index() {
		
	}

    public function new() {
        $this->User->recursive = -1;
        $users = $this->User->find('all');
        $this->set(compact('users'));
    }

	public function view($recipientId=null) {
		$id = $this->Auth->user('id'); 
		$conditions = array(
			'Message.recipient_id' => array($recipientId,$id),
			'Message.sender_id' => array($recipientId,$id),
		);
		$this->paginate = array(
			'conditions' => $conditions,
			'order' => array('Message.created' => 'desc'),
			'limit' => 10,
		);
		$messages = $this->paginate('Message');
		$this->log($messages);
		$this->set(compact('messages','id','recipientId'));
	}

    public function ajaxGetUsers() {
		$this->layout = false;
		$this->autoRender = false;
		$this->User->recursive = -1;
		$conditions = array(
            'conditions' => array(
                'User.email LIKE' => '%' . $this->request->query('q') . '%'
            )
        );
        $users = $this->User->find('all', $conditions);
		return json_encode(array(
			'success' => true,
			'message' => 'Retrieved users successfully',
			'data' => $users
		));
	}

    public function ajaxSendMessage() {
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$data = $this->request->data;
			$newMessage = $this->Message->save([
				'sender_id' => $this->Auth->user('id'),
				'recipient_id' => $data['recipient_id'],
				'message' => $data['message']
			]);
			if ($newMessage) {
                $this->Flash->success(__('Message sent successfully'));
				return json_encode(array(
					'success' => true,
					'message' => 'Message sent successfully',
					'data' => $newMessage
				));
			}
			$this->Flash->error(__('Message not sent'));
			return json_encode(array(
				'success' => false,
				'message' => 'Message not sent',
			));
		}
	}

}