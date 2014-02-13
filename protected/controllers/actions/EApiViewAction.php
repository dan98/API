<?php
class EApiViewAction extends CAction{
    
    public $modelName;
    
    public function run($pk)
    {
            if(!isset($pk))
                ApiOutput::sendResponse(500, 'Parameter <b>id</b> is missing.' );
            
            $model = CActiveRecord::model($this->modelName)->findByPk($pk);
            
            if(is_null($model))
                ApiOutput::sendResponse(404, $this->modelName . " with id {$pk} not found.");
            else if($model->user_id != Yii::app()->user->id)
                ApiOutput::sendResponse(403, 'Forbidden to access this row.'.Yii::app()->user->id.'d'.Yii::app()->session->getSessionID().'v.'.session_save_path());
            else
                ApiOutput::sendResponse(200, $model->toJSON());
    }
}