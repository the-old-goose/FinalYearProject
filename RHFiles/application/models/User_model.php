<?php 

/**
 * The user model is used to carry out database queries related to the User table
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
 * 
 */
class User_model extends CI_Model
{
        
        /**
         * This function will create a user, inserting the values into the User table and profile table in a transaction
         * 
         * @param string $username The unique username to be inserted
         * @param string $email    The email to be inserted
         * @param string $encrypt_pass The password encrypted by B-Crypt
         * @param string $first_name The firstname of the user to be created
         * @param string $last_name The lastname of the user to be created
         * @param int $location_id The id of the users location
         * @param string $mobile The 11 digit mobile number
         * @param string $auth_code The 10 sequence verification code sent in an email
         * @param bool $read_id True if the insert id is to be returned
         * @return mixed True on successful insertion, insert id if read flag is true , FALSE on failure
         */
        public function create_user_and_profile($username,$email,$encrypt_pass,$first_name,$last_name,$location_id=1,$mobile=07,$auth_code,$read_id=FALSE)
        {
            $this->db->trans_start();

            $user_data= array(
                'user_id'=>NULL,
                'username'=>$username,
                'email'=>$email,
                'password'=>$encrypt_pass,
                'user_type'=>1, //1- Standard user - 2-Moderator -  3-Admin
                'signup_date'=>NULL,
                'mobile'=>$mobile,
                'auth_code'=>$auth_code);

            $this->db->insert('User',$user_data);
            $user_id=$this->db->insert_id();
            $this->profile_model->create_profile($user_id,$first_name,$last_name,$location_id);
            $this->db->trans_complete();

            if($read_id) //if read_id flag set return insert id
            {
                return $user_id;
            }

            return $this->db->trans_status();
         }

        /**
        * This function retrieves a users email address
        *
        * @param int $user_id The id of the user in which the email should be read 
        * @return mixed String of email on success  or FALSE on failure
        */
        public function read_email($user_id)
        {
            $query=$this->db->get_where('User',array('user_id'=>$user_id));

            if($query->result())
            {
                return $email=$query->row(0)->email;
            }
            else
            {
                return FALSE;
            }
        
        }

        /**
        * This function retrieves the username that listed a skill
        *
        * @param int $skill_id The skill id which username needs read
        * @param bool $read_mobile When True will return row array
        * @param bool $only_username When True will return only username
        * @return mixed Assoc array of user if $only_username=False else return a string containing username else false on failure
        * @todo Clean up read_mobile -depreciated-
        */
        public function read_username_from_skill($skill_id,$read_mobile=FALSE,$only_username=FALSE)
        {
            $query=$this->db->query("SELECT User.username,User.mobile
                                     FROM User 
                                        INNER JOIN User_Skill 
                                            ON User.user_id=User_Skill.user_id
                                     WHERE User_Skill.skill_id=".$skill_id);   
            
            
            
            if($only_username)
            {
                return $query->row(0)->username;
            }


            return $user=$query->row();
        }
        
        /**
         * This function will check the User table for a specific user record with matching $email and verified $plain_pass
         * returning false if the record isnt found or the user row if found
         * 
         * @param string $email A users email
         * @param string $plain_pass The plaintext of a users password
         * @return mixed Returns an int of user_id if row is found, false on failure
         */
        public function login($email,$plain_pass)
        {
            $this->db->where('email',$email);
            $this->db->where('confirmed',1);
            $result=$this->db->get('User');

            if($result)
            {
                
                if(password_verify($plain_pass,$result->row(0)->password))
                {
                    return $result->row_array(0);
                }
            }
            
            return FALSE;
        }
        
        /**
         * This fuction checks the User Table to see if a user record already exists with the username=$username
         * 
         * @param string $username A username to be checked
         * @return boolean Return True if the username DOES NOT exists: else return False it exists
         */
        public function check_username_exists($username)
        {
            $query = $this->db->get_where('User',array('username'=>$username));
            if (empty($query->row_array()))
            {
                return TRUE;
            }
        
            return FALSE; 
        }
        
        /**
         * This fuction checks the User Table to see if a user record already exists with the email=$email
         * 
         * @param string $email An email to be checked
         * @return boolean Return True if the email DOES NOT exists: else return False it exists
         */
        public function check_email_exists($email)
        {
            $query = $this->db->get_where('User',array('email'=>$email));
            if (empty($query->row_array()))
            {
                return TRUE;
            }
        
            return FALSE; 
        }

        /**
         * This fuction updates the confirmed column of a user by checking if the $code matches an auth_code field in the db
         * 
         * @param string $code A code to be validated
         * @return boolean Return True if the code matches a users auth_code in the database and confirmed has been set to 1
         * @todo extend length of auth_code to consider collisions
         */
        public function validate_user($code)
        {
            
            $query=$this->db->get_where('User',array('auth_code'=>$code));
            $user_id=$query->row(0)->user_id;

            if($user_id)
            {
                $this->db->where('user_id', $user_id);
                $this->db->set('confirmed',1);
                $this->db->update('User');
                return TRUE;
            }
            
            return FALSE;
        }

        /**
         * This fuction retrieves a users auth_code using their username, used when sending verification emails
         * 
         * @param string $username The username thats code needs to be read
         * @return mixed String of auth_code on success , false on failure
         */
        public function read_code($username)
        {
            $query=$this->db->get_where('User',array('username'=>$username));

            return $auth_code=$query->row(0)->auth_code;
        }

        /**
         * This fuction checks is a user has been verified i.e. checking its confirmed value is 1
         * 
         * @param string $user_id The user id to check
         * @return bool True if user verified
         */
        public function read_if_user_valid($user_id)
        {
            $query=$this->db->get_where('User',array('user_id'=>$user_id));

            $confirmed=$query->row(0)->confirmed;
            if($confirmed==1)
            {
                return TRUE;
            }

            return FALSE;
        }

        /**
         * This fuction deletes a user and associated profile entry from db
         * 
         * @param string $user_id The user id to delete
         * @return bool True if delete successful 
         */
        public function delete_user($user_id)
        {
            $this->db->trans_start();
            $this->db->where('user_id', $user_id);
            $this->db->delete('Profile');

            $this->db->where('user_id', $user_id);
            $this->db->delete('User');

            $this->db->trans_complete();

            return $this->db->trans_status();
            
        }
}

?>