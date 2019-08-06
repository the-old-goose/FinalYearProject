<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This class is used read the users POST input used when searching in multiple controller functions
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class User_Input
{
   /**
     * @var object CodeIgniter instance Singleton
     */
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    /**
     * This function is used to return the search term that has been sent in a POST when searching 
     * 
     * @return string $searchterm The search term that is either stored from a previous search or newly sent
     */
    public function getSearchTerm()
    {
        $searchterm="";
        $this->CI->load->library('session');
            

        if(!$this->CI->session->userdata('searchterm'))
        {//no search term
            if($this->CI->input->post('search'))
            {   //a new search has been made
                $searchterm=$this->CI->input->post('search');
                $this->CI->session->set_userdata('searchterm', $searchterm); //store the new search in session
            }
        } 
        else
        { //already a search term
            if($this->CI->input->post('search'))
            { //new seach has been made
                $searchterm=$this->CI->input->post('search');
                $this->CI->session->set_userdata('searchterm', $searchterm); //store the new search in session
            }
            else
            {
                $searchterm=$this->CI->session->userdata('searchterm');  //use previous search term
            }
        }
            
        return $searchterm;

    }
        
}
?>