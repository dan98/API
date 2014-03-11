<?php

class ObjectController extends ApiController {

        public function actions()
        {
            return array(
                'view' => array(
                    'class' => 'application.controllers.actions.EApiViewAction',
                    'modelName' => 'Object'
                ),
                'list' => array(
                    'class' => 'application.controllers.actions.EApiListAction',
                    'modelName' => 'Object'
                ),
                'update' => array(
                    'class' => 'application.controllers.actions.EApiUpdateAction',
                    'modelName' => 'Object'
                ),
                'delete' => array(
                    'class' => 'application.controllers.actions.EApiDeleteAction',
                    'modelName' => 'Object'
                )
            );
        }

        public function actionCreate()
        {
            $model = new Object;

            foreach ($_POST as $var => $value){
                if ($model->hasAttribute($var))
                    $model->$var = $value;
                else
                    ApiOutput::sendResponse(500, sprintf('Parameter <b>%s</b> is not allowed for model <b>%s</b>.', $var, $_GET['model']));
            }

            $model->id = Yii::app()->db->uid->getNewId('Object');
            $model->user_id = Yii::app()->user->id;
            $model->score = 1400;
            $model->wins = $model->loses = 0;

            if ($model->save())
                ApiOutput::sendResponse(200, $model->findByPk($model->id)->toJSON());

            else {
                $msg = "Couldn't create a <b>Object</b>. ";
                foreach ($model->errors as $attribute => $attr_errors) {
                    $msg .= "Attribute: $attribute";
                    foreach ($attr_errors as $attr_error) {
                        $msg .= " : $attr_error";
                    }
                }
                ApiOutput::sendResponse(500, $msg);
            }
        }
}
