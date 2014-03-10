<?php
class Object extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'api_object';
	}
        
        public function behaviors(){
                return array(
                        'CTimestampBehavior' => array(
                                'class' => 'zii.behaviors.CTimestampBehavior',
                                'createAttribute' => 'created_time',
                                'updateAttribute' => 'updated_time',
                                'timestampExpression' => new CDbExpression('NOW()'),
                                'setUpdateOnCreate' => true
                        ),
                        'EJsonBehavior'=>array(
                                'class'=>'ext.EJsonBehavior'
                        ),
                );
        }
        
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, name', 'required'),
			array('name', 'length', 'max'=>255, 'min'=>3)
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
