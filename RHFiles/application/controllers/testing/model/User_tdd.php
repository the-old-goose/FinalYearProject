<?php

defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * This Controller carries out the tests for building the category api
 * TEST PATH:<testing/model/user_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class User_tdd extends CI_Controller
{
    /***** TEST CASE *****/

       public $user_details =array(
        'username'=>"#usertest",
        'email'=>"test@email.com",
        'encrypt_pass'=>"",
        'first_name'=>"testfirst",
        'last_name'=>"testlast",
        'location_id'=>1,
        'mobile'=>"07000000000",
        'auth_code'=>"X",
        'read_id'=>TRUE, 
        'year'=>"First");

    public $password ="password";

    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index(){
        $this->user_details['auth_code']=$this->verify_user->generate_code();
        $this->user_details['encrypt_pass']=password_hash($this->password,PASSWORD_BCRYPT);

        $test_name="Checking the test Case does not exist prior to registering";
        $test0=$this->checkingUserDoesNotExist($this->user_details['username']);
        $expected_result=FALSE;
        echo $this->unit->run($test0,$expected_result,$test_name,$notes="Will fail if username alread exists in table");

        $test_name="Checking test account can be registered ";
        $test1_user_id=$this->checkingUserCanBeCreated($this->user_details);
        $expected_result=TRUE; 
        echo $this->unit->run($test1_user_id,$expected_result,$test_name,$notes="");

        $test_name="Checking created user confimed bit set to 0 and therefore cannot login";
        $test2=$this->checkingNonValidatedUser($test1_user_id);
        $expected_result=FALSE; 
        echo $this->unit->run($test2,$expected_result,$test_name,$notes="Not yet verified");
        
        $test_name="Verifying created users verfication code";
        $test3=$this->assertUserCodeCanBeVerified();
        $expected_result=TRUE; 
        echo $this->unit->run($test3,$expected_result,$test_name,$notes="Verifies the created user");
         
        $test_name="Checking created users confirmed bit set to 1 ";
        $test4=$this->checkingNonValidatedUser($test1_user_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test4,$expected_result,$test_name,$notes="confirmed bit should be true after verification");

        $test_name="Checking created user can now login";
        $test5=$this->checkingCreatedAccountCanLogin();
        $expected_result=TRUE; 
        echo $this->unit->run($test5,$expected_result,$test_name,$notes="User now authenticated ");
        
        $test_name="Remove the registered user for test re-run";
        $test6=$this->cleanupCreatedAccount($test1_user_id);
        $expected_result=TRUE;
        echo $this->unit->run($test6,$expected_result,$test_name,$notes);
        
    }

    /**
     * This test checks that the test case does not already exist prior to testing
     */
    private function checkingUserDoesNotExist($username)
    {
        return !$success =$this->user_model->check_username_exists($username);
    }
    
    /**
     * This test checks that creating a user with valid details returns true when inserting the data
     * into the database.
     */
    private function checkingUserCanBeCreated(array $user_details)
    {
        return  $success= $this->user_model->create_user_and_profile($user_details['username'],$user_details['email'],$user_details['encrypt_pass'],$user_details['first_name'],$user_details['last_name'],$user_details['location_id'],$user_details['mobile'],$user_details['auth_code'],$user_details['read_id']); 
    }

    private function checkingNonValidatedUser(int $user_id)
    {
        $success= $this->user_model->read_if_user_valid($user_id);
        return  $success= $this->user_model->read_if_user_valid($user_id);
    }

    private function assertUserCodeCanBeVerified()
    {   
        return  $success= $this->user_model->validate_user($this->user_details['auth_code']);
    }

    
    private function checkingCreatedAccountCanLogin()
    {
        $success=$this->user_model->login($this->user_details['email'],$this->password);
        return ($success['username']===$this->user_details['username']);
    }


    /**
     * This function removes the account created in the test run to allow test re-runs.
     */
    private function cleanupCreatedAccount($user_id)
    {
        return $this->user_model->delete_user($user_id);
    }

}

?>