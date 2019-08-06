<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This class is used vefiry POST data associated with verifying a user
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class Verify_User
{
    var $user;
    protected $CI;

    public function __construct($user=NULL)
    {
            $this->user = $user;
            $this->CI =& get_instance();
    }

    /**
     * This function uses the codeigniter form validation library to set and check rules pertaining to logging in
     * 
     * @return boolean True if all rules are passed 
     */
    public function verify_user_login()
    {
        $this->CI->load->library('form_validation');
        $this->form_validation->CI =& $this;

        $this->CI->form_validation->set_rules('email','Emails','required');
        $this->CI->form_validation->set_rules('pass','Passwords','required');

        return $this->CI->form_validation->run();
    }

    /**
     * This function uses the codeigniter form validation library to set and check rules pertaining to updating a profile
     * 
     * @return boolean True if all rules are passed 
     */
    public function verify_user_profile()
    {
        $this->CI->load->library('form_validation');
        $this->form_validation->CI =& $this;

        $this->CI->form_validation->set_rules('profdesc','Profile Description','required|min_length[5]|alpha_numeric_spaces|max_length[253]');
        $this->CI->form_validation->set_rules('first_name','Firstname','required|alpha|min_length[3]|max_length[15]');
        $this->CI->form_validation->set_rules('last_name','Lastname','required|alpha|min_length[2]|max_length[15]');


        return $this->CI->form_validation->run();
    }

    /**
     * This function uses the codeigniter form validation library to set and check rules pertaining to registering
     * 
     * @return boolean True if all rules are passed 
     */
    public function verify_user_register()
    {
        $this->CI->load->library('form_validation');
        $this->form_validation->CI =& $this;
        
        $this->CI->form_validation->set_rules('first_name','Firstname','required|alpha|min_length[3]|max_length[15]');
        $this->CI->form_validation->set_rules('last_name','Lastname','required|alpha|min_length[2]|max_length[15]');
        $this->CI->form_validation->set_rules('user','Username','required|username_exists_check|min_length[5]|max_length[15]');
        $this->CI->form_validation->set_rules('email','Email','required|email_exists_check|valid_email');
        $this->CI->form_validation->set_rules('pass','Password','required|password_alphanumeric_check|min_length[6]|max_length[20]');
        $this->CI->form_validation->set_rules('mobile','Mobile','required|numeric|min_length[11]|max_length[11]');

        return $this->CI->form_validation->run();
    }
    
    /**
     * This function creates a psuedorandom string used as a users verification code
     * 
     * @return string verification code 
     */
    public function generate_code()
    {
       return $string = bin2hex(random_bytes(10));
    }

    /**
     * This function sends a verification email containing the generated validation code using smtp
     * 
     * @return bool True if mail sent successfully
     */
    public function send_verification_email($email_addr,$username)
    {
        //build config array using premade gmail account
        $config= array(
            'protocol'=>'smtp',
            'smtp_host'=>'ssl://smtp.googlemail.com',
            'smtp_port'=>465,
            'smtp_user'=>'HIDDEN',
            'smtp_pass'=>'HIDDEN',
            'charset'   => 'iso-8859-1',
            'mailtype'  => 'html',

        );

        $this->CI->load->library('email',$config);
        $this->email->CI =& $this;

        $this->CI->email->set_newline("\r\n");

        if(!$code=$this->CI->user_model->read_code($username))
        {
            return FALSE; // if verification code from db cannot be read return false
        }

        //build email template and send code as a-link
        $verify_link="<a href=".site_url().'users/validate_user/'.$code.">Click Here to Validate Account<a/>";
        $this->CI->email->from('HIDDEN', 'RH FILES');
        $this->CI->email->to($email_addr);
        $this->CI->email->subject('Account Validation');
        $this->CI->email->message('Thanks for taking the time to create an account. 
                               Please follow the link to validate your account: '.$verify_link);
        
        return $this->CI->email->send();
    }

    /**
     * This function checks if a user is logged in
     * 
     * @return int  user_id of the user logged in or will exit() on no session found
     */
    public function checkLoggedIn()
    {
        if(!isset($_SESSION['user_id']))
        {
            redirect('pages/view/home');
            exit;
        }
        else
        {
            return $user_id=$_SESSION['user_id'];
        }
    }    
    
}
?>
