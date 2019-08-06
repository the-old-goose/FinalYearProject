<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This Controller handles the logic for viewing a users listings
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 * @link <https://www.codeigniter.com/userguide3/tutorial/static_pages.html>
 * @link <https://www.w3schools.com/howto/howto_css_parallax.asp> (guide on parallax effect)
 */
class Pages extends CI_Controller 
{
    
    /**
     *  
     * This function will load the corrisponding view along with the page header and footer.
     * If the $page provided does not exist in the views folder, 404 error will be displayed.
     * 
     * @param string $page Name of a page in the site.
     */
    public function view($page = 'home') 
    {
        if ( !file_exists(APPPATH.'views/pages/'.$page.'.php'))
        {
            show_404();
        }

        if($page =='home') //If $page=home page in URL, then load only head and splash page.
        {
            //read the top 3 profiles using model
            $data['top_profiles']=$this->profile_model->read_highest_rank_profiles(3);

            //get gravatars 
            $data['pics']=array();
            $count=0;
            if($data['top_profiles'])
            {
                foreach($data['top_profiles'] as $profile)
                {
                    $data['pics'][$count]=$this->gravatar->get($profile['email'],$size=128,$default_image ='mm');
                    $count++;
                }
            }
            $this->load->view('templates/head',$data);
            $this->load->view('pages/home');
        }
        else //Load the corrisponding static page including header and footer.
        {
            $data['page']=str_replace('_', ' ', $page);
            $this->load->view('templates/header',$data);
            $this->load->view('pages/'.$page);
            $this->load->view('templates/footer');
        }
    }
}
