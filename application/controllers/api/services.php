<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
 */

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Services extends REST_Controller {

    function __construct() {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
        parent::__construct();
    }
    
    //Get all categories
    function categories_get() {

        $this->load->model('services_model');
        $services = $this->services_model->get_all_categories();

        if($services)
        {
            $this->response($services, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'Couldn\'t find any categories!'), 404);
        }
    }

    //Get all services for a specific category
    function by_category_id_get() {

        if(empty($this->get('id'))) {
            $this->response(array('error' => 'Bad request!'), 400);
        }

        $this->load->model('services_model');
        $service = $this->services_model->get_services_by_category_id($this->get('id'));


        if($service) {
            $this->response($service, 200); // 200 being the HTTP response code
        }
        else {
            $this->response(array('error' => 'No services could be found in this category'), 404);
        }
    }



    //Get a specific service by id
    function by_id_get() {

        if(empty($this->get('id'))) {
            $this->response(array('error' => 'Bad request!'), 400);
        }

        $this->load->model('services_model');
        $service = $this->services_model->get_service_by_id($this->get('id'));


        if($service) {
            $this->response($service, 200); // 200 being the HTTP response code
        }
        else {
            $this->response(array('error' => 'This service could not be found'), 404);
        }
    }
}