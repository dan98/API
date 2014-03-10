<?php
abstract class ApiOutput{
    
        static public function sendResponse($status = 200, $body = '', $content_type = 'text/json')
        {
            $status_header = 'HTTP/1.1 ' . $status;
            header($status_header);
            header('Content-type: ' . $content_type);

            // Default error messages
            if($body == '')
            {
                switch($status)
                {
                    case 401:
                        $body = 'Authentication problem. You need to be authorized to access this end-point.';
                        break;
                    case 404:
                        $body = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                        break;
                    case 500:
                        $body = 'The server encountered an error processing your request.';
                        break;
                    case 501:
                        $body = 'The requested method is not implemented.';
                        break;
                }
            }

            // If error
            if($status < 200 || $status >= 300)
            {
                $response = array('error' =>array('message' => $body, 'status' => $status));
                $body = CJSON::encode($response);
            }

            // If not error and body exists
            if($body != '')
            {
                echo $body;
                exit;
            }
        }
}