<?php

class UserIdentity extends CUserIdentity {

        private $_id;

        public function authenticate() {
            $user = CActiveRecord::model('User')->findByPk($this->username);

            if ($user === null)
                $this->errorCode = self::ERROR_USERNAME_INVALID;
            else
            if (!$user->validatePassword($this->password))
                $this->errorCode = self::ERROR_PASSWORD_INVALID;
            else {
                $this->setState('user_id', $user->id);
                $this->_id = $user->id;
                $this->username = $user->id;
                $this->errorCode = self::ERROR_NONE;
                Yii::app()->user->login($this);
            }
            return $this->errorCode == self::ERROR_NONE;
        }

        public function getId() {
            return $this->_id;
        }

}
