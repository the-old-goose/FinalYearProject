<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This Controller carries out the logic for the Profile API. It extends the Rest Controller handling HTTP GET requests by returning
 * a JSON response based on the resource requested.
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Profile extends REST_Controller {
    
    /**
     * Handles GET requests for a single profile by using the model to retrieve and return a single profile using a profile_id. If the request is successful
     * a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $profile_id The profile id passed in the GET request to be found
     * @var $profile The profile found from the model
     * @return object A JSON object containing a single profile
     */
    function single_profile_get()
    {
        $profile_id=(int) $this->get('profile_id');

        $profile = $this->profile_model->read_profile($profile_id);
        
        if($profile)
        {
            $this->response($profile, REST_Controller::HTTP_OK); // 200 OK HTTP response code With JSON payload
        }
 
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);//Errors to be handled application side
        }
    }

    /**
     * Handles GET requests for searching for profiles by using the model to retrieve and return profiles matching a search term. If the request is successful
     * a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $search_term The profiles first or last name to be searched
     * @var $number_of_items The maximum number of items that should be returned
     * @var $offset The offset used by database query , used in pagination
     * @var $profiles The profiles found that match the $search_term
     * @return object A JSON object containing profiles that match the search term with a limit of $number_of_items
     */
    function search_get()
    {
        $search_term=$_GET['search'];
        $number_of_items=(int)$_GET['items_per_page'];
        $offset=(int)$_GET['offset'];

        $profiles= $this->profile_model->read_profiles_using_search($search_term,$number_of_items,$offset);

        if($profiles)
        {
            $this->response($profiles, REST_Controller::HTTP_OK); // 200 being the HTTP response code
        }
 
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}
