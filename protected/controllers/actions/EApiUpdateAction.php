<?php
class EApiUpdateAction extends CAction{
    
    public $modelName;
    
    public function run($pk)
    {
            parse_str(file_get_contents('php://input'), $put_vars);

            
            $model = CActiveRecord::model($this->modelName)->findByPk($pk);

            if(is_null($model))
                ApiOutput::sendResponse(400, sprintf("Didn't find any model <b>%s</b> with ID <b>%s</b>.",$this->modelName, $pk) );
            else if($model->user_id != Yii::app()->user->id)
                ApiOutput::sendResponse(403, 'Forbidden to update this row.');

            foreach($put_vars as $var=>$value)
            {
                if($model->hasAttribute($var))
                {
                    $model->$var = $value;
                }
                else
                {
                    ApiOutput::sendResponse(500, sprintf('Parameter <b>%s</b> is not allowed for model <b>%s</b>', $var, $this->modelName) );
                }
            }
            
            // Rewrite user_id
            $model->user_id = Yii::app()->user->id;

            if($model->save())
            {
                ApiOutput::sendResponse(200, $model->findByPk($model->id)->toJSON());
            }
            else
            {
                $msg = sprintf("Couldn't update model <b>%s</b>. ", $this->modelName);
                foreach($model->errors as $attribute=>$attr_errors)
                {
                    $msg .= "Attribute: $attribute";
                    foreach($attr_errors as $attr_error)
                    {
                        $msg .= " : $attr_error";
                    }
                }
                ApiOutput::sendResponse(500, $msg);
            }
    }
}