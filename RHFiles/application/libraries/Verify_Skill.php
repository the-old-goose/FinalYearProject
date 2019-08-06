<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This class is used vefiry POST data associated with creating a new skill
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class Verify_Skill
{

    var $skill;
    protected $CI;

    public function __construct($skill=NULL)
    {
            $this->skill = $skill;
            $this->CI =& get_instance();
    }

    /**
     * This function uses the codeigniter form validation library to set and check rules pertaining to the skill being created
     * 
     * @return boolean True if all rules are passed 
     * @todo Custom validation for bad language
     */
    public function verify_created_skill()
    {
        $this->CI->load->library('form_validation');
        $this->form_validation->CI =& $this;
        
        $this->CI->form_validation->set_rules('skillname','Skill Name','required|alpha_numeric_spaces|min_length[3]|max_length[15]');
        $this->CI->form_validation->set_rules('skillvalue','Skill Value','required');
        $this->CI->form_validation->set_rules('categoryid','Category','required');
        $this->CI->form_validation->set_rules('skilldesc','Skill Description','required|alpha_numeric_spaces|min_length[10]|max_length[253]');

        return $this->CI->form_validation->run();
    }
 
}
?>
