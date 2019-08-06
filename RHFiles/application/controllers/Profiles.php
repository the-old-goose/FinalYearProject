<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller handles the logic associated with profiles
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class Profiles extends CI_Controller 
{
    /**
     *  
     * This function will load the corrisponding view and data viewing a users profile.
     * 
     * @param string $profiles_user_id The id of the profile to be viewed
     * @param int $edit If edit is not 0 the user will see the edit elements in the view when looking at own profile
     */
    public function view($profiles_user_id=NULL,$edit=0) 
    {
        $my_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id
        $data['page']="view";

        // If profile_id not given view own profile.
        if(is_null($profiles_user_id)) $profiles_user_id=$my_id; 
        
        //Check if viewing own profile so edit can be displayed.
        ($my_id==$profiles_user_id) ? $data['myprofile']=TRUE : $data['myprofile']=FALSE;
        
        //If looking at own profile and edit is true then show  the edit view elements.
        ($my_id==$profiles_user_id && $edit==1) ? $data['edit']=1 : $data['edit']=0; 
        
        //Read profile data and all skills they have listed using api.
        $params=array('profile_id'=>$profiles_user_id,
                      'user_id'=>$profiles_user_id);

        $data['profile']=$this->api_request->get("profile","single_profile",$params);
        $data['listings']=$this->api_request->get("skill","specific_users_skills",$params);


        //Check if profile and skill data found, if not set variable appropriately so view can display correctly
        empty($data['profile']) ? $data['no_results']=TRUE: $data['no_results']=FALSE;
        empty($data['listings']) ? $data['no_listings']=TRUE: $data['no_listings']=FALSE;

        //load gravatar using email
        $data['gravatar_url']= $this->gravatar->get($data['profile']['email'],$size=250,$default_image ='mm');
        
        $this->load->view('templates/header', $data);
        $this->load->view('profiles/view', $data);
        $this->load->view('templates/footer', $data);  
    }
    
    /**
     *  
     * This function will load the corrisponding view and data for searching for profiles
     * 
     * @param string $offset The offset of the profiles return used by the database
     */
    public function search($offset=0)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id
        $search_term=$this->user_input->getSearchTerm(); //Get users searched term
        $data['page']="Search Profile";

        //Initialize pagination
        $config['base_url'] = site_url().'/profiles/search/'; 
        $config['total_rows'] = $this->profile_model->read_all_like_count($search_term);
        $config['per_page'] = 5;
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);

        //Read the profiles relating to search term using API
        $params=array('offset'=>$offset,
                      'items_per_page'=>$config['per_page'],
                      'search'=>$search_term);

        $data['profiles']=$this->api_request->get("profile","search",$params);

        //If no profiles found set no_results so view can display appropriately
        empty($data['profiles']) ? $data['no_results']=true: $data['no_results']=false;

        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('profiles/user_search', $data);
        $this->load->view('templates/footer', $data);  
    }

    /**
     *  
     * This function will carry out the checks associated with allowing a user to update there profile
     * 
     */
    public function update() 
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id

        if($this->verify_user->verify_user_profile()===FALSE)  //valid fields provided for updating?
        {       
            $this->session->set_flashdata('fail_msg', validation_errors()); //Invalid so redirect and display errors
            redirect('profiles/view');
        }
        else //passed validation so set new profile attributes equal to post values using model
        {             
            $attributes= array(
                'first_name'=>ucfirst($this->input->post('first_name')),
                'last_name'=>ucfirst($this->input->post('last_name')),
                'location_id'=>$this->input->post('locationid'),
                'year' =>$this->input->post('year'),
                'description' =>ucfirst($this->input->post('profdesc'))    
            );

            if(!$success=$this->profile_model->update_profile($user_id,$attributes)) //Check Insert successful
            {
                $this->session->set_flashdata('fail_msg','Something went wrong'); //Failed so let user know. Possible db error
                redirect('profiles/view');
            }
            else
            {
                $this->session->set_flashdata('succ_msg','Your Profile is updated successfully'); //Successfully updated to redirect and notify.
                redirect('profiles/view');
            }
        }
    }
}