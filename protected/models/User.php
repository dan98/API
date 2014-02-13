<?php

class User extends CActiveRecord
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return '{{user}}';
	}
        
        public function behaviors(){
                return array(
                        'CTimestampBehavior' => array(
                                'class' => 'zii.behaviors.CTimestampBehavior',
                                'createAttribute' => 'created_time',
                                'timestampExpression' => new CDbExpression('NOW()')
                        )
                );
        }
        
        public function validatePassword($consumer_secret)
	{
		return $consumer_secret===$this->consumer_secret;
	}

}
