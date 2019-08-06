<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This class is used to extend the CI_Form_validation library adding my own checks
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class MY_Form_validation extends CI_Form_validation
{

    function __construct($rules = array())
    {
        parent::__construct($rules);
    }

    /**
    * Custom validation method which uses the model to check if the username already exists
    * 
    * @param string $username The username to check exists
    * @return bool True if username  does NOT exist
    * @link https://www.codeigniter.com/userguide3/libraries/form_validation.html#callbacks-your-own-validation-methods
    */
    function username_exists_check($username)
    {

        $this->CI->load->library('form_validation');
        $this->form_validation->CI =& $this;

        $this->CI->form_validation->set_message('username_exists_check','This username is taken!');
        return $this->CI->user_model->check_username_exists($username);
    }

    /**
    * Custom validation method which uses the model to check if the email already exists
    * 
    * @param string $email The email to check exists
    * @return bool True if email  does NOT exist
    * @link <https://www.codeigniter.com/userguide3/libraries/form_validation.html#callbacks-your-own-validation-methods>
    */
    function email_exists_check($email)
    {

        $this->CI->load->library('form_validation');
        $this->form_validation->CI =& $this;

        $this->CI->form_validation->set_message('email_exists_check','This Email is already in use!');
        return $this->CI->user_model->check_email_exists($email);
    }

    /**
    * Custom validation method which uses regex to check if the password contains letters and numbers
    * 
    * @param string $email The email to check exists
    * @return bool True if email  does NOT exist
    * @link <https://stackoverflow.com/questions/9335915/check-if-a-string-contains-numbers-and-letters>(regex)
    */
    function password_alphanumeric_check($password)
    {
        $this->CI->load->library('form_validation');
        $this->form_validation->CI =& $this;
        $this->CI->form_validation->set_message('password_alphanumeric_check','Password must contain Numbers and Characters');
        
        return  (preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password));
    }
}