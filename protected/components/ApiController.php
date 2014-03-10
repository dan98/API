<?php
class ApiController extends CController
{
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
                            'controllers'=>array('battle','object'),
                            'users'=>array('@'),
                    ),
                    array('deny',
                            'controllers'=>array('battle','object'),
                            'users'=>array('*'),
                    )
            );
        }
}
