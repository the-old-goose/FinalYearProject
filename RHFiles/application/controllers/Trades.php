<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * This controller handles the logic associated with trades 
 *
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 */
class Trades extends CI_Controller 
{
    /**
     * This function is called when a user initiates a trade by offering their skill in the individual skill view creating a skill proposal
     * 
     * @param int $initial_skill_id
     * @param int $offer_skill_id 
     */
    public function initiate($initial_skill_id,$offer_skill_id)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id

        //if skills not provided/not numeric redirect
        if(!is_numeric($initial_skill_id) || !is_numeric($offer_skill_id))
        {
            redirect('skills/index');
        }

        //store id's
        $initial_skill['skill_id']=$initial_skill_id;
        $offer_skill['skill_id']=$offer_skill_id;

        //GET request to api checking if user owns the skill they are making the offer for
        $params=array('user_id'=>$user_id,
                      'initial_skill_id'=>$skill_id,
                      'skill_proposal_id'=>FALSE);

        $data['result']=$this->api_request->get("skill","check_user_created_skill",$params);
         
        //if own skill proposing for
        if($data['result'])
        {
            $this->session->set_flashdata('fail_msg','You Cannot Trade for your own skill');
            redirect('skills/view/'.$initial_skill_id);
        }

        //GET request to api checking user owns the skill they are offering
        $params['initial_skill_id']=$offer_skill_id;
        $data['result']=$this->api_request->get("skill","check_user_created_skill",$params);

        //if user does not owns the offered skill
        if(!$data['result']) 
        {
            $this->session->set_flashdata('fail_msg','You Cannot propose another users skill listing!');
            redirect('skills/view/'.$initial_skill_id);
        }

        //Checks that a date has been selected using the calendar
        if( empty($sel_date=$this->input->post('sel-date')))
        {
            $this->session->set_flashdata('proposal_error','You must select atleast 1 day you are available using the calendar!');
            redirect('skills/view/'.$initial_skill_id);
        }     
        
        //sel_date array concatenated into a single string seperated by commas and inserted as proposed_dates field the proposal table
       $success= $this->proposal_model->create_proposal($initial_skill,$offer_skill,implode(",",$sel_date));

       if(!$success) //Notify user if proposal was successfully made
       {
            $this->session->set_flashdata('fail_msg','Proposal Unsuccessful, Something went wrong.');//DB error rediect and display to user
            redirect('skills/view/'.$initial_skill_id);
       }
       else
       {    
            $this->session->set_flashdata('succ_msg','Proposal Successfully Made!');
            redirect('listings/view_sent_offers');
       }
    }

    /**
     * This function is used to populate the view that shows the original skill owner the proposed skill and dates allowing them to 
     * make a decision
     * 
     * @param int skill_proposal_id The skill proposal to be confirmed or cancelled
     */
    public function decide($skill_proposal_id)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id
        $data['page']="Decide!";

        //GET request to api to check user owns the inital skill in proposal
        $params=array('user_id'=>$user_id,
                      'initial_skill_id'=>NULL,
                      'skill_proposal_id'=>$skill_proposal_id);

        $data['result']=$this->api_request->get("skill","check_user_created_skill",$params);

        //if user does not own initial skill
        if(!$data['result'])
        {
            $this->session->set_flashdata('fail_msg','You are not Authorized to Decide on this proposal ');
            redirect("listings/view_offers");
        }

        //If proposal is not pending approval...
        if(!$this->trade_model->read_trade_status($skill_proposal_id,TRUE,"pending approval"))
        {
            $this->session->set_flashdata('fail_msg','This trade is no longer pending approval');
            redirect("listings/view_offers");
        }

        //Get details of proposal
        $data['proposal']=$this->proposal_model->read_proposal_as_initial_skill_owner($user_id,$skill_proposal_id);

        //if empty set flags so view can display correctly
        empty($data['proposal']) ? $data['noresults']=TRUE: $data['noresults']=FALSE;
        
        //Read proposed dates
        $proposed_dates[]=$this->proposal_model->read_proposed_dates($skill_proposal_id);

        //Generate Calendar with Proposed dates marked with checkboxes in view
        $this->load->library('availability', $proposed_dates);
        $data['calendar']=$this->availability->generate_calendar();
        
        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('trades/decide',$data);
        $this->load->view('templates/footer', $data);
    }

    /**
     * This function is used to populate the view that shows the progress of a proposal
     * 
     * @param int skill_proposal_id The skill proposal which progress is being viewed
     */
    public function view_progress($skill_proposal_id=FALSE)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id
        $data['page']="Trade Progress";

        if(!$skill_proposal_id) //No proposal_id given
        {
            redirect('listings/view_offers');
        }

        //check user involved in proposal and set flag
        if(!$this->proposal_model->read_if_user_involved_in_proposal($user_id,$skill_proposal_id,FALSE))
        {
            //not involved in proposal
            $data['status']=FALSE;
        }
        else
        {
            //involved in proposal so read status of the trade using api
            $params=array('skill_proposal_id'=>$skill_proposal_id);
            $data['status']=$this->api_request->get("trade","read_trade_status",$params);

        }

        if($data['status']=='inprogress') // If the trade is 'inprogress' read the other users name and accepted date of trade
        {
            
           $data['proposal']=$this->trade_model->read_accepted_trade_date_and_opposite_username($user_id,$skill_proposal_id);
           //format the date string to display on view
           $this->load->library('availability');
           $data['date']=$this->availability->get_date_string($data['proposal']['accepted_date']);
        }

        $data['skill_proposal_id']=$skill_proposal_id;

        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('trades/progress',$data);
        $this->load->view('templates/footer', $data);  
    }

    /**
     * This function is used to populate the view that shows the users trade history
     * 
     */
    public function view_history()
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id

        //set tab title
        $data['page']="History";
        
        //Read trade history
        $data['trades']=$this->trade_model->read_trade_history($user_id);

        //Set flag based on result so view can display appropriatly 
        empty($data['trades']) ? $data['no_results']=TRUE: $data['no_results']=FALSE;

        //load views
        $this->load->view('templates/header', $data);
        $this->load->view('listings/history',$data);
        $this->load->view('templates/footer', $data);  
    }

    /**
     * This function is used when a initial skill owner accepts a proposal
     * 
     * @param $skill_proposal_id The proposal to be accepted
     */
    public function accept_proposal($skill_proposal_id)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id

        //verify initial skill owner
        if(!$this->proposal_model->read_if_user_involved_in_proposal($user_id,$skill_proposal_id,$check_skill_owner=TRUE))
        {
            //not initial skill owner so..
            $this->session->set_flashdata('fail_msg','You are not Authorized to Decide on this proposal!');
            redirect('Trades/decide/'.$skill_proposal_id);
        }
        
        //verify accepted date has been chosen
        if( empty($accepted_date=$this->input->post('avail_selected')))
        {
            //no date chosen
            $this->session->set_flashdata('fail_msg','You must confirm a single day you are available using the calendar!');
            redirect('Trades/decide/'.$skill_proposal_id);
        }     
        
    
        if($accepted_date<date('j')) //If selected date is less than  less than day of month it is..
        {
            $accepted_date=date('M',strtotime('+1 month')).$accepted_date; //append a month prefix e.g Jan onto the date
        }
        else //else including date today: append the current months prefix e.g Dec
        {
            $accepted_date=date('M').$accepted_date; 
        }

        //Mark the proposal as 'in progress'
        $success=$this->proposal_model->update_proposal_inprogress($skill_proposal_id,$accepted_date);
        
        if(!$success) //database error let user know update unsuccessful
        {
            $this->session->set_flashdata('fail_msg','Accepting the trade was unsuccessful');
            redirect("listings/view_offers");
        }

        //proposal successfully accepted and now deemed in progress
        $this->session->set_flashdata('succ_msg','Trade accepted!');
        redirect('trades/view_progress/'.$skill_proposal_id); 
    }

    /**
     * This function is used when a party involved in a proposal cancels the proposal
     * 
     * @param $skill_proposal_id The proposal to be cancelled
     */
    public function cancel_proposal($skill_proposal_id)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id

        //Check user is Involved in the proposal
        if(!$this->proposal_model->read_if_user_involved_in_proposal($user_id,$skill_proposal_id))
        {
            //not involved so..
            $this->session->set_flashdata('fail_msg','You are not Authorized to Cancel this proposal!');
            redirect('Trades/decide/'.$skill_proposal_id);
        }

        //GET request to api reading proposal status
        $params=array('skill_proposal_id'=>$skill_proposal_id,
                      'check_mode'=>0,
                      'status_check_value'=>0);

        $data['status']=$this->api_request->get("trade","read_trade_status",$params);


        //if proposal status not read...
        if(!$data['status'])
        {
            $this->session->set_flashdata('fail_msg','This proposals status could not be confirmed , Contact support!');
            redirect('listings/view_offers');
        } 
        if (!strcmp($status,"pending approval")&& !strcmp($status,"inprogress") )
        {
            //if proposal is not inprogress or pending approval i.e. completed
            $this->session->set_flashdata('fail_msg','This proposal cannot be cancelled!');
            redirect('listings/view_offers');
        }

       //delete the proposal
       $delete_successful= $this->proposal_model->cancel_proposal($skill_proposal_id);

       if($delete_successful) //notify the user appropriately
       {
          $this->session->set_flashdata('succ_msg','Skill Proposal Cancelled Successfully');
       }
       else
       {
         $this->session->set_flashdata('err_msg','Could Not Delete This Proposal, Contact Support!');
       }

        redirect('listings/view_offers');
    }

    /**
     * This function is used when a party involved in a proposal marks it as completed
     * 
     * @param $skill_proposal_id The proposal to be completed
     */
    public function complete_trade($skill_proposal_id)
    {
        $user_id=$this->verify_user->checkLoggedIn(); //Verify user logged in, get user_id

        //GET request to api checking trade is in progress
        $params=array('skill_proposal_id'=>$skill_proposal_id,
                      'check_mode'=>1,
                      'status_check_value'=>'inprogress');

        $data['status']=$this->api_request->get("trade","read_trade_status",$params);

        //if trade not in progress...
        if(!$status=$this->trade_model->read_trade_status($skill_proposal_id,TRUE,"inprogress"))
        {
            $this->session->set_flashdata('fail_msg','This proposal cannot be completed as its not: in progress!');
            redirect('listings/view_offers');
        }

        //Read accepted date, if cant read then notify user
        if(!$accepted_date=$this->proposal_model->read_accepted_date($skill_proposal_id))
        {
            $this->session->set_flashdata('fail_msg','Cannot Read the Accepted Date, Contact Support!');
            redirect('listings/view_offers');
        }

        //If the accepted date is greater than todays date
        if($accepted_date>date('Mj'))
        {
            //format date
            $this->load->library('availability');
            $formatted_date=$this->availability->get_date_string($accepted_date);
            //The day today is less than the accepted date so notify user they cannot mark it as complete yet
            $this->session->set_flashdata('warn_msg','You Cannot Complete the trade untill '.$formatted_date);
            redirect('trades/view_progress/'.$skill_proposal_id);
        }

        //Try to Insert in trade table, updated users trade count and scores, if failed Warn user unsuccessful...
        if(!$success=$this->trade_model->create_trade_and_mark_proposal_complete($skill_proposal_id))
        {
            $this->session->set_flashdata('fail_msg','Trade Could Not Be Completed, Contact Support!');
            redirect('listings/view_offers');
        }

        redirect('trades/view_progress/'.$skill_proposal_id);
    }
}

?>