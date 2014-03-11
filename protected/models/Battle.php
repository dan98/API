<?php

class Battle extends CActiveRecord {

        public function tableName() {
            return 'api_battle';
        }

        public function behaviors() {
            return array(
                'CTimestampBehavior' => array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'created_time',
                    'timestampExpression' => new CDbExpression('NOW()')
                ),
                'EJsonBehavior' => array(
                    'class' => 'ext.EJsonBehavior'
                ),
            );
        }

        public function rules() {
            return array(
                array('user_id, winner, loser', 'required'),
                array('user_id, winner, loser', 'numerical', 'integerOnly' => true)
            );
        }

        public function relations() {
            return array(
                'winner' => array(self::BELONGS_TO, 'Object', 'winner'),
                'loser' => array(self::BELONGS_TO, 'Object', 'loser')
            );
        }

        public static function model($className = __CLASS__) {
            return parent::model($className);
        }

}
