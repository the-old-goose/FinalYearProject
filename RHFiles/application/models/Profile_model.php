<?php

/**
 * This model carries out queries associated with the profile table using the database
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
*/
class Profile_model extends CI_Model {

    /**
    * This function inserts a profile entry into the profile table
    * 
    * @param int $user_id The user_id associated with the profile
    * @param string $first_name The first name of the user
    * @param string $last_name The lastname of the user
    * @param int $location_id The location id of the user
    * @return mixed Int of insert on success, or FALSE on failure
    */
    public function create_profile($user_id,$first_name,$last_name,$location_id=1)
    {
        $profile_data=array('user_id'=>$user_id,
                   'description'=>"Welcome to my page",
                   'score'=>0,
                   'trade_count'=>0,
                   'location_id'=>$location_id,
                   'profile_pic_added'=>0,
                   'year'=>"First",
                   'first_name'=>$first_name,
                   'last_name'=>$last_name );

        if(!$this->db->insert('Profile',$profile_data))
        {
            return FALSE;
        }

       return $this->db->insert_id();
    }

    /**
    * This function retrieves profiles using a search term comparing the profile first or lastname
    * 
    * @param string $searchterm The search term to match the profile first or last name
    * @param int $limit The max number of similar profiles to return
    * @param int $offset The offset of results to start retrieving from
    * @return mixed assoc array of similar profiles on success, or FALSE on failure
    */
    public function read_profiles_using_search($searchterm="",$limit=5,$offset=0)
    {
        $query = $this->db->like('first_name',$searchterm)
                          ->or_like('last_name',$searchterm)
                          ->limit($limit,$offset)
                          ->join('User', 'User.user_id = Profile.user_id')
                          ->join('Location','Profile.location_id = Location.location_id')
                          ->get('Profile');
        
        if(!$profiles=$query->result_array())
        {
            return FALSE; 
        }
        
        $count=0;
        foreach($profiles as $profile)//for each profile build their gravatar string using their email
        {
            $profile_data[$count] =array(
                'user_id'=>$profile['user_id'],
                'first_name'=>$profile['first_name'],
                'last_name'=>$profile['last_name'],
                'halls'=>$profile['name'],
                'image'=>$this->gravatar->get($profile['email'],$size=128,$default_image ='mm'));

            $count++;
        }

       return $profile_data ;
    }

    /**
    * This function retrieves profiles using a user_id
    *
    * @param int $user_id The id of the profile to return
    * @return mixed assoc array of the profile on success, or FALSE on failure
    */
    public function read_profile($user_id)
    {
        $query=$this->db->where('Profile.user_id',$user_id)
                        ->join('User', 'User.user_id = Profile.user_id')
                        ->join('Location','Profile.location_id = Location.location_id')
                        ->get('Profile');
        
       if(!$result=$query->row_array())
       {
            return FALSE;
       }
        
        $profile_data=array(
                   'user_id'=>$result['user_id'],
                   'username'=>$result['username'],
                   'email'=>$result['email'],
                   'description'=>$result['description'],
                   'score'=>$result['score'],
                   'trade_count'=>$result['trade_count'],
                   'location_name'=>$result['name'],
                   'year'=>$result['year'],
                   'first_name'=>$result['first_name'],
                   'last_name'=>$result['last_name'],
                   'rank'=>$this->read_rank($result['user_id']));

        return $profile_data;
    }

    /**
    * This function retrieves number of profiles with a firstname or lastname like $searchterm
    *
    * @param string $searchterm The first or lastname of the profiles to count
    * @return int Number of the profile like search term on success
    */
    public function read_all_like_count($searchterm="")
    {
        return $this->db->like('first_name',$searchterm)->or_like('last_name',$searchterm)->from("Profile")->count_all_results();
    }
   
    /**
    * This function retrieves the score of a profile
    *
    * @param int $user_id The id of the profile which score is to be retrieved
    * @return mixed The score of the profile on success or FALSE on failure
    */
    public function read_score($user_id)
    {
        $query=$this->db->get_where('Profile',array('user_id'=>$user_id));

            if($confirmed=$query->row(0)->score)
            {
                return $confirmed;
            }
            return FALSE;
    }

    /**
    * This function retrieves the rank of a profile by counting the number of profiles that have a score greater than the profile
    *
    * @param int $user_id The id of the profile which rank is to be retrieved
    * @return mixed Therank of the profile on success or FALSE on failure
    */
    public function read_rank($user_id)
    {
        $query=$this->db->query("SELECT count(*) 
                                 FROM Profile 
                                 WHERE score > (SELECT score 
                                                FROM Profile 
                                                WHERE user_id = $user_id)");   
       
        if(!$rank=$query->row_array())
        {
            return FALSE;
        }

        return $rank["count(*)"]+1;//if 0 profiles higher, then rank is 1
    }

    /**
    * This function retrieves the top $amount of highest rank profiles using Profile.score
    *
    * @param int $amount The top $amount of profile with highest scores descending to be retrieved
    * @return mixed Assoc array containing $amount of highest ranked profiles or FALSE on failure
    */
    public function read_highest_rank_profiles($amount=3)
    {
        $query=$this->db->query("SELECT Profile.user_id ,Profile.score,Profile.first_name,User.username,User.email
                                 FROM `Profile`
                                 JOIN User
                                    ON Profile.user_id= User.user_id 
                                 ORDER BY Profile.score DESC 
                                 LIMIT 0,".$amount); 
        
       if($top_profiles=$query->result_array())
       {
           return $top_profiles;
       }

       return FALSE;
    }

    /**
    * This function updates a profile using a user_id and attributes passed
    *
    * @param int $user_id The id of the profile to be updated
    * @param array $attributes The array of attributes to be updated
    * @return bool True on successful update
    */
    public function update_profile($user_id,$attributes){
        
        $data = array(
            'first_name'=>$attributes['first_name'],
            'last_name'=>$attributes['last_name'],
            'location_id'=>$attributes['location_id'],
            'year' => $attributes['year'],
            'description' => $attributes['description'],
        );
    
        $this->db->where('user_id', $user_id);
        return $this->db->update('Profile', $data);

    }

    /**
    * This function sets a profile score, used for testing the highest rank profiles
    *
    * @param int $test_user_id The id of the profile update there score
    * @param int $new_score The new score
    * @return bool True on successful update
    */
    public function set_score($test_user_id,$new_score=0)
    {
        $this->db->where('user_id', $test_user_id);
        $this->db->set('score',$new_score);
        return $this->db->update('Profile');
    }

    /**
    * This function updates both profiles scores that are involved in a trade after it is completed
    *
    * @param array $initiator_id_and_score The id and score of the proposal initial skill owner
    * @param array $offerer_id_and_score   The id and score of the proposal offer skill owner
    * @return bool True on successful update
    */
    public function update_score($initiator_id_and_score,$offerer_id_and_score)
    {
        //start transaction
        $this->db->trans_start();
        
        $initiator_score=$initiator_id_and_score['value'];//obtain scores
        $offer_score=$offerer_id_and_score['value'];

        $this->db->where('user_id', $initiator_id_and_score['user_id']);
        $this->db->set('score','score+'.$offer_score,FALSE); //add old score+value of initial skills score
        $this->db->update('Profile');
        
        $this->db->where('user_id', $offerer_id_and_score['user_id']);
        $this->db->set('score','score+'.$initiator_score,FALSE); //add old score+vale of offer skill score
        $this->db->update('Profile');

        return $this->db->trans_complete();
    }

    /**
    * This function updates both profiles trade counts that are involved in a trade after it is completed
    *
    * @param array $initiator_user_id The id of the initial skill owner
    * @param array $proposer_user_id  The id of the offer skill owner
    * @return bool True on successful update
    */
    public function update_trade_count($initiator_user_id,$proposer_user_id)
    {
        $this->db->trans_start();
        
        $this->db->where('user_id', $initiator_user_id);
        $this->db->set('trade_count','trade_count+1',FALSE);
        $this->db->update('Profile');

        $this->db->where('user_id', $proposer_user_id);
        $this->db->set('trade_count','trade_count+1',FALSE);
        $this->db->update('Profile');
        
        return $this->db->trans_complete();
    }

}


?>