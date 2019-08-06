<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the category api
 * TEST PATH:<testing/model/location_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Location_tdd extends CI_Controller
{

    /***** TEST CASE *****/

    public $location_name="Test_Location";
    
    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index(){

        $test_name="Checking a location can be created";
        $test0=$this->checkingLocationCanBeCreated($this->location_name);
        $expected_result=TRUE;
        echo $this->unit->run(is_numeric($test0),$expected_result,$test_name,$notes="True if location id is returned");

        $test_name="Reading the location that has been created ";
        $test1=$this->checkingCreatedLocationCanBeRead($test0);
        $expected_result=TRUE; 
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Asserts the location name in db matches the testcase");

        $test_name="Deleting the created location ";
        $test2=$this->cleanupCreatedLocation($test0);
        $expected_result=TRUE; 
        echo $this->unit->run($test2,$expected_result,$test_name,$notes="Deletes the created location for reruns");
        
    }

    /**
     * Creates a location using the model
     * 
     * @return int The id of the location created
     */
    private function checkingLocationCanBeCreated($location_name)
    {
        return $success =$this->location_model->create_location($location_name);
    }
    
    /**
     * reads the created location name checking it matches the test case
     * 
     * @return boolean True if the test case matches the result read in db
     */
    private function checkingCreatedLocationCanBeRead($location_id)
    {
          $loc_name= $this->location_model->read_location($location_id);
          return strcmp($this->location_name,$loc_name);
    }


    private function cleanupCreatedLocation($location_id)
    {
        return $this->location_model->delete_location($location_id);
    }

}

?>