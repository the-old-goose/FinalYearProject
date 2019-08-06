<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the category api
 * TEST PATH:<testing/model/skill_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Skill_tdd extends CI_Controller {

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
    
    public $skill_details =array(
            'name'=>"#testskill",
            'description'=>"A skill",
            'value'=>5,
            'category_id'=>1);

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

        $test_name="Checking test account can be registered ";
        $test1_user_id=$this->checkingUserCanBeCreated($this->user_details);
        $expected_result=TRUE; 
        echo $this->unit->run($test1_user_id,$expected_result,$test_name,$notes="");

        $test_name="Checking a skill can be created";
        $test2_skill_id=$this->checkingSkillCanBeCreated($test1_user_id,$this->skill_details);
        $expected_result=TRUE;
        echo $this->unit->run(is_numeric($test2_skill_id),$expected_result,$test_name,$notes="uses created users id");
    
        $test_name="Checking the created skill can be read";
        $test3=$this->checkingSkillCanBeRead($test2_skill_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test3,$expected_result,$test_name,$notes="");

        $test_name="Checking the created skill can be read using search";
        $test4=$this->checkingSkillCanBeReadUsingSearch($this->skill_details['name'],$test2_skill_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test4,$expected_result,$test_name,$notes="Will fail if more than 10 skills with same name in db due to limit");

        $test_name="Checking the user skill table entry has been created";
        $test5_user_skill_id=$this->checkingUserSkillTableEntryCreated($test2_skill_id,$this->skill_details);
        $expected_result=TRUE; 
        echo $this->unit->run($test5_user_skill_id,$expected_result,$test_name,$notes="");

        $test_name="Deleting the skill and user skill created ";
        $test6=$this->cleanupCreatedSkill($test2_skill_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test6,$expected_result,$test_name,$notes="");

        $test_name="Deleting the user created";
        $test7=$this->cleanupCreatedAccount($test1_user_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test7,$expected_result,$test_name,$notes="");
        
    }
    
   /**
     * This test checks that creating a user with valid details returns true when inserting the data
     * into the database.
     */
    private function checkingUserCanBeCreated(array $user_details)
    {
        return  $success= $this->user_model->create_user_and_profile($user_details['username'],$user_details['email'],$user_details['encrypt_pass'],$user_details['first_name'],$user_details['last_name'],$user_details['location_id'],$user_details['mobile'],$user_details['auth_code'],$user_details['read_id']); 
    }

    private function checkingSkillCanBeCreated(int $user_id,array $skill_details)
    {
        return  $success= $this->skill_model->create_skill($user_id,$skill_details['name'],$skill_details['description'],$skill_details['value'],$skill_details['category_id'],$skill_details['category_id'],$read_id=TRUE); 
    }

    private function checkingSkillCanBeRead(int $skill_id)
    {
        $skill= $this->skill_model->read_specific_skills($skill_id);
        return ($skill['name']==$this->skill_details['name'] && $skill['description']==$this->skill_details['description'] );
    }

    private function checkingSkillCanBeReadUsingSearch($search_term,$skill_id)
    {
        $skills_found=$this->skill_model->read_search_skills($search_term,$limit=10);
        
        foreach($skills_found as $skill)
        {
            if($skill['skill_id']==$skill_id)
            {
                return TRUE;
            }
        }
        return FALSE;
    }

    private function checkingUserSkillTableEntryCreated($skill_id,array $skill)
    {
        $skill['skill_id']=$skill_id;
        return $user_skill_id=$this->skill_model->read_user_skill_id($skill);
    }

    /**
     * This function removes the account created in the test run to allow test re-runs.
     */
    private function cleanupCreatedSkill($skill_id)
    {
        return $this->skill_model->delete_skill($skill_id);
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