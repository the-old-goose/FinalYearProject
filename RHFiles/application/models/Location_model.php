<?php

/**
 * This model carries out queries associated with the location table using the database
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
 */
class Location_model extends CI_Model {

    /**
    * This function inserts a location entry into the location table
    * 
    * @param string $location_name The name of the location to be created
    * @return mixed Int of insert on success, or FALSE on failure
    */
    public function create_location($location_name)
    {
        $location=array('location_id'=>NULL,
                        'name'=>$location_name);

        if( !$this->db->insert('Location',$location))
        {
            return FALSE;
        }

       return $this->db->insert_id();
    }

     /**
    * This function reads a location using a location_id
    * 
    * @param int $location_id The id of the location to be read
    * @return mixed name of location on success, or FALSE on failure
    */
    public function read_location($location_id)
    {
        $query= $this->db->get_where('Location',array('location_id'=>$location_id));
        
        if(!is_null($query->row()->name))
        {
            return FALSE;
        }
        
        return $query->row()->name;
    }

    /**
    * This function deletes a location entry in the location table
    * 
    * @param int $location_id The id of the location to be deleted
    * @return bool True on successful delete
    */
    public function delete_location($location_id)
    {
        $this->db->where('location_id', $location_id);
        return $this->db->delete('Location');
    }

}


?>