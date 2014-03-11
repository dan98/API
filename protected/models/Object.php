<?php

class Object extends CActiveRecord {

        public function tableName() {
            return 'api_object';
        }

        public function behaviors() {
            return array(
                'CTimestampBehavior' => array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'created_time',
                    'updateAttribute' => 'updated_time',
                    'timestampExpression' => new CDbExpression('NOW()'),
                    'setUpdateOnCreate' => true
                ),
                'EJsonBehavior' => array(
                    'class' => 'ext.EJsonBehavior'
                ),
            );
        }

        public function rules() {
            return array(
                array('user_id, name', 'required'),
                array('name', 'length', 'max' => 255, 'min' => 3)
            );
        }

        public static function model($className = __CLASS__) {
            return parent::model($className);
        }

}
