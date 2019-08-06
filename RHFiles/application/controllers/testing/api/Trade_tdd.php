<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller carries out the tests for building the skill api
 * TEST PATH:<testing/api/trade_tdd>
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Trade_tdd extends CI_Controller
{
    /***** TEST CASE *****/

    public $existing_trade=array();
        
    /*********************/

    public function __construct()
    {
        parent::__construct();
        $this->load->library("unit_test");
    }
    
    public function index()
    {

        $this->existing_trade=$this->trade_model->read_trade_for_testing();
        
        $test_name="Checking API can be used to fetch a trade checking its completed";
        $test1=$this->checkingApiCanFetchTradeStatus($this->existing_trade['skill_proposal_id']);
        $expected_result=TRUE;
        echo $this->unit->run($test1,$expected_result,$test_name,$notes="Assumes atleast 1 trade exists and status is complete");

    }

    /**
     * Checks if the api fetch if a skill proposal is complete and therefore a trade.
     * 
     * @return boolean True if the skill proposal status is complete
     */
    private function checkingApiCanFetchTradeStatus($skill_proposal_id)
    {
        $params=array('skill_proposal_id'=>$skill_proposal_id,'check_mode'=>TRUE,'status_check_value'=>'complete');
        $trade_status_match=$this->api_request->get("trade","read_trade_status",$params);
        return $trade_status_match;
    }
    

}

?>