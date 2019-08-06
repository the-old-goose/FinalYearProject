<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This controller handles the logic associated with skills i.e. the marketplace
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class Users extends CI_Controller
{

    /**
     * This function is called when a user tries to register. The fields are validated and sent in a POST request to the api to be stored in
     * the db or the page is reloaded and error message displayed on validation failure.
     * 
     */
    public function register()
    {
        //set tab title
        $data['page']="Register";

        if(isset($_SESSION['user_id'])) //Already logged in?
        {
            redirect('profiles/view');
        }

        //validate Registering the user...
        if($this->verify_user->verify_user_register()===FALSE)
        {
            //if validation fail... set flash data so user doesnt have to retype every field on validation fail
            $this->session->set_userdata('flash_firstname', $this->input->post('first_name'));
            $this->session->set_userdata('flash_lastname', $this->input->post('last_name'));
            $this->session->set_userdata('flash_username', $this->input->post('user'));
            $this->session->set_userdata('flash_email', $this->input->post('email'));
            $this->session->set_userdata('flash_mobile', $this->input->post('mobile'));
            
            $this->session->mark_as_flash(array('flash_username', 'flash_lastname','flash_username','flash_email','flash_mobile'));
            
            //Validation failed so reload the page with validation error msgs
            $this->load->view('templates/header',$data);
            $this->load->view('users/register');
            $this->load->view('templates/footer');
        }
        else //POST user info to api and redirect back to homepage
        { 
            //Pass hashed using B-Crypt function
            $encrypt_pass=password_hash($this->input->post('pass'),PASSWORD_BCRYPT);
              
            //Create assoc array of user details
            $params= array(
                'username'=>$this->input->post('user'),
                'email'=>$this->input->post('email'),
                'password'=>$encrypt_pass,
                'first_name'=>ucfirst($this->input->post('first_name')),
                'last_name'=>ucfirst($this->input->post('last_name')),
                'location_id'=>$this->input->post('locationid'),
                'mobile'=>$this->input->post('mobile'),
                'auth_code'=>$this->verify_user->generate_code());

            //POST request containing user details for registering
            $register_success=$this->api_request->post('user','register',$params);    
          
            //Notify if POST unsuccessful
            if ($register_success === FALSE) 
            { 
                $this->session->set_flashdata('fail_msg','Registeration Failed Please Try Again!');
                redirect('users/register');
            }

            
            //Send verification email with code to the user once POST successful
            if(!$this->verify_user->send_verification_email($params['email'],$params['username']))
            {
                //if email cannot be sent display error msg
                $this->session->set_flashdata('fail_msg','Error sending validation email, Contact support!');
                redirect('users/register');
            }

            //Notify user that verification email sent
            $this->session->set_flashdata('warn_msg','Validation Email Sent to '.$params['email'].' Check Spam!');
            redirect('users/login');
        }
    }

    /**
    * This function is called when a user clicks on the link in the email to validate their account. Once validated the user will 
    * be able to login
    * 
    * @param string $code The auth_code in email to be validated
    */
    public function validate_user($code=NULL)
    {
        //GET request using API to determin if the code is valid
        $params=array('code'=>$code);
        $data['success']=$this->api_request->get("user","validate_user_code",$params);

        //if the validation code in db matches that provided
        if($data['success'])
        {
            $this->session->set_flashdata('succ_msg','Registration Successful, Please Log In.');
            redirect('users/login');
        }
        else
        {
            $this->session->set_flashdata('fail_msg','Sorry, We were Unable to verify this account.');
            redirect('users/register');
        }
    }
        
    /**
     * This function deals with the data and views when a user attempts to login 
     *   
     */
    public function login()
    {
        if(isset($_SESSION['user_id'])) //Already logged in?
        {
            redirect('profiles/view');
        }

        //set tab title
        $data['page']="Login";
   
        //Verify user login input i.e email ,password
        if($this->verify_user->verify_user_login()===FALSE)
        {
            //validation failed so reload page with errors
            $this->load->view('templates/header',$data);
            $this->load->view('users/login');
            $this->load->view('templates/footer');
        }
        else
        {
            //read POST input
            $email=$this->input->post('email');
            $encrypt_pass=$this->input->post('pass');

            //GET request to API fetching account
            $params=array('email'=>$email,
                          'pass'=>$encrypt_pass);

            $user=$this->api_request->get("user","login",$params);
                
            
            // If user found and API call successful create session with user details...
            if($user_id=$user['user_id'])
            { 
                    
                $user_data= array(
                        'user_id'=>$user_id,
                        'username'=>$user['username'],
                        'user_pic'=>$this->gravatar->get($email,$size=40,$default_image ='mm'));
                    
                //create session
                $this->session->set_userdata($user_data);
                $this->session->set_flashdata('succ_msg','Login successful');
                redirect('profiles/view');

            }
            else  //Or Login not found so display error and redirect
            {
                $this->session->set_flashdata('fail_msg','Login Failed');
                redirect('users/login');
                    
            }
        }
    }
        
    /**
     * This function is called when logging out, it will unset the session data.The user is 
     * redirected to the homepage and flash data is displayed
     */
    public function logout()
    {

        //redirect if not logged in
        $this->verify_user->checkLoggedIn();

        //Destroy session data
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('username');
        $this->session->unset_userdata('user_pic');

        $this->session->set_flashdata('succ_msg','You are now logged out');
        redirect('users/login');
    }

}