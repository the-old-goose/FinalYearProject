<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the profile api
 * TEST PATH:<testing/api/profile_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Profile_tdd extends CI_Controller
{
    /***** TEST CASE *****/

    public $existing_profile=array();
        

    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index(){

        //Read the profile with the highest rank storing it as the existing profile
        $this->existing_profile=$this->profile_model->read_highest_rank_profiles(1);
        
        //Fetch this profile with the api and ensure it matches
        $test_name="Checking API can be used to fetch the top profile";
        $test1=$this->checkingApiCanFetchAProfile($this->existing_profile[0]['user_id']);
        $expected_result=TRUE;
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Assumes atleast 1 account is created");

        //Fetch the same profile by searching for it using its first_name
        $test_name="Checking API can be used to fetch same profile using search";
        $test2=$this->checkingApiCanFetchProfileUsingSearch($this->existing_profile[0]['first_name']);
        $expected_result=TRUE;
        echo $this->unit->run($test2,$expected_result,$test_name,$notes="Assumes first result found has identical first name");
        
    }

    /**
     * Checks if the profile can be fetched using the api
     * 
     * @return boolean True if api profile user_id  match ones found directly using model
     */
    private function checkingApiCanFetchAProfile($profile_id)
    {
        $api_profile=$this->api_request->get("profile","single_profile",$params=array('profile_id'=>$profile_id));
        return ($api_profile['user_id']==$profile_id);
    }
    
    /**
     * Checks if the profile can be fetched using profile search api function 
     * 
     * @return boolean True if api profile first name  match ones found directly using model
     */
    private function checkingApiCanFetchProfileUsingSearch($first_name_search_term)
    {
        $params=array('search'=>$first_name_search_term,'items_per_page'=>1,'offset'=>0);
        $api_profile=$this->api_request->get("profile","search",$params);

        return ($api_profile[0]['first_name']==$first_name_search_term);
    }
 

}

?>