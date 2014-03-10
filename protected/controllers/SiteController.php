<?php

class SiteController extends CController {

    public $layout = 'column1';
    public $breadcrumbs = array();
    
    public function filters()
    {
        return array(
                'accessControl', // perform access control for CRUD operations
        );
    }
        
    public function accessRules()
    {
        return array(
                array('allow',
                        'actions'=>array('error','page','register','login','index'),
                        'users'=>array('*'),
                ),
                array('allow',
                        'actions'=>array('logout','checkSession'),
                        'users'=>array('@'),
                ),
                array('deny',
                        'users'=>array('*')
                )
        );
    }
    public function actions() {
        return array(
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                ApiOutput::sendResponse($error['code'], $error['message'].' '.$error['file'].'['.$error['line'].']');
        }
    }

    public function actionRegister() {
        $model = new RegisterForm;

        if (isset($_POST['RegisterForm'])) {
            $model->attributes = $_POST['RegisterForm'];

            if ($model->validate()) {
                $user = new User;

                // Generate random id and a random secre
                $user->id = Yii::app()->db->uid->getNewId('User');
                $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $randomString = '';
                for ($i = 0; $i < 64; $i++) {
                    $randomString .= $characters[rand(0, strlen($characters) - 1)];
                }

                $user->consumer_secret = $randomString;
                $user->email = $model->email;
                $user->name = $model->name;
                $user->description = $model->description;

                if ($user->save()) {
                    Yii::app()->user->setFlash('register', "Thank you for registering on our service. Here are your credentials: consumer_id={$user->id} and consumer_secret={$user->consumer_secret} .");
                } else
                    throw new CHttpException('500', 'Error registering this user');
            }
        }
        $this->render('register', array('model' => $model));
    }

    public function actionLogin() {

        // Check if credentials are not empty
        if (empty($_SERVER['HTTP_X_CONSUMER_ID']) || empty($_SERVER['HTTP_X_CONSUMER_SECRET']))
            ApiOutput::sendResponse(401, 'Id or secret is empty!');

        // Parse id and secret
        $consumer_id = $_SERVER['HTTP_X_CONSUMER_ID'];
        $consumer_secret = $_SERVER['HTTP_X_CONSUMER_SECRET'];

        // Construct the useridentity class
        $user = new UserIdentity($consumer_id, $consumer_secret);

        // Try to authenticate
        if ($user->authenticate()) {
            // If no error pass return the session
            ApiOutput::sendResponse(200, CJSON::encode(
                            array(
                                'session' => Yii::app()->session->getSessionID()
                            )
                    )
            );
        } else
            ApiOutput::sendResponse(401);
    }

    public function actionLogout() {
        if (Yii::app()->session->destroySession(Yii::app()->session->getSessionID())) {
            ApiOutput::sendResponse(200);
        } else {
            ApiOutput::sendResponse(500);
        }
    }
    
    public function actionCheckSession() {
        
    }

    public function actionIndex() {
        $this->render('pages/home');
    }

}
