<?php

use Restserver\Libraries\REST_Controller;


defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This Controller carries out the logic for the Trade API. It extends the Rest Controller handling HTTP GET requests by returning
 * a JSON response based on the resource requested.
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Trade extends REST_Controller {
   
    /**
     * Handles GET requests for a retrieving a trade status.If the $check_mode is TRUE and $status_check_value is provided then a boolean will be returned, 
     * Otherwise the trade status will be returned
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $skill_proposal_id The skill proposal id to be checked
     * @var $check_mode A boolean stating that the model should check the $status_check_value against the status found
     * @var $status_check_value A string of the expected status
     * @var $trade_status The status of the trade or if $check_mode and $status_check_value provided, a boolean indicating if it matches that in the model
     * @return object A JSON object containing either the $trade_status or Boolean if $status_check_value and $check_mode are set
     */
    function read_trade_status_get()
    {

        $skill_proposal_id=(int)$this->get('skill_proposal_id');
        $check_mode=(bool)$this->get('check_mode');
        $status_check_value=$this->get('status_check_value');

        $trade_status= $this->trade_model->read_trade_status($skill_proposal_id,$check_mode,$status_check_value);
    
        if($trade_status)
        {
            $this->response($trade_status,  REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}
