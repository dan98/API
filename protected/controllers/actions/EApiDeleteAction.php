<?php
class EApiDeleteAction extends CAction{
    
        public $modelName;
    
        public function run($pk)
        {
                $model = CActiveRecord::model($this->modelName)->findByPk($pk);
                
                if(is_null($model))
                    ApiOutput::sendResponse(404, sprintf("Didn't find any <b>%s</b> with ID <b>%s</b>.", $this->modelName, $pk));
                else if($model->user_id != Yii::app()->user->id)
                    ApiOutput::sendResponse(403, 'Forbidden to delete this row.');

                if($model->delete())
                    ApiOutput::sendResponse(200, $model->toJSON());
                else
                    ApiOutput::sendResponse(500, sprintf("Couldn't delete <b>%s</b> with ID <b>%s</b>.", $this->modelName, $pk));
        }
}