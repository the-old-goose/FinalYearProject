<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the skill api
 * TEST PATH:<testing/api/user_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class User_tdd extends CI_Controller
{
     /***** TEST CASE *****/
     public $new_user=array(
        'username'=>'@apitest',
        'email'=>'apitest@site.com',
        'password'=>'password',
        'first_name'=>'test',
        'last_name'=>'test',
        'location_id'=>1,
        'mobile'=>'07999999999',
        'auth_code'=>'123'
     );

     /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index()
    {
        //save plain text pass for logging in before its hash is stored 
        $plain_text_pass=$this->new_user['password'];
        //Decrypt pass and generate authentication code for user
        $this->new_user['password']=password_hash($this->new_user['password'],PASSWORD_BCRYPT);
        $this->new_user['auth_code']=$this->verify_user->generate_code();

        $test_name="Checking API can be used to register a user";
        $test0=$this->checkingApiCanRegisterUser($this->new_user);
        $expected_result=NULL;
        echo $this->unit->run($test0,$expected_result,$test_name,$notes="Registers test case, HTTP POST so no JSON returned");

        $test_name="Checking API can be used to validate authentication code";
        $test1=$this->checkingApiCanAuthenticateCode($this->new_user['auth_code']);
        $expected_result=TRUE;
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Will fail if test case unsuccessfully registered");

        $test_name="Checking API can be used to login  user";
        $test2_login_details=$this->checkingApiCanLoginUser($this->new_user['email'],$plain_text_pass);
        $expected_result=TRUE;
        echo $this->unit->run(is_array($test2_login_details),$expected_result,$test_name,$notes="Will fail if test case unsuccessfully registerd");
        
        $test_name="Remove the registered user for test re-run";
        $test3=$this->cleanupCreatedAccount($test2_login_details['user_id']);
        $expected_result=TRUE;
        echo $this->unit->run($test3,$expected_result,$test_name,$notes);
    }

    /**
     * This function registers a user setting up the other tests by using the API
     */
    private function checkingApiCanRegisterUser(array $new_user)
    {
        $register_success=$this->api_request->post('user','register',$new_user);
    }

    /**
     * Checks to see if the created user can be logged in using the api
     * 
     * @return mixed The users login details, or FALSE if account not found
     */
    private function checkingApiCanLoginUser($email,$pass)
    {
        $params=array('email'=>$email,'pass'=>$pass);
        $login_details=$this->api_request->get("user","login",$params);
        if($login_details['email']==$email)
        {
            return $login_details;
        }
        
        return FALSE;
        
    }

    /**
     * Validates the created account using the api 
     * 
     * @return boolean true if account validation succussful, false on failure.
     */
    private function checkingApiCanAuthenticateCode($auth_code)
    {
        return $this->api_request->get("user","validate_user_code",$params=array('code'=>$auth_code));  
    }

    
    private function cleanupCreatedAccount($user_id)
    {
        return $this->user_model->delete_user($user_id);
    }
    
}

?>