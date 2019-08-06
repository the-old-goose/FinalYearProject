<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the category api
 * TEST PATH:<testing/model/category_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Category_tdd extends CI_Controller
{
    /***** TEST CASE *****/

    public $category_name="Test_Category";
    public $current_number_of_categories=9;
        
   /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index(){

        $test_name="Checking a category can be created";
        $test0=$this->checkingCategoryCanBeCreated($this->category_name);
        $expected_result=TRUE;
        echo $this->unit->run(is_numeric($test0),$expected_result,$test_name,$notes="True if category id is returned");

        $test_name="Reading the category that has been created ";
        $test1=$this->checkingCreatedCategoryCanBeRead($test0);
        $expected_result=TRUE; 
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Asserts the Category name in db matches the testcase");

        $test_name="Checking all categories can be read";
        $test2=$this->checkingAllCategoriesCanBeRead();
        $expected_result=TRUE; 
        echo $this->unit->run($test2,$expected_result,$test_name,$notes="Checks array length is current number of categories (+1 just created)");

        $test_name="Deleting the created category ";
        $test3=$this->cleanupCreatedCategory($test0);
        $expected_result=TRUE; 
        echo $this->unit->run($test3,$expected_result,$test_name,$notes="Deletes the created category for reruns");
        
    }

    /**
     * Uses the model to create a category
     * 
     * @return int The id of the category created
     */
    private function checkingCategoryCanBeCreated($category_name)
    {
        return $success =$this->category_model->create_category($category_name);
    }
    
    /**
     * Uses the model to check the created category matches the test case
     * 
     * @return boolean True If the category read from db matches the test case
     */
    private function checkingCreatedCategoryCanBeRead($category_id)
    {
          $category_name= $this->category_model->read_category($category_id);
          return strcmp($this->category_name,$category_name);
    }

    /**
     * Uses the model to check all the categories can be read
     * 
     * @return boolean True If array of the size of all the categories is the current number+1 just created
     */
    private function checkingAllCategoriesCanBeRead()
    {
        $all_categories= $this->category_model->read_all_categories();

        if(sizeof($all_categories)==($this->current_number_of_categories+1))
        {
           return $success=TRUE;
        }
        else
        {
           return $success=FALSE;
        }
        
    }

    private function cleanupCreatedCategory($category_id)
    {
        return $this->category_model->delete_category($category_id);
    }

}

?>