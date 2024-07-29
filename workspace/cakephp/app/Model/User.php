<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class User extends AppModel {

    public $hasMany = array(
        'Messages' => array(
            'className' => 'Message',
            'foreignKey' => 'sender_id'
        )
    );

    public $validate = array(
        'name' => array(
            'rule' => array('lengthBetween', 5, 20),
            'message' => 'Name must be 5-20 characters'
        ),
        'email' => array(
            'rule' => 'isUnique',
            'message' => 'An email already exists'
        ),
        'password' => array(
            'Not Empty' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your password',
            ),
            'Match passwords' => array(
                'rule' => 'matchPasswords',
                'message' => 'Passwords does not match'
            )
        ),
        'password_confirmation' => array(
            'Not Empty' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your password',
            ),
        ),
        'dob' => array(
            'Not Empty' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your birthday',
            ),
        ),
        'gender' => array(
            'Not Empty' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your gender',
            ),
        ),
        'hobby' => array(
            'Not Empty' => array(
                'rule' => 'notBlank',
                'message' => 'Please enter your hobby',
            ),
        )
    );
    public function matchPasswords($data) {
        $isMatched = $data['password'] == $this->data['User']['password_confirmation'];
        if (!$isMatched) $this->invalidate('password_confirmation','Password does not match');
        return $isMatched;
    }
    
    public function beforeSave($options = array()) {
        $this->getIp();
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }
        return true;
    }

    public function getIp() {
        if (!isset($this->data[$this->alias]['id'])) {
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $this->data[$this->alias]['created_ip'] = $_SERVER['REMOTE_ADDR'];
                $this->data[$this->alias]['modified_ip'] = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $this->data[$this->alias]['modified_ip'] = $_SERVER['REMOTE_ADDR'];
            }
        }
        return true;
    }

}
