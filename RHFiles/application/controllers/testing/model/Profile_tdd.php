<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the category api
 * TEST PATH:<testing/model/profile_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Profile_tdd extends CI_Controller
{
    /***** TEST CASE *****/

    public $user_details =array(
        'username'=>"testusername",
        'email'=>"test@email.com",
        'encrypt_pass'=>"",
        'first_name'=>"testfirst",
        'last_name'=>"testlast",
        'location_id'=>1,
        'mobile'=>"07000000000",
        'auth_code'=>"X",
        'read_id'=>TRUE, 
        'year'=>"First",
        'description'=>"desc",
        'score'=>99999);

    public $password ="password";

    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index()
    {
        $this->user_details['encrypt_pass']=password_hash($this->password,PASSWORD_BCRYPT);

        $test_name="Creating a user checking profile is created";
        $test0_profile_id=$this->checkingProfileCanBeCreated($this->user_details);
        $expected_result=TRUE;
        echo $this->unit->run(is_numeric($test0_profile_id),$expected_result,$test_name,$notes="True if user id is returned");

        $test_name="Checking fields can be read from the created profile ";
        $test1=$this->checkingCreatedProfileCanBeRead($test0_profile_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Asserts the profile from db matches test case");

        $test_name="Updating the created profiles details";
        $this->user_details['first_name']="updatedfirstname";
        $test2=$this->checkingProfileCanBeUpdated($test0_profile_id,$this->user_details);
        $expected_result=TRUE; 
        echo $this->unit->run($test2,$expected_result,$test_name,$notes="");

        $test_name="Checking the top profiles function works checking profile has the highest score";
        $test3=$this->checkProfileIsTopProfile($test0_profile_id,$no_of_profiles=1);
        $expected_result=TRUE; 
        echo $this->unit->run($test3,$expected_result,$test_name,$notes="Test profile score set to 99999 so potential it may not be highest score");
        
        $test_name="Check profile can be found using searching";
        $test4=$this->checkProfileCanBeFoundUsingSearch($search_term=$this->user_details['last_name'],$test0_profile_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test4,$expected_result,$test_name,$notes="Profile must be found within first 10 results as limt 10 set");

        $test_name="Deleting the created users and profile";
        $test5=$this->cleanupCreatedProfile($test0_profile_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test5,$expected_result,$test_name,$notes="Deletes the created profile for reruns");
        
    }

    /**
     * ####
     */
    private function checkingProfileCanBeCreated($user_details)
    {
        return $success =$this->user_model->create_user_and_profile($user_details['username'],$user_details['email'],$user_details['encrypt_pass'],$user_details['first_name'],$user_details['last_name'],$user_details['location_id'],$user_details['mobile'],$user_details['auth_code'],$user_details['read_id']);
    }
    
    /**
     * ####
     */
    private function checkingCreatedProfileCanBeRead($profile_id)
    {
          $profile= $this->profile_model->read_profile($profile_id);
          
          return ($profile['first_name']==$this->user_details['first_name'] &&$profile['last_name']==$this->user_details['last_name']);
    }

    /**
     * ####
     */
    private function checkingProfileCanBeUpdated($profile_id,$attributes)
    {
       return $success=$this->profile_model->update_profile($profile_id,$attributes);
    }

    /**
     * ####
     */
    private function checkProfileIsTopProfile($user_id,$no_of_profiles=1)
    {
        $success=$this->profile_model->set_score($user_id,$this->user_details['score']);

        $top_profiles=$this->profile_model->read_highest_rank_profiles($no_of_profiles);

        return ($top_profiles[0]['username']==$this->user_details['username']) && $success;
        
    }

    /**
     * ####
     */
    private function checkProfileCanBeFoundUsingSearch($search_term,$user_id)
    {
        $profiles_found=$this->profile_model->read_profiles_using_search($search_term,$limit=10);
        foreach($profiles_found as $profile)
        {
            if($profile['user_id']==$user_id)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    private function cleanupCreatedProfile($profile_id)
    {
        return $this->user_model->delete_user($profile_id);
    }

}

?>