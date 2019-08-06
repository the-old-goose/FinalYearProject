<?php

/**
 * The skill model is used to carry out database queries related to skills 
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
 * 
 */
class Skill_model extends CI_Model{
    
    /**
    * This function will create a user, inserting the values into the User table and profile table in a transaction
    * 
    * @param int $user_id        The user creating the skill
    * @param string $skill_name  The name of the skill
    * @param string $description The description of the skill
    * @param int $value          The value of the skill i.e. 5-100
    * @param int $category_id    The category the skill belongs to
    * @param bool $read_id       True if the insert id is to be returned
    * @return mixed True on successful insertion, insert id if $read_id flag is True , False on failure
    */
    public function create_skill($user_id,$skill_name,$description,$value,$category_id,$read_id=FALSE)
    {
        $skill= array(
            'skill_id'=>NULL,
            'name'=>$skill_name,
            'description'=>$description,
            'value'=>$value,
            'date_added'=>NULL,
            'category_id'=>$category_id );

        $this->db->trans_start();
        $this->db->insert('Skill',$skill);

        $user_skill=array(
            'user_skill_id'=>NULL,
            'skill_id'=>$this->db->insert_id(),
            'user_id'=>$user_id,
            'date_added'=>NULL);

        $this->db->insert('User_Skill',$user_skill);

        $this->db->trans_complete();
        
        if($read_id && $this->db->trans_status())
        {
            return $user_skill['skill_id'];
        }
        return $this->db->trans_status();
    }

    /**
     * This function will query the database returning the number of records in the Skill table
     * 
     * @return int Number of records in Skill Table.
     */
    public function read_all_skills_count()
    {
        return $this->db->count_all('Skill');
    }

    /**
     * This function will return the number of skills a user has created
     * 
     * @return int Number of skills a user has
     */
    public function read_users_skills_count($user_id)
    {
        $this->db->where('user_id',$user_id);
        $this->db->from('User_Skill');
        return $this->db->count_all_results(); 
    }
    
    /**
     * This function takes a $term and counts how many skills have a 'name' like the string $term returning the number
     * 
     * @param string $term A term to check against skill name attribute.
     * @return int Number of records that have a name field like '$term'
     */
    public function read_all_skills_like_count($term="")
    {
        return $this->db->like('name',$term)->from("Skill")->count_all_results();
    }
    
    /**
     * This function returns the number of skills where their category_id=$c_id
     * 
     * @param int $category_id  A category_id to check
     * @return int Number of skill records with category_id = $c_id
     */
    public function read_all_skills_in_category_count($category_id)
    {
        return $this->db->where('category_id',$category_id)->from('Skill')->count_all_results();
    }
    
    /**
     * This function returns an Associative array of all the skills in the Skill table
     * 
     * @return mixed Array of all of the skills in the database, False on failure
     */
    public function read_all_skills()
    {
        $query =$this->db->get('Skill');

        if(!$query->result_array())
        {
            return FALSE;
        }

        return $query->result_array();
    }
    
    /**
     * This function returns either a single skill as an associative array if $skill_id is set. Otherwise returns
     * an associate array of a set of skills using a limit and offset used in the marketplace with pagination or view individual skill when id passed
     * 
     * @param boolean $skill_id A specific skills id, Default=False(not passed)
     * @param int $limit     A maximum number of results to be returned 
     * @param int $offset    The offset for finding the set of skills
     * @return mixed An Associative array containing skill record(s) from the Skill table, False on failure
     */
    public function read_specific_skills($skill_id = FALSE,$limit=5,$offset=0)
    {
        if($limit) //limit set so use limit and offset
        {
            $this->db->limit($limit,$offset);
        }

        if($skill_id === FALSE) //if no skill id 
        {
            $query =$this->db->get('Skill'); // get all skills using limit and offset
            $skills=$query->result_array();
                
            if(!$skills)
            {
                return FALSE;
            }

            $count=0;
            foreach($skills as $skill) // read the username of each skill creator and build gravatar string storing in new assoc array
            {
                $skills_and_user[$count]=array(
                    'skill_id'=>$skill['skill_id'],
                    'name'=>$skill['name'],
                    'description'=>$skill['description'],
                    'value'=>$skill['value'],
                    'date_added'=>$skill['date_added'],
                    'category_id'=>$skill['category_id'],
                    'username'=>$this->user_model->read_username_from_skill($skill['skill_id'],FALSE,TRUE),
                    'picture'=>base_url()."images/skill-graphic/marketplace-".$skill['category_id'].".png");
                
                $count++;
            }
              
            return $skills_and_user;
        }
        
        $query= $this->db->where('Skill.skill_id',$skill_id) // get a single skill as skill id passed
                        ->join('User_Skill', 'User_Skill.skill_id=Skill.skill_id')
                        ->get('Skill');

        if(!$skill=$query->row_array()) //if no result 
        {
            return FALSE;
        }
       
        $initial_skill=array( //create gravatar url of skill using email
            'skill_id'=>$skill['skill_id'],
            'name'=>$skill['name'],
            'description'=>$skill['description'],
            'value'=>$skill['value'],
            'picture'=>$this->gravatar->get($this->user_model->read_email($skill['user_id']),$size=256,$default_image ='mm'),
            'user_id'=>$skill['user_id'] );

        return $initial_skill;
    }
    
    /**
     * This function returns true if a user created a skill
     * 
     * @param int $user_id The user to check
     * @param int $skill_id The skill to check
     * @param int $skill_proposal_id The skill proposal id to check, default=FALSE
     * @return bool True if user created skill or False in not or on failure
     */
    public function read_if_user_created_skill($user_id,$skill_id,$skill_proposal_id=FALSE)
    {
        if($skill_proposal_id)
        {
            $skill_id=$this->proposal_model->read_initial_skill_using_proposal_id($skill_proposal_id);
            
            if(!$skill_id)//Proposal does not exist
            {
                return FALSE;
            }
        }

    
        $query=$this->db->query("SELECT User_Skill.skill_id 
                                 FROM User_Skill 
                                 WHERE User_Skill.user_id=".$user_id.
                               " AND User_Skill.skill_id=".$skill_id);

        if($query->result_array())
        {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * This function returns the skills a user has created using a limit and offset
     * 
     * @param int $user_id The user to check
     * @param int $limit The maximum number of skills to return
     * @param int $offset The offset for finding the set of skills
     * @return mixed Associative array of skills or false on failure
     */
    public function read_users_skills($user_id,$limit=FALSE,$offset=0,$return_row=FALSE)
    {
        if($limit)
        {
            $query=$this->db->query("SELECT Skill.*,User.user_id,User.email
                                     FROM Skill 
                                     INNER JOIN User_Skill 
                                     ON Skill.skill_id=User_Skill.skill_id 
                                     INNER JOIN User
                                     ON User_Skill.user_id=User.user_id
                                     WHERE User_Skill.user_id=".$user_id." LIMIT ".$offset.",".$limit);

            if($return_row)
            {
                $users_single_skill=$query->row_array();
                $users_single_skill['picture']= $this->gravatar->get($users_single_skill['email'],$size=256,$default_image ='mm');
                return $users_single_skill;
            }

            return $query->result_array();
        }
        
        $query=$this->db->query("SELECT Skill.* 
                                 FROM Skill 
                                 INNER JOIN User_Skill 
                                 ON Skill.skill_id=User_Skill.skill_id 
                                 WHERE User_Skill.user_id=".$user_id);

        $users_skills=$query->result_array();                         

        return $users_skills;
    }

    /**
     * Reads the user skill id of a skill given an associative array containing a skill
     * 
     * @param array $skill The associative array containing a skill
     * @return mixed Int the user id of the skill or false on failure
     */
    public function read_user_skill_id($skill)
    {
        $this->db->select('user_skill_id');
        $this->db->where('skill_id', $skill['skill_id']);
        $query= $this->db->get('User_Skill');

        if(!$query)
        {
            return FALSE;
        }
        return $query->row(0)->user_skill_id;
    }

    /**
     * This function returns an array of skills that have a name attribute like $searchterm with a limit and offset
     * 
     * @param string $searchterm A string to be searched
     * @param int $limit        The limit of results to return , usually defined by 'results_per_page'
     * @param int $offset       The offset for finding the next set of skills,usually defined by pagination URI segment
     * @return array            An Associative array containing skill record(s) from the Skill table
     */
    public function read_search_skills(string $searchterm="",$limit=5,$offset=0)
    {
        $query = $this->db->like('name',$searchterm)
                          ->limit($limit,$offset)
                          ->get('Skill');

        if(!$skills=$query->result_array())
        {
            return FALSE;
        }
                
        $count=0;
        foreach($skills as $skill)
        {
           $skills_and_user[$count]=array(
                'skill_id'=>$skill['skill_id'],
                'name'=>$skill['name'],
                'description'=>$skill['description'],
                'value'=>$skill['value'],
                'date_added'=>$skill['date_added'],
                'category_id'=>$skill['category_id'],
                'username'=>$this->user_model->read_username_from_skill($skill['skill_id'],FALSE,$only_username=TRUE),
                'picture'=>base_url()."images/skill-graphic/marketplace-".$skill['category_id'].".png" );
            
            $count++;
        }
              
        return $skills_and_user;
       
    }
    
    /**
     * This function returns an array of skills that have a category_id attribute matching $c_id with a 
     * limit and offset applied.
     * 
     * @param int $c_id The category_id to check
     * @param int $limit The limit of results to return , usually defined by 'results_per_page'
     * @param int $offset The offset for finding the next set of skills,usually defined by pagination URI segment
     * @return array An associative array of skills 
     */
    public function read_category_search_skills($category_id=0,$limit=5,$offset=0)
    {
       
        if($category_id===0)
        {
            return $query =$this->db->get('Skill')->result_array();
        }
        

        $query=$this->db->select('*')
                        ->where('category_id',$category_id)
                        ->limit($limit,$offset)
                        ->get('Skill');
        
        if(!$skills=$query->result_array())
        {
            return FALSE;
        }
                
        $count=0;
        foreach($skills as $skill)
        {
            $skills_and_user[$count]=array(
                'skill_id'=>$skill['skill_id'],
                'name'=>$skill['name'],
                'description'=>$skill['description'],
                'value'=>$skill['value'],
                'date_added'=>$skill['date_added'],
                'category_id'=>$skill['category_id'],
                'username'=>$this->user_model->read_username_from_skill($skill['skill_id'],FALSE,$only_username=TRUE),
                'picture'=>base_url()."images/skill-graphic/marketplace-".$skill['category_id'].".png");
            
            $count++;
        }
              
        return $skills_and_user;
    }

    /**
     * This function deletes a skill and its user skill entry in a transaction
     * 
     * @param int $skill_id The skill id to delete
     * @return bool True on success
     */
    public function delete_skill(int $skill_id)
    {
    
        $this->db->trans_start();

        $this->db->where('skill_id', $skill_id);
        $this->db->delete('User_Skill');

        $this->db->where('skill_id', $skill_id);
        $this->db->delete('Skill');

        $this->db->trans_complete();

        

        return $this->db->trans_status();
    }

}