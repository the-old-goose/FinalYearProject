<?php
defined('BASEPATH') OR exit('No direct script access allowed');

defined('REST_API') OR define('REST_API', "http://localhost/rhfiles/index.php/api/");

/**
 * This class is used to hide the complexity when making GET or POST requests to the REST API  
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class Api_request
{
    /**
     * @var object CodeIgniter instance Singleton
     */
    protected $CI;
   

    public function __construct()
    {
            $this->CI =& get_instance();
    }

    /**
     * This function sends a GET request to the API.Using the parameters passed builds the url string and decodes the JSON response returning an array
     * 
     * @param string $controller The name of the Rest API controller
     * @param string $resource The name of the resource that is being requested
     * @param array  $params The parameters to be passed in the request
     * @var string   $get_string The url string that is built using the REST_API constant
     * @var int      $counter A counter used to build the string correctly
     * @return array $result_array The associative array representation of the decoded JSON response 
     */
    public function get(string $controller,string $resource,$params=array())
    {
        $get_string="";
        $counter = 0;
         
        //foreach parameter passed append param array key '=' equals 
        foreach($params as $param =>$value)
        {
            $counter++;
            $get_string=$get_string.$param.'='.$value; //build get request string

            if($counter< sizeof($params))//if the counter is less than size of params append & for next param
            {
                $get_string.='&';
            }
        }

        //Call api using file get contents using constant , true converts JSON to associative array
        @$result_array=json_decode(file_get_contents(REST_API.$controller.'/'.$resource.'?'.$get_string),TRUE);

        if(!is_null($result_array))//if results
        {
            return $result_array; //return array
        }

       return FALSE;
    }

    /**
     * This function sends a POST request to the API.Using the parameters passed builds the url string and returns TRUE when POST is successful
     * 
     * @param string $controller The name of the Rest API controller
     * @param string $resource The name of the resource that is being requested
     * @param array  $params The parameters to be passed in the request
     * @return bool  True on successful POST request
     */
    public function post($controller,$resource,$params)
    {
        $url=REST_API.$controller.'/'.$resource;
        $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($params)));

        $context  = stream_context_create($options); 
        $post_success = file_get_contents($url, false, $context);
 
        if($post_success)
        {
            return TRUE;
        }

        return FALSE;
    }
        
}
?>
