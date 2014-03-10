<?php
define("BASE_URL", "http://api:8888/api");
session_start();
class RatingClient
{
        private $consumer_id;
        private $consumer_secret;
        private $session;
        private $base_url;
        private $headers;
        
        /**
         * RatingClient constructor - creates an instance of the RatingAPI Client.
         */
        public function __construct($consumer_id, $consumer_secret)
        {
                $this->consumer_id = $consumer_id;
                $this->consumer_secret = $consumer_secret;
                
                // Create header for http_* methods
                $this->headers = array();
                if(!empty($this->consumer_id))  $this->headers['x-consumer-id'] = $this->consumer_id;
                if(!empty($this->consumer_secret)) $this->headers['x-consumer-secret'] = $this->consumer_secret;
                $this->base_url = BASE_URL;
                
                $this->_login();
        }
        
        private function _login()
        {
            $mc = new Memcache(); 
            $mc->addServer("localhost", 11211);
            if($mc->get('ratingapi-session') == null)
            {
                $result = $this->http_get("/login", null, true);
                if($result['session'] != '' && $result['session'] != null)
                {
                    $this->session = $result['session'];
                    $mc->set('ratingapi-session', $this->session);
                }else
                    throw new RatingException('Session not obtained');
            }
            else
            {
                $this->session = $mc->get('ratingapi-session');
                $r = new HttpRequest($this->base_url.'/checkSession/?session='.$this->session, HttpRequest::METH_GET);
                $r->send();
                if($r->getResponseHeader("Location") == $this->base_url."/login")
                {
                    $mc->set('ratingapi-session', null);
                    $this->_login();
                }
            }
        }
        
        public function logout()
        {
            
            $mc = new Memcached(); 
            $mc->addServer("localhost", 11211);
            unset($this->session);
            $this->http_get("/logout");
            $mc->delete('ratingapi-session');
        }
        
        public function isLogged()
        {
            if($this->session != null && $this->session != '')
                return true;
            return false;
        }
        
        
        // API  End-points.
        // Objects
        public function listObjects($options = null)
        {
                return $this->listObject($this->http_get("/object/", $options));
        }
        
        public function getObject($id)
        {
                return $this->singleObject($this->http_get("/object/".$id));
        }

        public function createObject($options)
        {
                return $this->singleObject($this->http_post("/object", array(), $this->encodeArray($options)));
        }

        public function deleteObject($id)
        {
                return $this->singleObject($this->http_delete("/object/".$id));
        }     

        public function renameObject($id, $name)
        {
                return $this->singleObject($this->http_put("/object/".$id, array(), $this->encodeArray(array("name" => $name))));
        }
        
        // Battles
        public function listBattles($options = null)
        {
                return $this->listBattle($this->http_get("/battle", $options));
        }
        
        public function getBattle($id)
        {
                return $this->singleBattle($this->http_get("/battle/".$id));
        }

        public function createBattle($winner,$loser)
        {
                if(is_object($winner))
                    $winner = $winner->id;
                if(is_object($loser))
                    $loser = $loser->id;
                    
                return $this->singleBattle($this->http_post("/battle", array(), $this->encodeArray(array("winner" => $winner,"loser"=>$loser))));
        }
        
        
        //Result handling and creating objects.
        private function singleObject($result)
        {
                $result = $this->listObject($result);
                return $result[0];
        }

        private function listObject($result)
        {
                $created = array();
                foreach ($result as $object)
                {
                    $created[] = new RatingObject($object, $this);
                }
                
                return $created;
        }
        
        //Result handling and creating objects.
        private function singleBattle($result)
        {
                $result = $this->listBattle($result);
                return $result[0];
        }

        private function listBattle($result)
        {
                $created = array();
                
                foreach ($result as $key => $battle)
                {
                    $created[] = new RatingBattle($battle, $this);
                }
                
                return $created;
        }

        // Requests handling
        private function http_get($url, $args = array(), $credentials = false)
        {
                $full_url = $this->base_url . $url;
                
                $args['session'] = $this->session;
                
                // Add encoded args to the full_url
                if(!$credentials)
                    $full_url .= $this->encodeArray($args, true);

                $r = new HttpRequest($full_url, HttpRequest::METH_GET);
                if($credentials){
                    $r->setHeaders($this->headers);
                }
                $r->send();
                
                // Get status and body
                $rc = $r->getResponseCode();
                $response = $r->getResponseBody();
                // Decode and check for errors
                $response = json_decode($response, true);
                RatingException::check($response);
                // If no exceptions trowed return the body
                return $response;
        }


        private function http_delete($url, $args = array(), $body = NULL)
        {
                $full_url = $this->base_url.$url;
                
                $args['session'] = $this->session;
                
                // Add encoded args to the full_url
                $full_url .= $this->encodeArray($args, true);

                $r = new HttpRequest($full_url, HttpRequest::METH_DELETE);
                $r->setBody($body);
                $r->send();
                
                // Get status and body
                $rc = $r->getResponseCode();
                $response = $r->getResponseBody();
                
                // Decode and check for errors
                $response = json_decode($response, true);
                RatingException::check($response);
                
                // If no exceptions trowed return the body
                return $response;
        }


        private function http_post($url, $args = array(), $body = NULL)
        {
                $full_url = $this->base_url.$url;
                
                $args['session'] = $this->session;
                
                // Add encoded args to the full_url
                $full_url .= $this->encodeArray($args, true);

                $r = new HttpRequest($full_url, HttpRequest::METH_POST);
                
                $r->setBody($body);
                $r->send();
                
                // Get status and body
                $rc = $r->getResponseCode();
                $response = $r->getResponseBody();
                
                // Decode and check for errors
                $response = json_decode($response, true);
                RatingException::check($response);
                
                // If no exceptions throwed return the body
                return $response;
        }
        
        private function http_put($url, $args = array(), $body = NULL)
        {
                $full_url = $this->base_url.$url;
                
                $args['session'] = $this->session;
                
                // Add encoded args to the full_url
                $full_url .= $this->encodeArray($args, true);

                $r = new HttpRequest($full_url, HttpRequest::METH_PUT);
                $r->setHeaders($this->headers);
                $r->setPutData($body);
                $r->send();
                
                // Get status and body
                $rc = $r->getResponseCode();
                $response = $r->getResponseBody();
                
                // Decode and check for errors
                $response = json_decode($response, true);
                RatingException::check($response);
                
                // If no exceptions throwed return the body
                return $response;
        }

        
        // Method used to encode an array to string.
        // Ex: array('name'=>'josh', 'created_time'=>'now()') to "name=josh&created_time=now()"
        private function encodeArray($data, $for_get = false)
        {
                if ($data == NULL){
                        $data = array();
                }
                
                $first = true;
                $result = "";
                foreach ($data as $key => $value){
                    
                        if ($first) {
                                if ($for_get){
                                        $result .= "?";
                                }
                                $result .= urlencode($key) . "=" . urlencode($value);
                                $first = false;
                        }
                        else{
                                $result .= "&" . urlencode($key) . "=" . urlencode($value);
                        }
                }
                return $result;
        }

}


/**
 * RatingException class - defines a Rating specific error from either the client or the server
 */
class RatingException extends Exception
{
        public static function check($result)
        {

                if(isset($result["error"]))
                {
                        throw new RatingException("<b>{$result["error"]["status"]}</b> [".self::getStatusCodeMessage($result["error"]["status"])."] : ".$result["error"]["message"], $result["error"]["status"]);
                }
                return true;
        }
        
        static function getStatusCodeMessage($status)
        {
            $codes = array(
                100 => 'Continue',
                101 => 'Switching Protocols',
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',
                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                306 => '(Unused)',
                307 => 'Temporary Redirect',
                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported'
            );
            return (isset($codes[$status])) ? $codes[$status] : '';
        }
}



/**
 * RatingObject class - the base class for objects instances returned.
 */
class RatingObject {

        private $id;
        private $name;
        private $image_url;
        private $score;
        private $wins;
        private $losses;
        private $updated_time;
        private $created_time;
        private $client;
        
        public function __construct($data = NULL, $client = NULL)
        {
                if($data != NULL)
                {
                    foreach ($data as $key => $value)
                    {
                            if (property_exists($this, $key))
                            {
                                    $this->$key = $value;
                            }
                    }
                }
                
                $this->client = $client;
        }
        
        public function __get($property)
        {
                if (property_exists($this, $property))
                    return $this->$property;
        }


        public function delete($client = NULL)
        {
                if($client != NULL)
                    return $client->deleteObject($this->id);
                else
                    return $this->client->deleteObject($this->id);
        }


        public function rename($name, $client = NULL)
        {
                if($client != NULL)
                {  
                    $obj = $client->renameObject($this->id, $name);
                    if($obj != null)
                    {
                       return $obj;
                    }
                }
                else
                {
                    $obj = $this->client->renameObject($this->id, $name);
                    if($obj != null)
                    {
                        return $obj;
                    }
                }
                return false;
                
        }
        
        public function battles($options, $client = NULL)
        {
            $options['object'] = $this->id;
            if($client != NULL)
                return $client->listBattles($options, $this->id);
            else
                return $this->client->listBattles($options, $this->id);
        }

}

class RatingBattle {

        private $id;
        private $winner;
        private $winner_score;
        private $loser;
        private $loser_score;
        private $created_time;
        private $client;
        
        public function __construct($data = NULL, $client = NULL)
        {
                if($data != NULL)
                {
                    foreach ($data as $key => $value)
                    {
                            if($key == 'winner' || $key == 'loser')
                            {
                                $this->$key = new RatingObject($value,$client);
                            }
                            else
                            if (property_exists($this, $key))
                            {
                                    $this->$key = $value;
                            }
                    }
                }
                
                $this->client = $client;
                
        }
        public function __get($property)
        {
                if (property_exists($this, $property))
                    return $this->$property;
        }
}

function expected($Rb, $Ra) {
	return 1/(1 + pow(10, ($Rb-$Ra)/400));
}