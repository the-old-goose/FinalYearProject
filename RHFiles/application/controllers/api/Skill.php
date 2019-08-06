<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This Controller carries out the logic for the Skill API. It extends the Rest Controller handling HTTP GET requests by returning
 * a JSON response based on the resource requested.
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Skill extends REST_Controller {

    /**
     * Handles GET requests for a retrieving a specific users skills by using the model to retrieve and return all of a specific users skills using their $user_id.
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $user_id The user id passed in the GET request which skills are to be retrieved
     * @var $limit The maximum number of items that should be returned
     * @var $offset The offset used by database query , used in pagination
     * @var $return_row Boolean to determin if only a single row should be returned
     * @return object A JSON object containing the specific users skills based on the $user_id
     */
    function specific_users_skills_get()
    {
        $user_id=(int) $this->get('user_id');
        $limit=(int) $this->get('limit');
        $offset=(int) $this->get('offset');
        $return_row=$this->get('return_row');
       
        $users_skills=$this->skill_model->read_users_skills($user_id,$limit,$offset,$return_row);

        if($users_skills)
        {
            $this->response($users_skills, REST_Controller::HTTP_OK); // 200 being the HTTP response code
        }
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);//Errors to be handled application side
        }
    }

     /**
     * Handles GET requests for checking a user created a skill by using the model.The $skill_proposal_id or $initial_skill_id can be checked.
     * A JSON object  will be returned True of False depending on if the $user_id is associated with the proposal
     * 
     * @var $user_id The user id passed in the GET request to check
     * @var $initial_skill_id The initial skill id to be checked
     * @var $skill_proposal_id The skill proposal id to be checked
     * @return object A JSON object containing true if the user created the initial skill in a proposal
     */
    function check_user_created_skill_get()
    {
        $user_id=(int) $this->get('user_id');
        $initial_skill_id=(int) $this->get('initial_skill_id');
        $skill_proposal_id=(int) $this->get('skill_proposal_id');

        $did_user_create_skill=$this->skill_model->read_if_user_created_skill($user_id,$initial_skill_id,$skill_proposal_id);
        
        $this->response($did_user_create_skill, REST_Controller::HTTP_OK); // 200 being the HTTP response code
        
    }

     /**
     * Handles GET requests for retrieving a single skill by using the model.The skill to be returned is determined by the $id
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $user_id The skill id passed in the GET request to retrieve
     * @var $skill The Skill found using the model
     * @return object A JSON object containing the skill found
     */
    function single_get()
    {   
        $id=(int) $this->get('id');
        $skill = $this->skill_model->read_specific_skills( $id );
        
        if($skill)
        {
            $this->response($skill, REST_Controller::HTTP_OK); // 200 being the HTTP response code
        }
 
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);//Errors to be handled application side
        }
    }
    
     /**
     * Handles GET requests for retrieving specific skills by using the model.The $offset and $items are used to determin what skills to retrieved.
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $offset The offset used by database query , used in pagination
     * @var $items The maximum number of skills to be returned
     * @var $skill The skills found using the model
     * @return object A JSON object containing the specific skills found
     */
    function specific_get()
    {   
       $offset=$_GET['offset'];
       $items=(int)$_GET['items_per_page'];
        
       $skill = $this->skill_model->read_specific_skills( FALSE,$items,$offset);  

        if($skill)
        {
            $this->response($skill, REST_Controller::HTTP_OK); // 200 being the HTTP response code
        }
 
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
   /**
     * Handles GET requests for retrieving  skills in a category by using the model.The $offset and $items are used to determin what skills to retrieved.
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $c_id The category id the skills belong to
     * @var $items The maximum number of skills to be returned
     * @var $offset The offset used by database query , used in pagination
     * @var $skill The skills found belonging to the category found using the model
     * @return object A JSON object containing the skills found
     */
    function incategory_get()
    {
       
       $c_id=(int)$_GET['cid'];
       $items=$_GET['items'];
       $offset=$_GET['offset'];
       
       $skill = $this->skill_model->read_category_search_skills($c_id,$items,$offset); 
       
       if($skill)
        {
            $this->response($skill, REST_Controller::HTTP_OK); // 200 being the HTTP response code
        }
 
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    /**
     * Handles GET requests for retrieving  all skills by using the model.
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $skills All skills found using the model
     * @return object A JSON object containing the skills found
     */
    function all_get()
    {
        $skills = $this->skill_model->read_all_skills();
    
        if($skills)
        {
            $this->response($skills, REST_Controller::HTTP_OK);
        }
 
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
        }
    }
    
    /**
     * Handles GET requests for retrieving skills relating to a search term by using the model.The $searchterm is used to determin what skills to retrieved.
     * If the request is successful a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $searchterm The skill name used as a search term
     * @var $items The maximum number of skills to be returned
     * @var $offset The offset used by database query , used in pagination
     * @var $skill The skills found relating to the search term
     * @return object A JSON object containing the skills found
     */
    function search_get()
    {
        $searchterm=$_GET['search'];
        $items=$_GET['items'];
        $offset=$_GET['offset'];

        $skill= $this->skill_model->read_search_skills($searchterm,$items,$offset);

        if($skill)
        {
            $this->response($skill, REST_Controller::HTTP_OK); // 200 being the HTTP response code
        }
 
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}
