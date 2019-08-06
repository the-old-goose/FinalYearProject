<?php
use Restserver\Libraries\REST_Controller;


defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This Controller carries out the logic for the User API. It extends the Rest Controller handling HTTP GET and POST requests by returning
 * a JSON response based on the resource requested.
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class User extends REST_Controller {

     /**
     * Handles GET requests for logging a user in.
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $email The users email
     * @var $enc_pass The users password
     * @var $user The users details found using the model
     * @return object A JSON object containing the users details if login successful
     */
    function login_get()
    {   
        $email= $this->get('email');
        $enc_pass=$this->get('pass');

        $user = $this->user_model->login($email,$enc_pass);
    
        if($user)
        {
            $this->response($user, 200); 
        }
 
        else
        {
            $this->response(NULL, 404);//Errors to be handled application side
        }
    }

     /**
     * Handles GET requests for validating a users authentication code.
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $code The users verification code to be authenticated
     * @var $validation_successful A boolean stating if the code account could be validated
     * @return object A JSON object containing a boolean stating if the validation was successful
     */
    function validate_user_code_get()
    {
        $code= $this->get('code');

        $validation_successful = $this->user_model->validate_user($code);

        if($validation_successful)
        {
            $this->response($validation_successful, 200); 
        }
 
        else
        {
            $this->response(NULL, 404);//Errors to be handled application side
        }

    }
    
    /**
     * Handles POST requests for registering a user using the model to insert the data.
     * @return bool True on success
     */
    function register_post()
    {
        $username=$this->input->post('username');
        $email=$this->input->post('email');
        $enc_pass=$this->input->post('password');
        $first_name=$this->input->post('first_name');
        $last_name=$this->input->post('last_name');
        $location_id=$this->input->post('location_id');
        $mobile=$this->input->post('mobile');
        $auth_code=$this->input->post('auth_code');
        $success=$this->user_model->create_user_and_profile($username,$email,$enc_pass,$first_name,$last_name,$location_id,$mobile,$auth_code,FALSE);

        $this->response($success);
    }
}