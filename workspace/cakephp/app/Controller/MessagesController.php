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

	public function view($recipientId = null) {
		$id = $this->Auth->user('id'); 
		if ($recipientId) {
			$recipient = $this->User->findById($recipientId);
			if ($recipient) {
				$conditions = array(
					'Message.recipient_id' => array($recipientId,$id),
					'Message.sender_id' => array($recipientId,$id),
				);
				$this->paginate = array(
					'conditions' => $conditions,
					'order' => array('Message.created' => 'desc'),
					'limit' => 10,
				);
				$this->User->recursive = -1;
				$messages = $this->paginate('Message');
				$this->log($recipient);
				$success = true;
				$this->set(compact('messages','id','recipientId', 'recipient', 'success'));
			} else {
				$this->Flash->error(__('User not found'));
				return $this->redirect(array('action' => 'index'));
			}
		} else {
			return $this->redirect(array('action' => 'index'));
		}
	}

    public function ajaxGetUsers() {
		$this->layout = false;
		$this->autoRender = false;
		$this->User->recursive = -1;
		$id = $this->Auth->user('id'); 
		$conditions = array(
            'conditions' => array(
                'User.email LIKE' => '%' . $this->request->query('q') . '%',
				'User.id !=' => $id
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

	public function ajaxDeleteMessage() {
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$this->log($this->request->query);
			$this->log($this->request->params);
			$data = $this->request->data;
			$id = $this->Auth->user('id'); 
			$recipientId = $data['recipientId'];
			$conditions = array(
				'Message.recipient_id' => array($recipientId,$id),
				'Message.sender_id' => array($recipientId,$id),
			);
			$nextMessage = $this->Message->find(
				'all',
				array(
					'conditions' => $conditions,
					'limit' => 10,
					'page' => $data['currentPage']+1,
					'order' => array('Message.created' => 'desc'),
				)
			);
			$flag = $this->Message->delete($data['id']);
			if ($flag) {
				return json_encode(array(
					'success' => true,
					'message' => 'Message deleted successfully',
					'nextMessage' => (count($nextMessage) > 0)?$nextMessage[0]:null
				));
			}
		}
	}

	public function ajaxGetLists() {
		$this->layout = false;
		$this->autoRender = false;
		$this->Message->recursive = -1;
		$limit = 10;
		if ($this->request->is('ajax')) {
			$currentUserId = $this->Auth->user('id');
			$data = $this->request->query;
			$searchQuery = (isset($data['search']))?$data['search']:'';
			$page = (isset($data['page']))?$data['page']:1;
			
			$query = array(
				'fields' => array('Message.*', 'User.*', 'Sender.*'), // Adjusted to only include the sender
				'joins' => array(
					array(
						'table' => 'users',
						'alias' => 'User',
						'type' => 'LEFT',
						'conditions' => array(
							'User.id = Message.recipient_id', // Join on sender
					)
					),
					array(
						'table' => 'users',
						'alias' => 'Sender',
						'type' => 'LEFT',
						'conditions' => array(
							'Sender.id = Message.sender_id', // Join on sender
						)
					)
				),
				'conditions' => array(
					'Message.id = (SELECT MAX(um2.id) FROM messages um2 WHERE (um2.sender_id, um2.recipient_id) IN ((Message.sender_id, Message.recipient_id), (Message.recipient_id, Message.sender_id)))',
					'AND' => array (
						'OR' => array(
							'Sender.name LIKE' => '%'.$searchQuery.'%',
							'User.name LIKE' => '%'.$searchQuery.'%'
						)
					),
					'OR' => array(
						'Message.sender_id' => $currentUserId,
						'Message.recipient_id' => $currentUserId,
					)
				),
				'limit' => $limit,
				'page' => $page,
				'order' => array('Message.created' => 'desc'),
			);
			
			// Executing the query
			$messages = $this->Message->find('all', $query);
			$this->log($messages);

			foreach ($messages as $key => $message) {
				if ($message['Message']['recipient_id'] === $currentUserId) {
					$messages[$key]['User'] = $message['Sender'];
				}
			}

			{
				unset($query['limit']);
				unset($query['page']);
				$count = $this->Message->find('count',$query);
			}
			if (count($messages) > 0) {
				return json_encode(array(
					'success' => true,
					'message' => 'Messages found',
					'data' => $messages,
					'totalPages' => ceil($count/$limit)
				));
			} else {
				return json_encode(array(
					'success' => false,
					'message' => 'No messages found',
				));
			}
		}
	}

	public function ajaxDeleteConversation() {
		$this->layout = false;
		$this->autoRender = false;
		if ($this->request->is('post')) {
			$data = $this->request->data;
			$flag = $this->Message->deleteAll(array(
				'OR' => array(
					array(
						'AND' => array(
							'recipient_id' => $data['recipient'],
							'sender_id' => $data['sender']
						)
					),
					array(
						'AND' => array(
							'recipient_id' => $data['sender'],
							'sender_id' => $data['recipient']
						)
					)
				)
			));
			if ($flag) {
				return json_encode(array(
					'success' => true,
					'message' => 'Message deleted successfully',
				));
			}
		}
	}

}