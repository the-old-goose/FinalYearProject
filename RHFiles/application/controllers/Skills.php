<?php


defined('BASEPATH') OR exit('No direct script access allowed');


/**
 * This controller handles the logic associated with skills i.e. the marketplace
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 * @var $data Default common data required in all functions.
 * @link <https://www.codeigniter.com/userguide3/tutorial/news_section.html>
 */
class Skills extends CI_Controller 
{    
    /**
     * @var $data The data common to all functions
     */
    var $data;
    
    public function __construct()
    {
        parent::__construct();
        $this->data = array(
            'c_id' => 0,
            'categories'=>$this->getcategories(),
            'skills_per_page'=> 4
        );  
    }

  /**
   * The index function is called when accessing the inital page when you land on the marketplace view.
   * Pagination is initialized,then a HTTP GET request is sent to the API to get all the skills.
   * 
   * @param int $offset Used by pagination to calculate determin which records to return from DB
   */
    public function index($offset =0)
    {    
        //Pagination config
        $config['base_url'] = site_url().'/skills/index/';
        $config['total_rows'] = $this->skill_model->read_all_skills_count();
        $config['per_page'] = $this->data['skills_per_page'];
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $data=$this->data;
        $data['total_rows']=$config['total_rows'];
        
        //tab name
        $data['page']="Marketplace";

        //start benchmarking
        $this->benchmark->mark('index_start');

        //GET Request sent to API fetching skills, recieving JSON and decoding into an associative array
        $params=array('offset'=>$offset,
                      'items_per_page'=>$config['per_page']);

        $data['skills']=$this->api_request->get("skill","specific",$params);
        
        $this->benchmark->mark('index_end');

        //store time taken to retrieving skills using api for display on view 
        $data['query_time']= $this->benchmark->elapsed_time('index_start', 'index_end');
        
        //no_results boolean records if skills were returned,so view can act accordingly
        empty($data['skills']) ? $data['no_results']=true: $data['no_results']=false; 
        
        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('skills/index', $data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * The view function is called when accessing an individual skill on the marketplace.
     * 
     * @param int $skill_id The skill_id of the skill to be retrieved from the Skill table.
     * @param int $user_skill_offset The offset for the current users skills to display for trading
     */
    public function view($skill_id=NULL,$user_skill_offset=0)
    {
        if(!$skill_id) //No skill_id provided redirect to marketplace.
        {
            redirect('skills/index');
        }

        //store users skill offset and set tab title
        $data['user_skill_offset']=$user_skill_offset; 
        $data['page']="View a skill";

        if($this->session->user_id)//If logged in set user_id
        {
            $user_id=$this->session->user_id;
            
            //use api to check if the user owns the skill being viewed
            $params=array('user_id'=>$user_id,
                          'initial_skill_id'=>$skill_id,
                          'id'=>$skill_id,
                          'skill_proposal_id'=>FALSE,
                          'limit'=>1,
                          'offset'=>$user_skill_offset,
                          'return_row'=>TRUE);

            $data['result']=$this->api_request->get("skill","check_user_created_skill",$params);
            

            if($data['result'])
            {
                //If users has create skill prevent them from trading with themselves by setting flags
                $data['my_skill']=TRUE;
                $data['proposers_skill']=FALSE; 
            }
            else 
            {
                //Get the users skills using API to display in view for trading
                $data['my_skill']=FALSE;
                $data['proposers_skill']=$this->api_request->get("skill","specific_users_skills",$params);    
              

                //if user id not found then proposer has no skills
                if(!isset($data['proposers_skill']['user_id']))
                {
                    $data['proposers_skill']=null;
                }
                
                if($user_skill_offset>0 && is_null($data['proposers_skill']['skill_id']))
                {
                    //If the user has atleast 1 skill and if the next users skill is null when applying the next offset, redirect the page so the users skills loop as users_skill_offset will reset to 0.
                    redirect('skills/view/'.$skill_id);
                }
            }
        }
       
        //GET request to API retrieving a single skill using id ,surpressing 404 error from API if not found.
        $data['skill']=$this->api_request->get("skill","single",$params);

        $this->load->library('availability');
        $data['calendar']=$this->availability->generate_calendar(); //Generate calendar Parameters based on todays date.

        empty($data['skill']) ? $data['no_results']=TRUE: $data['no_results']=FALSE; //set flags to allow view to update accordingly
        
        $this->load->view('templates/header', $data);
        $this->load->view('skills/view', $data);
        $this->load->view('templates/footer', $data);     
    }
    
    /**
     * The search function carries out the logic and provides the views when searching for particular skills in the marketplace 
     * 
     * @param int $offset Incremented by pagination, used to calculate which records to return from DB
     */
    public function search($offset=0)
    {
      
        $search_term=$this->user_input->getSearchTerm(); //get users searched term
        
        //Paginiation Config
        $config['base_url'] = site_url().'/skills/search/'; 
        $config['total_rows'] = $this->skill_model->read_all_skills_like_count($search_term);
        $config['per_page'] = $this->data['skills_per_page'];
        $config['uri_segment'] = 3;
        $this->pagination->initialize($config);
        $data=$this->data;
        $data['total_rows']=$config['total_rows'];
        $data['categories']=$this->getcategories();
        
        //set tab title
        $data['page']="Search";

        //start benchmarking
        $this->benchmark->mark('search_start');
        
        //GET request to api to retrieve skills based on search term 
        $params=array('search'=>$search_term,
                      'offset'=>$offset,
                      'items'=>$config['per_page']);

        $data['skills']=$this->api_request->get("skill","search",$params);
        
        //save benchmark times to display in view
        $this->benchmark->mark('search_end');
        $data['query_time']= $this->benchmark->elapsed_time('search_start', 'search_end');

        //set flags to allow view to update accordingly
        empty($data['skills']) ? $data['no_results']=TRUE: $data['no_results']=FALSE;
        
        //load views
        $this->load->view('templates/header',$data);
        $this->load->view('skills/index',$data);
        $this->load->view('templates/footer');
    } 
     
    /**
     * The categories function carries out the logic and provides the views when refining particular skills in the marketplace based on there category
     * 
     * @param int $c_id Id of the category to retrieve skills for.
     * @param int $offset  Incremented by pagination, used to calculate which records to return from DB
     */
    public function categories($c_id=0,$offset=0)
    {
        
        //Pagination config
        $config['base_url'] = site_url().'/skills/categories/'.$c_id.'/';
        $config['total_rows'] = $this->skill_model->read_all_skills_in_category_count($c_id);
        $config['per_page'] = $this->data['skills_per_page'];
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);
        $data['total_rows']=$config['total_rows'];

        //set tab title
        $data['page']="Category Search";

        //GET categories using call to API 
        $data['categories']=$this->getcategories();

        $data['c_id']=$c_id; //store category id used to search
        
        $this->benchmark->mark('inCat_start');

        //GET request to api retrieving skills found in the category based on $c_id passed as parameter
        $params=array('cid'=>$c_id,
                      'offset'=>$offset,
                      'items'=>$config['per_page']);


        $data['skills']=$this->api_request->get("skill","incategory",$params);
        
        $this->benchmark->mark('inCat_end');
       
        //store benchmarking times
        $data['query_time']= $this->benchmark->elapsed_time('inCat_start', 'inCat_end');
    
        //set flags to allow view to update accordingly
        empty($data['skills']) ? $data['no_results']=TRUE: $data['no_results']=FALSE; 
        
        //load views
        $this->load->view('templates/header',$data);
        $this->load->view('skills/index',$data);
        $this->load->view('templates/footer');
    }
    
    public function create_skill()
    {
        
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id
        $data=$this->data;
        
        //Format skill POST values
        $skill_name=substr(strtoupper($this->input->post('skillname')), 0, 15); 
        $desc=substr(ucfirst($this->input->post('skilldesc')),0,252).'...';
        $val=$this->input->post('skillvalue');
        $cat_id=$this->input->post('categoryid');

        if($this->verify_skill->verify_created_skill()===FALSE) //Do the POST values pass validation?
        {
            //Failed validation so reload page with errors
            $data['page']="Create skill";
            $this->load->view('templates/header',$data);
            $this->load->view('pages/create_skill');
            $this->load->view('templates/footer');
        }
        else
        {
            if($this->skill_model->create_skill($user_id,$skill_name,$desc,$val,$cat_id))//If  create skill  insert successful
            {
                $this->session->set_flashdata('succ_msg','Skill Created Successfully'); //redirect and display success msg
                redirect('profiles/view');
            }
            else
            {
                $this->session->set_flashdata('err_msg','Something went wrong'); //Something wrong with DB ,redirect and display fail message 
                redirect('pages/view/create_skill');
            }
        } 
    }
    
    /**
     * This function will send a request to the api retrieving the categories and their ID's
     * 
     * @return array An Associative array containing categories and their id's
     */
    private function getcategories()
    {
        return $categories=$this->api_request->get("category","all",$params=array());
    }
}
?>
