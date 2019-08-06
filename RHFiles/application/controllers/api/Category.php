<?php

use Restserver\Libraries\REST_Controller;

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

/**
 * This Controller carries out the logic for the Category API. It extends the Rest Controller handling HTTP GET requests by returning
 * a JSON response based on the resource requested.
 * 
 * @author Youcef Adoum
 * @package RHfiles
 * @version 1.0
 * @link Guide <https://code.tutsplus.com/tutorials/working-with-restful-services-in-codeigniter--net-8814>
 *
 */
class Category extends REST_Controller {

    /**
     * Handles GET requests for all categories by using the model to retrieve and return all categories. If the request is successful
     * a JSON object and HTTP_OK (200) returned otherwise HTTP_NOT_FOUND response code.
     * 
     * @var $categories The categories found from the model
     * @return object A JSON object containing all categories
     */
    function all_get()
    {
        $categories= $this->category_model->read_all_categories();

        if($categories)
        {
            $this->response($categories,  REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response(NULL, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}
