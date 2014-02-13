<?php
class ApiController extends CController
{

        public function actionCreate()
        {

                switch($_GET['model'])
                {
                    case 'object':
                        $model = new Object;
                        break;
                    case 'battle':
                        $model = new Battle;
                        $winner = Object::model()->findByPk($_POST['winner']);
                        $loser = Object::model()->findByPk($_POST['loser']);
                        if($winner == null || $loser == null)
                            ApiOutput::sendResponse(404, 'The winner or the loser object doesn\'t exist.');

                        if($winner->user_id == $this->consumer_id && $loser->user_id == $this->consumer_id)
                        {
                            $k = 24;
                            $winner_expected = $this->_expected($loser->score, $winner->score);
                            $winner->score = $winner->score + $k * (1-$winner_expected);
                            $winner->wins += 1;
                            $winner->save();

                            $loser_expected = $this->_expected($winner->score, $loser->score);
                            $loser->score = $loser->score + $k * (0-$loser_expected);
                            $loser->losses += 1;
                            $loser->save();
                        }
                        else
                            ApiOutput::sendResponse(403, 'Forbidden to operate with the winner or the loser object.');
                        break;
                    default:
                        ApiOutput::sendResponse(501, sprintf('Mode <b>create</b> is not implemented for model <b>%s</b>.',$_GET['model']) );
                        exit;
                }

                foreach($_POST as $var=>$value){
                    if($model->hasAttribute($var)){
                        $model->$var = $value;
                    }else{
                        ApiOutput::sendResponse(500, sprintf('Parameter <b>%s</b> is not allowed for model <b>%s</b>.', $var, $_GET['model']) );
                    }
                }

                $model->user_id = $this->consumer_id;

                if($model->save()) {
                    ApiOutput::sendResponse(200, $model->findByPk($model->id)->toJSON());
                }else{
                    $msg = sprintf("Couldn't create model <b>%s</b>. ", $_GET['model']);
                    foreach($model->errors as $attribute=>$attr_errors){
                        $msg .= "Attribute: $attribute";
                        foreach($attr_errors as $attr_error){
                            $msg .= " : $attr_error";
                        }
                    }
                    ApiOutput::sendResponse(500, $msg);
                }

                var_dump($_REQUEST);
        }
        
        private function _expected($Rb, $Ra){
            return 1/(1 + pow(10, ($Rb-$Ra)/400));
        }
    
}
