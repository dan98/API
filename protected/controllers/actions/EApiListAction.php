<?php
class EApiListAction extends CAction{
    
    public $modelName;
    
    public function run()
    {
            $array = array();
            $params = array();
            
            // Main condition
            $condition = 'user_id=:user_id';
            $params[':user_id'] = Yii::app()->user->id;
            
            // Order ASC, DESC and random
            if(isset($_GET['order']) && $_GET['order'] != '')
            {
                $array['order'] = $_GET['order'];
                if($_GET['order'] == 'random')
                {
                    $array['order'] = 'rand()';
                    $array['limit'] = 2;
                }
            }
            
            // Filter by object
            if(isset($_GET['object']) && $_GET['object'] != '')
            {
                $condition .= ' AND (winner=:object_id OR loser=:object_id)';
                $params[':object_id'] = $_GET['object'];
            }
            
            // LIMIT Option
            if(isset($_GET['limit']) && $_GET['limit'] != '')
            {
                $array['limit'] = $_GET['limit'];
            }
            
            // End array
            $array['condition'] = $condition;
            $array['params'] = $params;
            
            $models = CActiveRecord::model($this->modelName)->findAll($array);

            $total = '';
            foreach($models as $model)
            {
                $total .= $model->toJSON();
            }
            $total = str_replace("][", ",", $total);

            ApiOutput::sendResponse(200, $total);
    }
}