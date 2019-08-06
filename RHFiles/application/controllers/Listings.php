<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * This Controller handles the logic for viewing a users listings
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 *
 */
class Listings extends CI_Controller 
{
    /**
     *  
     * This function will load the corrisponding view and data relating to the offers made to a logged in user.
     * 
     * @param string $offset The offset used by pagination of offers to be viewed
     */
    public function view_offers($offset=0) 
    {
        
        $user_id=$this->verify_user->checkLoggedIn(); //Verfify user logged in,get user_id
        $data['page']="My Offers";

        //Initialize pagination
        $config['base_url'] = site_url().'/listings/view_offers/';    
        $config['total_rows'] = $this->proposal_model->read_proposal_count($user_id);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        
        //read offers using model
        $data['skill_proposal']=$this->proposal_model->read_proposal_as_initial_skill_owner($user_id,FALSE,$config['per_page'],$offset);
        
        //set no_results which will prevent foreach running in view if there is no results
        if( empty($data['skill_proposal'])) 
        {
            $data['no_results']=TRUE;
        }
        else
        {    //if we have results obtain the gravatar for the user who made the offer

            $data['no_results']=FALSE;

            foreach( $data['skill_proposal'] as $skill_proposal)
            {
                $data['gravatar_url']= $this->gravatar->get($skill_proposal['email'],$size=25,$default_image ='mm');
            }
        }

        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('listings/view_offers', $data);
        $this->load->view('templates/footer', $data);  
    }

    /**
     *  
     * This function will load the corrisponding view and data relating to the offers sent by the logged in user.
     * 
     * @param string $offset The offset used by pagination of offers to be viewed
     */
    public function view_sent_offers($offset=0)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id
        $data['page']="Sent Offers";

        //Initialize pagination
        $config['base_url'] = site_url().'/listings/view_sent_offers/';
        $config['total_rows'] = $this->proposal_model->read_proposal_count($user_id,TRUE);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
    
        //Read offers made to other users
        $data['skill_proposal']=$this->proposal_model->read_proposal_as_offerer($user_id,FALSE,$config['per_page'],$offset);
    
        //set no_results which will prevent foreach running in view if there is no results
        if(empty($data['skill_proposal']))
        {
            $data['no_results']=TRUE;
        } 
        else
        {   //if we have results obtain the gravatar for the user who was sent the offer
            $data['no_results']=FALSE;

            foreach( $data['skill_proposal'] as $skill_proposal)
            {
                $email=$skill_proposal['email'];
                $data['gravatar_url']= $this->gravatar->get($email=$skill_proposal['email'],$size=25,$default_image ='mm');
            }
        }
        
        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('listings/view_sent_offers', $data);
        $this->load->view('templates/footer', $data);  
    }

    /**
     *  
     * This function will load the corrisponding view and data relating to the skills the logged in user has created.
     * 
     * @param string $offset The offset used by pagination of offers to be viewed
     */
    public function view_skills($offset=0)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in returns user_id
        $data['page']="My Listings";

        //initialize pagination
        $config['base_url'] = site_url().'/listings/view_skills/';
        $config['total_rows'] = $this->skill_model->read_users_skills_count($user_id);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);


        //Read users skills using API
        $params=array('user_id'=>$user_id,
                      'limit'=>$config['per_page'],
                      'offset'=>$offset,'return_row'=>0);
                      
        $data['user_skills']=$this->api_request->get("skill","specific_users_skills",$params);

        //set no_results which will prevent foreach running in view if there is no results
        empty($data['user_skills']) ? $data['no_results']=true: $data['no_results']=false;

        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('listings/view_skills', $data);
        $this->load->view('templates/footer', $data);  
    }      
}