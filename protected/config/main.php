<?php

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Rating API',
    'preload' => array('log'),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.controllers.actions.*'
    ),
    'defaultController' => 'site/',
    'modules' => array(
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => false,
            'generatorPaths' => array('bootstrap.gii')
        ),
    ),
    'components' => array(
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true
        ),
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=api',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => 'hypertext',
            'charset' => 'utf8',
            'tablePrefix' => 'api_',
            'behaviors' => array(
                'uid' => array(
                    'class' => 'ext.ERandomKeyBehavior',
                    'dataType' => 'INT', // optionally set your own default property values
                    'digits' => 9, // optionally set your own default property values
                ),
            ),
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'caseSensitive' => false,
            'showScriptName' => false,
            'rules' => array(
                array('site/checkSession', 'pattern' => 'api/checkSession', 'verb' => 'GET'),
                array('site/login', 'pattern' => 'api/login', 'verb' => 'GET'),
                array('site/logout', 'pattern' => 'api/logout', 'verb' => 'GET'),
                // Object
                array('object/list', 'pattern' => 'api/object', 'verb' => 'GET'),
                array('object/view', 'pattern' => 'api/object/<pk:\d+>', 'verb' => 'GET'),
                array('object/update', 'pattern' => 'api/object/<pk:\d+>', 'verb' => 'PUT'),
                array('object/delete', 'pattern' => 'api/object/<pk:\d+>', 'verb' => 'DELETE'),
                array('object/create', 'pattern' => 'api/object', 'verb' => 'POST'),
                // Battles
                array('battle/list', 'pattern' => 'api/battle', 'verb' => 'GET'),
                array('battle/view', 'pattern' => 'api/battle/<pk:\d+>', 'verb' => 'GET'),
                array('battle/create', 'pattern' => 'api/battle', 'verb' => 'POST'),
                array('battle/delete', 'pattern' => 'api/battle/<pk:\d+>', 'verb' => 'DELETE'),
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            ),
        ),
        'session' => array(
            'autoStart' => true,
            'cookieMode' => 'none',
            'useTransparentSessionID' => true,
            'sessionName' => 'session',
            'timeout' => 28800,
        ),
    )
);
