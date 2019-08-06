<?php

/**
 * This model carries out queries associated with the initial_skill and offer_skill tables using the database
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
 */
class Initialoffer_model extends CI_Model {

    /**
    * This function inserts an initial_skill entry into the initial_skill table
    * 
    * @param int $initial_user_skill_id The user_skill id of the initial skill
    * @return bool True if insert successful
    */
    public function create_initial_skill($initial_user_skill_id)
    {
        $data=array('initial_skill_id'=>NULL,
                    'user_skill_id'=>$initial_user_skill_id );

        $this->db->insert('Initial_Skill',$data);

        return $this->db->insert_id();
    }

    /**
    * This function inserts an offer_skill entry into the offer_skill table
    * 
    * @param int $offer_user_skill_id The user_skill id of the offer skill
    * @return bool True if insert successful
    */
    public function create_offer_skill($offer_user_skill_id)
    {
        $data=array('offer_skill_id'=>NULL,
        'user_skill_id'=>$offer_user_skill_id );

        $this->db->insert('Offer_Skill',$data);
        return $this->db->insert_id();
    }

    /**
    * This function reads the initial skill owners user id and score of skill they created
    * 
    * @param int $initial_skill_id The initial skill id to be read
    * @return mixed Associative array containing the user id and score or false on failure
    */
    public function read_initial_skill_user_id_and_score($initial_skill_id)
    {
        $query=$this->db->query("SELECT User.user_id,Skill.value
                                FROM User
                                INNER JOIN User_Skill
                                ON User_Skill.user_id=User.user_id 
                                INNER JOIN Skill
                                ON User_Skill.skill_id=Skill.skill_id
                                INNER JOIN Initial_Skill
                                ON User_Skill.user_skill_id=Initial_Skill.user_skill_id
                                WHERE Initial_Skill.initial_skill_id=".$initial_skill_id);
    
        if(!$query->row_array())
        {
            return FALSE;
        }

        return $query->row_array();
    }

    /**
    * This function reads the initial skill using an initial skill id
    * 
    * @param int $initial_skill_id The initial skill id to be read
    * @return mixed Associative array containing initial skill or false on failure
    */
    public function read_initial_skill($initial_skill_id)
    {
         
         $query=$this->db->query("SELECT Skill.name,Skill.skill_id,User.username,User.user_id,User.email,User.mobile
                                  FROM Skill 
                                     INNER JOIN User_Skill 
                                         ON Skill.skill_id=User_Skill.skill_id
                                     INNER JOIN  Initial_Skill
                                         ON User_Skill.user_skill_id=Initial_Skill.user_skill_id
                                     INNER JOIN User
                                         ON User_Skill.user_id=User.user_id
                                  WHERE Initial_Skill.initial_skill_id=".$initial_skill_id);   
        
         
         if(!$result=$query->row_array())
         {
             return FALSE;
         }

         $initial_skill=array(
             'skill_id'=>$result['skill_id'],
             'user_id'=>$result['user_id'],
             'name'=>$result['name'],
             'username'=>$result['username'],
             'email'=>$result['email'],
             'mobile'=>$result['mobile']
         );
         
         return $initial_skill;
    }

    /**
    * This function reads the offer skill using an offer skill id
    * 
    * @param int $offer_skill_id The offer skill id to be read
    * @return mixed Associative array containing the offer skill or false on failure
    */
    public function read_offer_skill($offer_skill_id)
    {
         
         $query=$this->db->query("SELECT Skill.name,Skill.skill_id,User.username,User.user_id,User.email,User.mobile
                                  FROM Skill 
                                     INNER JOIN User_Skill 
                                         ON Skill.skill_id=User_Skill.skill_id
                                     INNER JOIN  Offer_Skill
                                         ON User_Skill.user_skill_id=Offer_Skill.user_skill_id
                                     INNER JOIN User
                                         ON User_Skill.user_id=User.user_id
                                  WHERE Offer_Skill.offer_skill_id=".$offer_skill_id); 

         if(!$result=$query->row_array())
         {
             return FALSE;
         }
         
         $offer_skill=array(
             'skill_id'=>$result['skill_id'],
             'user_id'=>$result['user_id'],
             'name'=>$result['name'],
             'username'=>$result['username'],
             'email'=>$result['email'],
             'mobile'=>$result['mobile']
         );
 
         return $offer_skill;
    }

    /**
    * This function reads the offer skill owners user id and score of skill they created
    * 
    * @param int $offerskill_id The offer skill id to be read
    * @return mixed Associative array containing the user id and score or false on failure
    */
    public function read_offer_skill_user_id_and_score($offer_skill_id)
    {
        $query=$this->db->query("SELECT User.user_id,Skill.value
                                 FROM User
                                 INNER JOIN User_Skill
                                 ON User_Skill.user_id=User.user_id 
                                 INNER JOIN Skill
                                 ON User_Skill.skill_id=Skill.skill_id
                                 INNER JOIN Offer_Skill
                                 ON User_Skill.user_skill_id=Offer_Skill.user_skill_id
                                 WHERE Offer_Skill.offer_skill_id=".$offer_skill_id);

        if(!$query->row_array())
        {
            return FALSE;
        }

        return $query->row_array();               
    }
}


?>