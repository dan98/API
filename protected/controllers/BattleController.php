<?php
class BattleController extends ApiController{
    
    public function filters()
    {
        return array( 'accessControl' ); // perform access control for CRUD operations
    }
 
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated users to access all actions
                'users'=>array('@'),
            ),
            array('deny'),
        );
    }
    
    public function actions()
    {    
        return array(
            'view' => array(
                'class' => 'application.controllers.actions.EApiViewAction',
                'modelName' => 'Battle'
            ),
            'list' => array(
                'class' => 'application.controllers.actions.EApiListAction',
                'modelName' => 'Battle'
            ),
            'delete' => array(
                'class' => 'application.controllers.actions.EApiDeleteAction',
                'modelName' => 'Battle'
            )
        );
    }
    
    
    public function actionCreate()
    {
            $model = new Battle;
            $winner = Object::model()->findByPk($_POST['winner']);
            $loser = Object::model()->findByPk($_POST['loser']);
            
            if($winner == null || $loser == null)
                ApiOutput::sendResponse(404, 'The winner or the loser object doesn\'t exist.');

            if($winner->user_id == Yii::app()->user->id && $loser->user_id == Yii::app()->user->id)
            {
                $k = 24;
                $winner_expected = $this->_expected($loser->score, $winner->score);
                $winner->score = $winner->score + $k * (1-$winner_expected);
                $model->winner_score = $winner->score;
                $winner->wins += 1;
                $winner->save();

                $loser_expected = $this->_expected($winner->score, $loser->score);
                $loser->score = $loser->score + $k * (0-$loser_expected);
                $model->loser_score = $loser->score;
                $loser->losses += 1;
                $loser->save();
            }
            else
                ApiOutput::sendResponse(403, 'Forbidden to operate with the winner or the loser object.');


            foreach($_POST as $var=>$value)
            {
                if($model->hasAttribute($var))
                {
                    $model->$var = $value;
                }else
                {
                    ApiOutput::sendResponse(500, sprintf('Parameter <b>%s</b> is not allowed for model <b>%s</b>.', $var, $_GET['model']) );
                }
            }
            
            $model->id = Yii::app()->db->uid->getNewId('Battle');
            $model->user_id =  Yii::app()->user->id;

            if($model->save())
            {
                ApiOutput::sendResponse(200, $model->findByPk($model->id)->toJSON());
            }
            else
            {
                $msg = sprintf("Couldn't create <b>Battle</b>. ");
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

    private function _expected($Rb, $Ra){
        return 1/(1 + pow(10, ($Rb-$Ra)/400));
    }

    
}