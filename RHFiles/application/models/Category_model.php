<?php

/**
 * This model carries out queries associated with categories using the database
 * 
 * @author Youcef Adoum
 * @version 1.0
 * @package RHfiles
 */
class Category_model extends CI_Model {

   /**
    * This function returns an array of all the categories from the Category table
    * 
    * @return array An associative array of categories in Category table
    */
    public function read_all_categories()
    {
        $query=$this->db->get('Category');

        if($query->result_array())
        {
            return $query->result_array(); 
        }

        return FALSE;       
    } 

    /**
    * This function returns the name of a category given the category_id
    * 
    * @param int $category_id The id of the category to be read
    * @return mixed Category name or False on failure
    */
    public function read_category($category_id)
    {
        $query= $this->db->get_where('Category',array('category_id'=>$category_id));
        
        if(!is_null($query->row()->name))
        {
            return FALSE;
        }
        
        return $query->row()->name;
    }

    /**
    * This function inserts a category entry into the category table
    * 
    * @param string $category_name The name of the category to be inserted
    * @return mixed The category id of the new category or FALSE on failure
    */
    public function create_category($category_name)
    {
        $category=array('category_id'=>NULL,
                        'name'=>$category_name);

        if( !$this->db->insert('Category',$category))
        {
            return FALSE;
        }

       return $this->db->insert_id();
    }

    /**
    * This function deletes a category given a category id
    * 
    * @param string $category_id The id of the category to be deleted
    * @return bool  True on successful deletion
    */
    public function delete_category($category_id)
    {
        $this->db->where('category_id', $category_id);
        return $this->db->delete('Category');
    }
       


}