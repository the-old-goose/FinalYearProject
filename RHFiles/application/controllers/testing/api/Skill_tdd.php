<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the skill api
 * TEST PATH:<testing/api/skill_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Skill_tdd extends CI_Controller {
    
    /***** TEST CASE *****/

    public $category_id=1;
   
    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index()
    {
      
        $test_name="Checking API can get skills in a category";
        $test0_category_skill=$this->checkingApiCanGetCategorySkills($this->category_id);
        $expected_result=TRUE;
        echo $this->unit->run(is_array($test0_category_skill),$expected_result,$test_name,$notes=" Assumes at least 1 skill in category ,can fail if inconsistent data left in db i.e no user_skill");

        $test_name="Checking API can get a single skill";
        $test1=$this->checkingApiCanGetSingleSkill($test0_category_skill[0]['skill_id']);
        $expected_result=TRUE;
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="");

    }

   /**
     * Checks if the a skill belonging to a category can be fetched using the api
     * 
     * @return array containing the skill belonging to the category $category_id
     */
    private function checkingApiCanGetCategorySkills($category_id)
    {
        $params=array('cid'=>$category_id,
                      'offset'=>'0',
                      'items'=>'1');
        
        return $test=$this->api_request->get("skill","incategory",$params);
    }

    /**
     * Checks if the a single skill can be fetched using the api
     * 
     * @return boolean True if the skill from api matches the one provided
     */
    private function checkingApiCanGetSingleSkill($skill_id)
    {
        $single_skill=$this->api_request->get("skill","single",$params=array('id'=>$skill_id));
        return $single_skill['skill_id']==$skill_id;
    }

}

?>