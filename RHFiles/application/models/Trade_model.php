<?php

/**
 * The Trade model is used to carry out database queries relating to trades 
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
 */
class Trade_model extends CI_Model
{
    /**
    * This function uses a transaction to mark a proposal as complete and create an entry in the trade table
    *
    * @param int $skill_proposal_id The proposal id that will be marked as completed
    * @return bool True on Successful insert
    */
    public function create_trade_and_mark_proposal_complete($skill_proposal_id)
    {
        //start transaction
        $this->db->trans_start();

        $skill_proposal=$this->proposal_model->read_skill_proposal($skill_proposal_id);//read proposal
        $this->proposal_model->update_proposal_status_as_complete($skill_proposal_id);//mark proposal as complete

        //read initial and offer skill scores and user ids
        $initiator_id_and_score=$this->initialoffer_model->read_initial_skill_user_id_and_score($skill_proposal['initial_skill_id']);
        $offerer_id_and_score=$this->initialoffer_model->read_offer_skill_user_id_and_score($skill_proposal['offer_skill_id']);
        
        //update opposite users trade count and score 
        $this->profile_model->update_trade_count($initiator_id_and_score['user_id'],$offerer_id_and_score['user_id']);
        $this->profile_model->update_score($initiator_id_and_score,$offerer_id_and_score);

        $trade=array(
            'trade_id'=>NULL,
            'initial_skill_id'=>$skill_proposal['initial_skill_id'],
            'offer_skill_id'=>$skill_proposal['offer_skill_id'],
            'completed_time'=>NULL);

        //insert trade entry
        $this->db->insert('Trade',$trade);

        $this->db->trans_complete();

        return $this->db->trans_status();

    }

    /**
    * This function is used to read a users trade history listing the trades a user has been involved in as either the initial or of skill owner.
    * The 10 most recent trades the user was involved in will be returned
    *
    * @param int $user_id The users id to retrieve trade history for
    * @return mixed Associative Array showing maximum of 10 trades in decending order or false on failure
    */
    public function read_trade_history($user_id)
    {
        $row_count=0; //Counter for array
        $all_trades=NULL;

        for($half=1;$half<=2;$half++) //2 loops of query 1 for initial skill, 1 for offer skill
        {
            if($half==1){ //table names set for owning initial skill
                $table_name="Initial_Skill"; 
                $opposite_name="Offer_Skill";
            }
            else if($half==2){ // table names set for owning offering skills
                 $table_name="Offer_Skill"; 
                 $opposite_name="Initial_Skill";
            }
            
            $table_name=$this->db->query(
           "SELECT Trade.completed_time,Proposer_Name.username,
                   Skill.name,Trade.offer_skill_id,Offer_Skill.offer_skill_id,
                   Proposer.user_skill_id,Proposer_Skill.name AS other_name 
            FROM Skill 
                INNER JOIN User_Skill 
                    ON User_Skill.skill_id = Skill.skill_id
                INNER JOIN User
                    ON User.user_id=User_Skill.user_id
                INNER JOIN ".$table_name."
                    ON ".$table_name.".user_skill_id=User_Skill.user_skill_id
                INNER JOIN Trade
                    ON Trade.".strtolower($table_name)."_id=".$table_name.".".strtolower($table_name)."_id
                INNER JOIN ".$opposite_name."
                    ON ".$opposite_name.".".strtolower($opposite_name)."_id=Trade.".strtolower($opposite_name)."_id
                INNER JOIN User_Skill AS Proposer
                    ON ".$opposite_name.".user_skill_id=Proposer.user_skill_id
                INNER JOIN Skill AS Proposer_Skill
                    ON Proposer.skill_id =Proposer_Skill.skill_id
                INNER JOIN User As Proposer_Name
                    ON Proposer.user_id=Proposer_Name.user_id
            WHERE User.user_id=".$this->db->escape($user_id)." 
            ORDER BY Trade.completed_time DESC
            LIMIT 0,10");
        
            
            try
            {
                foreach ($table_name->result_array() as $row)
                {
                    $all_trades[$row_count]=$row;
                    $row_count++;
                } 
            }
            catch(Exception $e)
            {
                //can throw execption for failed foreach but user may have participated as only a trade initiator/initial skill owner
                //returning false will not return the other half of the trade that has values
            }
        }

        if(is_null($all_trades))
        {
            return FALSE;
        }
        
     return $all_trades;    
    }

    
    /**
     * This function reads the trade status of a proposal and can compare status with $status_check_value if $check_mode is true
     * 
     * @param int $skill_proposal_id The skill proposal to check
     * @param bool $check_mode When set will compare the result with the $status_check_value
     * @param string $status_check_value String (inprogress,pending approval,complete)
     * @return mixed Will return the status if $check_mode not set or True if $check_mode set and $status_check_value==result, false on failure or if not matching
     */
    public function read_trade_status($skill_proposal_id,$check_mode=FALSE,$status_check_value=FALSE)
    {
        $query= $this->db->get_where('Skill_Proposal',array('skill_proposal_id'=>$skill_proposal_id));
        if($check_mode && $status_check_value)
        {
            return $query->row()->status==$status_check_value;
        }
        if($query->result())
        {
            return $query->row()->status;
        }
        
        return FALSE;    

    }
    
    /**
     * This function reads the accepted trade date and the opposite username i.e if user was initial skill owner read offer skill owner username and vice versa.
     * Used for populating the view displaying the in-progress trade status.
     * 
     * @param int $current_user_id The current users id
     * @param int The skill proposal to read
     * @return mixed The opposite users username and accepted trade date or false on failure
     */
    public function read_accepted_trade_date_and_opposite_username($current_user_id,$skill_proposal_id)
    {
       if($this->proposal_model->read_if_user_offered_proposal($current_user_id,$skill_proposal_id))
        {
            $proposal=$this->proposal_model->read_proposal_as_offerer($current_user_id,$skill_proposal_id);
        }
         else
        {
            $proposal=$this->proposal_model->read_proposal_as_initial_skill_owner($current_user_id,$skill_proposal_id);
        }
       
        if($proposal['user_id']!=$current_user_id)
        {
            $user_and_date=array(
                'username'=>$proposal['username'],
                'accepted_date'=>$proposal['accepted_date'],
                'mobile'=>$proposal['mobile']);

            return $user_and_date;
        }
        else 
        {
            $user=$this->user_model->read_username_from_skill($proposal['skill_id']);
            $user_and_date=array(
                'username'=>$user['username'],
                'accepted_date'=>$proposal['accepted_date'],
                'mobile'=>$user['mobile']);

            return $user_and_date;

        }
        return FALSE;
    }

    /**
     *This function is used to delete a trade using an initial skill id
     * 
     * @param int $initial_skill_id The Initial skill involved in the trade
     * @return bool Returns true on success
     */
    public function delete_trade($initial_skill_id)
    {
        $this->db->where('initial_skill_id', $initial_skill_id);
        return $this->db->delete('Trade');
    }

    /**
     *This function is used to read a single random trade for testing purposes
     * 
     * @return mixed Returns a trade on success of false on failure
     */
    public function read_trade_for_testing()
    {
       $query= $this->db->query("SELECT * 
                                 FROM Trade
                                 INNER JOIN Skill_Proposal
                                 ON Skill_Proposal.offer_skill_id=Trade.offer_skill_id
                                 WHERE 1 limit 0,1");
    
        if($trade=$query->row_array())
        {
            return $trade;
        }

        return FALSE;
    }
}


?>