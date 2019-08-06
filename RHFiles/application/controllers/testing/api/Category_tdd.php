<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the category api
 * TEST PATH:<testing/api/category_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Category_tdd extends CI_Controller
{
    /***** TEST CASE *****/

    public $exitsting_categories=array();
    
    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index(){

        //read all categories
        $this->existing_categories=$this->category_model->read_all_categories();

        //check the api can fetch all categories comparing it to directly fetching from the model
        $test_name="Checking API can be used to fetch all categories";
        $test1=$this->checkingApiCanFetchAllCategories();
        $expected_result=TRUE;
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Does the api return the same categories as the the model directly");
        
        //Setting existing categories as null
        $this->existing_categories=NULL;

        //checking that the api still retrieves the categories and checks the result is not null
        $test_name="Checking API can be used to fetch all categories";
        $test1=$this->checkingApiCanFetchAllCategories();
        $expected_result=FALSE;
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Checking they no longer match when existing categories set to null");
    }

    /**
     * Checks if the categories retrieved from the api match the categories retrieved directly using the model
     * 
     * @return Boolean True categories from get request to api match ones found directly using model
     */
    private function checkingApiCanFetchAllCategories()
    {
        $api_categories=$this->api_request->get("category","all");
        return ($api_categories==$this->existing_categories);
    }
    

 

}

?>