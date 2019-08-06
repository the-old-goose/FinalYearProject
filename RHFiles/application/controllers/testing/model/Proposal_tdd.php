<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the category api
 * TEST PATH:<testing/model/proposal_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Proposal_tdd extends CI_Controller
{   
    /***** TEST CASE *****/
    public $user_a_details =array(
        'username'=>"#usertest1",
        'email'=>"test2@email.com",
        'encrypt_pass'=>"",
        'first_name'=>"testfirst",
        'last_name'=>"testlast",
        'location_id'=>1,
        'mobile'=>"07000000000",
        'auth_code'=>"X",
        'read_id'=>TRUE, 
        'year'=>"First");
    
    public $user_b_details =array(
            'username'=>"#usertest2",
            'email'=>"test1@email.com",
            'encrypt_pass'=>"",
            'first_name'=>"testfirst",
            'last_name'=>"testlast",
            'location_id'=>1,
            'mobile'=>"07000000000",
            'auth_code'=>"X",
            'read_id'=>TRUE, 
            'year'=>"First");

    public $skill_a_details =array(
            'skill_id'=>0,
            'name'=>"#testskilla",
            'description'=>"A skill",
            'value'=>5,
            'category_id'=>1);
    
    public $skill_b_details =array(
            'skill_id'=>0,
            'name'=>"#testskillb",
            'description'=>"A skill",
            'value'=>5,
            'category_id'=>1);

    public $proposed_dates="Jan1,Jan2";

    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index()
    {
        $test_name="Creating test account A";
        $test1_user_a_id=$this->checkingUserCanBeCreated($this->user_a_details);
        $expected_result=TRUE; 
        echo $this->unit->run($test1_user_a_id,$expected_result,$test_name,$notes="");

        $test_name="Checking skill for account A can be created";
        $test2_skill_a_id=$this->checkingSkillCanBeCreated($test1_user_a_id,$this->skill_a_details);
        $expected_result=TRUE;
        echo $this->unit->run(is_numeric($test2_skill_a_id),$expected_result,$test_name,$notes="uses created users id");

        $test_name="Creating test account B";
        $test3_user_b_id=$this->checkingUserCanBeCreated($this->user_b_details);
        $expected_result=TRUE; 
        echo $this->unit->run($test3_user_b_id,$expected_result,$test_name,$notes="");

        $test_name="Checking skill for account B can be created";
        $test4_skill_b_id=$this->checkingSkillCanBeCreated($test3_user_b_id,$this->skill_b_details);
        $expected_result=TRUE;
        echo $this->unit->run(is_numeric($test4_skill_b_id),$expected_result,$test_name,$notes="uses created users id");

        $this->skill_a_details['skill_id']=$test2_skill_a_id;
        $this->skill_b_details['skill_id']=$test4_skill_b_id;

        $test_name="Checking Creating a proposal using both users skills";
        $test5_proposal_id=$this->checkingASkillProposalCanBeCreated($this->skill_a_details,$this->skill_b_details,$this->proposed_dates);
        $expected_result=TRUE;
        echo $this->unit->run(is_numeric($test5_proposal_id),$expected_result,$test_name,$notes="");
        

        $test_name="Checking Creating a proposal can be read";
        $test6_proposal=$this->checkingCreatedProposalCanBeRead($test5_proposal_id);
        $expected_result=TRUE;
        echo $this->unit->run(is_array($test6_proposal),$expected_result,$test_name,$notes="asserts the array containing the proposal info is returned");
        
        $test_name="Checking Initial and offer skill entries created";
        $test7=$this->checkingInitialAndOfferSkillEntriesMade($test6_proposal);
        $expected_result=TRUE; 
        echo $this->unit->run($test7,$expected_result,$test_name,$notes="checks inital and offer skill tables are created when creating skills");

        $test_name="Checking proposal is pending approval";
        $test8=$this->checkingProposalIsPendingApproval($test5_proposal_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test8,$expected_result,$test_name,$notes="New propsals default status is pending approval");

        $test_name="Cancelling a proposal";
        $test8=$this->checkingProposalCanBeCancelled($test5_proposal_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test8,$expected_result,$test_name,$notes="");
        
        $test_name="Deleting the skills and user skills created ";
        $test9=$this->cleanupCreatedSkill($test2_skill_a_id,$test4_skill_b_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test9,$expected_result,$test_name,$notes="");

        $test_name="Deleting the user created";
        $test10=$this->cleanupCreatedAccount($test1_user_a_id,$test3_user_b_id);
        $expected_result=TRUE; 
        echo $this->unit->run($test10,$expected_result,$test_name,$notes="");
    }


    private function checkingUserCanBeCreated(array $user_details)
    {
        return  $success= $this->user_model->create_user_and_profile($user_details['username'],$user_details['email'],$user_details['encrypt_pass'],$user_details['first_name'],$user_details['last_name'],$user_details['location_id'],$user_details['mobile'],$user_details['auth_code'],$user_details['read_id']); 
    }

    private function checkingSkillCanBeCreated(int $user_id,array $skill_details)
    {
        return  $success= $this->skill_model->create_skill($user_id,$skill_details['name'],$skill_details['description'],$skill_details['value'],$skill_details['category_id'],$skill_details['category_id'],$read_id=TRUE); 
    }

    private function checkingASkillProposalCanBeCreated($initial_skill,$offer_skill,$proposed_dates)
    {
    
        return $success=$this->proposal_model->create_proposal($initial_skill,$offer_skill,$proposed_dates,TRUE);
        
    }

    public function checkingCreatedProposalCanBeRead($skill_proposal_id)
    {
       return $proposal= $this->proposal_model->read_skill_proposal($skill_proposal_id);
    }

    private function checkingInitialAndOfferSkillEntriesMade($proposal)
    {
        return (is_array($this->initialoffer_model->read_initial_skill($proposal['initial_skill_id'])) && is_array($this->initialoffer_model->read_offer_skill($proposal['offer_skill_id'])));
    }

    private function checkingProposalIsPendingApproval($proposal_id)
    {
        return $proposal_status=$this->trade_model->read_trade_status($proposal_id,TRUE,'pending approval');   
    }

    private function checkingProposalCanBeCancelled($proposal_id)
    {
        return $this->proposal_model->cancel_proposal($proposal_id);
    }

    private function cleanupCreatedSkill($skill_a_id,$skill_b_id)
    {
        return ($this->skill_model->delete_skill($skill_a_id) && $this->skill_model->delete_skill($skill_b_id));
    }

    private function cleanupCreatedAccount($user_a_id,$user_b_id)
    {
        return ($this->user_model->delete_user($user_a_id) && $this->user_model->delete_user($user_b_id) );
    }
}

?>