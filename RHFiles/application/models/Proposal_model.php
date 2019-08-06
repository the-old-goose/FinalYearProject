<?php

/**
 * The Proposal model is used to carry out database queries related to proposals
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
 */
class Proposal_model extends CI_Model {

    /**
    * This function creates a proposal using 2 skill ids and proposed dates, returning the insert id if $read_id flag set
    *
    * @param int $initial_skill The id of the initial_skill in the proposal
    * @param int $offer_skill The id of the offer_skill in the proposal
    * @param string $proposed_dates A string of proposed dates in the format: Jan1,Feb12,Mar25
    * @param bool $read_id If True insert id will be returned
    * @return mixed insert id on success or true, false on failure
    */
    public function create_proposal($initial_skill,$offer_skill,$proposed_dates,$read_id=FALSE)
    {
        $this->db->trans_start();//start transaction

        $initial_user_skill_id=$this->skill_model->read_user_skill_id($initial_skill); //read skill ids
        $offer_user_skill_id=$this->skill_model->read_user_skill_id($offer_skill);

        $initial_skill_id=$this->initialoffer_model->create_initial_skill($initial_user_skill_id); //create entries in initial/offer tables
        $offer_skill_id=$this->initialoffer_model->create_offer_skill($offer_user_skill_id);

        $data=array('skill_proposal_id'=>NULL,
            'initial_skill_id'=>$initial_skill_id,
            'offer_skill_id'=>$offer_skill_id,
            'status'=>"pending approval",
            'proposal_time'=>NULL,
            'proposed_date'=>$proposed_dates);

        $this->db->insert('Skill_Proposal',$data);//insert into proposal table
        $id=$this->db->insert_id();
        $this->db->trans_complete();

        if($read_id && $this->db->trans_status()) //if insert successful and read_id flag true return id
        {
            return $id;
        }

        return $this->db->trans_status();
    }

    /**
    * This function retrieves a skill proposal
    *
    * @param int $skill_proposal_id The id of the skill proposal to be read
    * @return array The Associative array containing the skill proposal details
    */
    public function read_skill_proposal($skill_proposal_id)
    {
        $query=$this->db->get_where('Skill_Proposal',array('skill_proposal_id'=>$skill_proposal_id));
        return $query->row_array();
    }

    /**
    * This function retrieves a skill proposal proposed dates
    *
    * @param int $skill_proposal_id The id of the skill proposal to be read
    * @return mixed The proposed dates of a skill proposal, false on failure
    */
    public function read_proposed_dates($skill_proposal_id)
    {
        $query= $this->db->get_where('Skill_Proposal',array('skill_proposal_id'=>$skill_proposal_id));
        
        if(is_null($query->row()->proposed_date))
        {
            return FALSE;
        }
        
        return $query->row()->proposed_date;
    }

    /**
    * This function retrieves a skill proposal accepted dates
    *
    * @param int $skill_proposal_id The id of the skill proposal to be read
    * @return mixed The accepted dates of a skill proposal,false on failure
    */
    public function read_accepted_date($skill_proposal_id)
    {
        $query= $this->db->get_where('Skill_Proposal',array('skill_proposal_id'=>$skill_proposal_id));

        if(is_null($query->row()->accepted_date))
        {
            return FALSE;
        }

        return $query->row()->accepted_date;
    }

    /**
    * This function returns the number of proposals a user is involved in as either the initial skill owner or offer skill owner
    *
    * @param int $user_id The user id to check
    * @param bool If true will check the proposals user is the offer skill owner, opposite for false
    * @return int The number of proposals the user is either the initial skill or offer skill owner
    */
    public function read_proposal_count($user_id,$as_proposer=FALSE)
    {
        if($as_proposer)
        {
            $query=$this->db->query("SELECT COUNT(*)
                                     FROM Skill 
                                     INNER JOIN User_Skill 
                                     ON Skill.skill_id=User_Skill.skill_id 
                                     INNER JOIN Offer_Skill
                                     ON User_Skill.user_skill_id=Offer_Skill.user_skill_id
                                     INNER JOIN Skill_Proposal
                                     ON Offer_Skill.Offer_skill_id=Skill_Proposal.offer_skill_id      
                                     WHERE User_Skill.user_id=".$user_id); 
    
         
            $proposal_count_as_offerer=$query->result_array();
    
            return $proposal_count_as_offerer[0]['COUNT(*)']; 
        }
        

        $query=$this->db->query("SELECT COUNT(*)
                                 FROM Skill 
                                 INNER JOIN User_Skill 
                                 ON Skill.skill_id=User_Skill.skill_id 
                                 INNER JOIN Initial_Skill
                                 ON User_Skill.user_skill_id=Initial_Skill.user_skill_id
                                 INNER JOIN Skill_Proposal
                                 ON Initial_Skill.initial_skill_id=Skill_Proposal.initial_skill_id      
                                 WHERE User_Skill.user_id=".$user_id); 

     
        $proposal_count_as_initiator=$query->result_array();
        return $proposal_count_as_initiator[0]['COUNT(*)'];
    }

    /**
    * This function retrieves proposals where the $user_id is the initial skill owner
    *
    * @param int $user_id The user id to check
    * @param mixed $skill_proposal_id if int set then will retrieve only a specific proposal
    * @param int $limit The maximum number of proposals to return
    * @param int $offset The offset used by the query used for pagination
    * @return mixed Array of proposal details on success, FALSE on failure
    */
    public function read_proposal_as_initial_skill_owner($user_id,$skill_proposal_id=FALSE,$limit=5,$offset=0)
    {
        //Read specific proposal using user_id and proposal_id where the user owns the initial skill
        if($skill_proposal_id)
        {
            $query=$this->db->query("SELECT Skill_Proposal.skill_proposal_id,Skill_Proposal.offer_skill_id,Skill.skill_id,Skill.name,Skill_Proposal.status,Skill_Proposal.accepted_date
            FROM Skill 
            INNER JOIN User_Skill 
                ON Skill.skill_id=User_Skill.skill_id 
            INNER JOIN Initial_Skill
                ON User_Skill.user_skill_id=Initial_Skill.user_skill_id
            INNER JOIN Skill_Proposal
                ON Initial_Skill.initial_skill_id=Skill_Proposal.initial_skill_id      
            WHERE User_Skill.user_id=".$user_id." AND Skill_Proposal.skill_proposal_id=".$skill_proposal_id);
        
            if(!$result=$query->row_array()) //no results found
            {
                return FALSE;
            }
        
        //Read the offer skill
        $offer_skill=$this->initialoffer_model->read_offer_skill($result['offer_skill_id']);

        //Build array using the inital skill from first query and offer skill in second query 
        $proposals=array(
            'skill_proposal_id'=>$result['skill_proposal_id'],
            'skill_id'=>$result['skill_id'],
            'name'=>$result['name'],
            'status'=>$result['status'],
            'accepted_date'=>$result['accepted_date'],
            'offer_skill_id'=>$offer_skill['skill_id'],
            'offered_skill'=>$offer_skill['name'],
            'user_id'=>$offer_skill['user_id'],
            'username'=>$offer_skill['username'],
            'mobile'=>$offer_skill['mobile'],
            'pic'=>$this->gravatar->get($offer_skill['email'],$size=128,$default_image ='mm'));
    
         return $proposals; //Return proposal

        }
        else //No proposal_id so get all the proposals the user is involved in as the inital skill owner using their user_id
        {
            $query=$this->db->query("SELECT Skill_Proposal.skill_proposal_id,Skill_Proposal.offer_skill_id,Skill.skill_id,Skill.name,Skill_Proposal.status
            FROM Skill 
            INNER JOIN User_Skill 
            ON Skill.skill_id=User_Skill.skill_id 
            INNER JOIN Initial_Skill
            ON User_Skill.user_skill_id=Initial_Skill.user_skill_id
            INNER JOIN Skill_Proposal
            ON Initial_Skill.initial_skill_id=Skill_Proposal.initial_skill_id      
            WHERE User_Skill.user_id=".$user_id." AND Skill_Proposal.status !='complete'".
            " LIMIT ".$offset.",".$limit); 

        }

        if(!$all_proposals_as_initial_skill_owner=$query->result_array())
        {
            return FALSE;
        }

        $count=0;
        foreach ($all_proposals_as_initial_skill_owner as $proposal) 
        {
            $offer_skill=$this->initialoffer_model->read_offer_skill($proposal['offer_skill_id']); 

            $proposals[$count]=array(
                'skill_proposal_id'=>$proposal['skill_proposal_id'],
                'skill_id'=>$proposal['skill_id'],
                'name'=>$proposal['name'],
                'status'=>$proposal['status'],
                'offer_skill_id'=>$offer_skill['skill_id'],
                'offered_skill'=>$offer_skill['name'],
                'user_id'=>$offer_skill['user_id'],
                'username'=>$offer_skill['username'],
                'email'=>$offer_skill['email'],
                'mobile'=>$offer_skill['mobile'] );

            $count++;

        }
    
        return $proposals;

   }

    /**
    * This function retrieves proposals where the $user_id is the offer skill owner
    *
    * @param int $user_id The user id to check
    * @param mixed $skill_proposal_id if int set then will retrieve only a specific proposal
    * @param int $limit The maximum number of proposals to return
    * @param int $offset The offset used by the query used for pagination
    * @return mixed Array of proposal details on success, FALSE on failure
    */
   public function read_proposal_as_offerer($user_id,$skill_proposal_id=FALSE,$limit=5,$offset=0)
    {
        if($skill_proposal_id)
        {
            $query=$this->db->query("SELECT Skill_Proposal.skill_proposal_id,Skill_Proposal.initial_skill_id,Skill.skill_id,Skill.name,Skill_Proposal.status,Skill_Proposal.accepted_date
                                     FROM Skill 
                                     INNER JOIN User_Skill 
                                     ON Skill.skill_id=User_Skill.skill_id 
                                     INNER JOIN Offer_Skill
                                     ON User_Skill.user_skill_id=Offer_Skill.user_skill_id
                                     INNER JOIN Skill_Proposal
                                     ON Offer_Skill.offer_skill_id=Skill_Proposal.offer_skill_id      
                                     WHERE User_Skill.user_id=".$user_id." AND Skill_Proposal.skill_proposal_id=".$skill_proposal_id);
     
            $result=$query->row_array();
            $initial_skill=$this->initialoffer_model->read_initial_skill($result['initial_skill_id']);
     
            $proposals=array(
                'skill_proposal_id'=>$result['skill_proposal_id'],
                'skill_id'=>$result['skill_id'],
                'name'=>$result['name'],
                'status'=>$result['status'],
                'accepted_date'=>$result['accepted_date'],
                'user_id'=>$initial_skill['user_id'],
                'username'=>$initial_skill['username'],
                'email'=>$initial_skill['email'],
                'mobile'=>$initial_skill['mobile']);
    
            return $proposals;
    
        }
        else
        {
            $query=$this->db->query("SELECT Skill_Proposal.skill_proposal_id,Skill_Proposal.initial_skill_id,Skill.skill_id,Skill.name,Skill_Proposal.status
            FROM Skill 
            INNER JOIN User_Skill 
            ON Skill.skill_id=User_Skill.skill_id 
            INNER JOIN Offer_Skill
            ON User_Skill.user_skill_id=Offer_Skill.user_skill_id
            INNER JOIN Skill_Proposal
            ON Offer_Skill.offer_skill_id=Skill_Proposal.offer_skill_id      
            WHERE User_Skill.user_id=".$user_id." AND Skill_Proposal.status !='complete'".
            " LIMIT ".$offset.",".$limit); 
    
         }
         
         $result=$query->result_array();

        $count=0;
        if($result)
        {
            foreach ($result as $r)
            {
                $initial_skill=$this->initialoffer_model->read_initial_skill($r['initial_skill_id']);
                $proposals[$count]=array(
                    'skill_proposal_id'=>$r['skill_proposal_id'],
                    'skill_id'=>$r['skill_id'],
                    'name'=>$r['name'],
                    'status'=>$r['status'],
                    'initial_skill_id'=>$initial_skill['skill_id'],
                    'initial_skill'=>$initial_skill['name'],
                    'user_id'=>$initial_skill['user_id'],
                    'username'=>$initial_skill['username'],
                    'email'=>$initial_skill['email'],
                    'mobile'=>$initial_skill['mobile'] );

                    $count++;
            }
        }
        else
        {
            return FALSE;
        }
            
        return $proposals;
        
    }
    
    /**
    * This function retrieves the initial skill of a proposal using a $skill_proposal_id
    *
    * @param int $skill_proposal_id
    * @return mixed Skill_id returned on success, FALSE on failure
    */
    public function read_initial_skill_using_proposal_id($skill_proposal_id)
    {
        $query=$this->db->query(
                                "SELECT Skill.skill_id
                                FROM Skill
                                INNER JOIN User_Skill
                                ON User_Skill.skill_id=Skill.skill_id 
                                INNER JOIN Initial_Skill
                                ON Initial_Skill.user_skill_id=User_Skill.user_skill_id
                                INNER JOIN Skill_Proposal
                                ON Skill_Proposal.initial_skill_id=Initial_Skill.initial_skill_id
                                WHERE Skill_Proposal.skill_proposal_id=".$skill_proposal_id);
                            
       if($skill_id=$query->row(0)->skill_id)
       {
            return $skill_id=$query->row(0)->skill_id;
       }

       return FALSE;
    }

    /**
    * This function retrieves true if a user initiated the proposal i.e. owns the offer skill of a skill proposal
    *
    * @param int $current_user_id The user id to check
    * @param int $skill_proposal_id The skill proposal to check
    * @return bool True if user owns the offer skill of a proposal
    */
    public function read_if_user_offered_proposal($current_user_id,$skill_proposal_id)
    {
      
        $query=$this->db->query("SELECT User_Skill.user_skill_id
                                 FROM User_Skill
                                 INNER JOIN Offer_Skill
                                 ON User_Skill.user_skill_id=Offer_Skill.user_skill_id
                                 INNER JOIN Skill_Proposal
                                 ON Offer_Skill.offer_skill_id=Skill_Proposal.offer_skill_id      
                                 WHERE User_Skill.user_id=".$current_user_id." AND Skill_Proposal.skill_proposal_id=".$skill_proposal_id);
       
        if ($result=$query->row_array())
        {
            return TRUE; 
        }

        return FALSE;
    }

    /**
    * This function updates a proposals status as inprogress and sets the accepted date
    *
    * @param int $skill_proposal_id The skill proposal to update
    * @param string $accepted_date A single accepted date in the format: Jan10
    * @return bool True on successful update
    */
    public function update_proposal_inprogress($skill_proposal_id,$accepted_date)
    {
        $data = array(
            'status'=>'inprogress',
            'accepted_date'=>$accepted_date);

        $this->db->where('skill_proposal_id', $skill_proposal_id);
        return $this->db->update('Skill_Proposal', $data);
    }

    /**
    * This function checks if a user is involved in a skill proposal i.e the offer skill owner or initial skill owner using their $user_id
    *
    * @param int $user_id The user to check
    * @param int $skill_proposal_id The skill proposal to check
    * @param int $check_skill_owner True if checking user owns the initial skill of a proposal
    * @return bool True on involved in proposal and if inital skill owner if $check_skill_owner set , false if not involved or if $check_skill_owner and not inital skill owner
    */
    public function read_if_user_involved_in_proposal($user_id,$skill_proposal_id,$check_skill_owner=FALSE)
    {
    
        $query=$this->db->query("SELECT User.user_id
                                 FROM User
                                 INNER JOIN User_Skill
                                 ON User_Skill.user_id=User.user_id 
                                 INNER JOIN Initial_Skill
                                 ON Initial_Skill.user_skill_id=User_Skill.user_skill_id
                                 INNER JOIN Skill_Proposal
                                 ON Skill_Proposal.initial_skill_id=Initial_Skill.initial_skill_id
                                 WHERE Skill_Proposal.skill_proposal_id=".$skill_proposal_id);

        $read_initial_user_id= $query->row_array();
                
        if(!is_null($read_initial_user_id))
        {
            if($read_initial_user_id['user_id'] == $user_id)
            {
                return TRUE;
            }
        }

        if($check_skill_owner) 
        {
            return FALSE;
        }
        
        $query2=$this->db->query("SELECT User.user_id
                                 FROM User
                                 INNER JOIN User_Skill
                                 ON User_Skill.user_id=User.user_id 
                                 INNER JOIN Offer_Skill
                                 ON Offer_Skill.user_skill_id=Offer_Skill.user_skill_id
                                 INNER JOIN Skill_Proposal
                                 ON Skill_Proposal.offer_skill_id=Offer_Skill.offer_skill_id
                                 WHERE Skill_Proposal.skill_proposal_id=".$skill_proposal_id);

        $read_offer_user_id= $query2->row_array();
        
        if(!is_null($read_offer_user_id))
        {
            if($read_offer_user_id['user_id'] == $user_id)
            {
                return TRUE;
            }
        }
        
        return FALSE;       
    }

    /**
    * This function updates a proposals status as complete 
    *
    * @param int $skill_proposal_id The skill proposal to update
    * @return bool True on successful update
    */
    public function update_proposal_status_as_complete($skill_proposal_id)
    {
        $data = array(
            'status' => 'complete');
    
      $this->db->where('skill_proposal_id', $skill_proposal_id);
      return $this->db->update('Skill_Proposal', $data);
    }

    /**
    * This function deletes a proposal and its associate offer and initial skill entries
    *
    * @param int $skill_proposal_id The skill proposal to delete
    * @return bool True on successful delete
    */
    public function cancel_proposal($skill_proposal_id)
    {
       return $query=$this->db->query("DELETE Skill_Proposal,Initial_Skill,Offer_Skill
                                       FROM Skill_Proposal
                                       INNER JOIN Initial_Skill
                                       ON Skill_Proposal.initial_skill_id=Initial_Skill.initial_skill_id
                                       INNER JOIN Offer_Skill
                                       ON Offer_Skill.offer_skill_id=Skill_Proposal.offer_skill_id
                                       WHERE Skill_Proposal.skill_proposal_id=".$skill_proposal_id);
          
    }
}

?>