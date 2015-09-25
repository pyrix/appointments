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

class Appointments extends REST_Controller {

    function __construct() {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
        parent::__construct();
    }

    function avail_appointments_by_service_id_get() {

        if(!$this->get('id')) {
            $this->response(NULL, 400);
        }

        $this->load->model('appointments_model');
        $result = $this->appointments_model->get_avail_by_service_id($this->get('id'));

        if($result) {
            $this->response(array('success' => 'Appointment saved successfully!'), 200); // 200 being the HTTP response code
        }

        else {
            $this->response(array('error' => 'Could not find any categories!'), 404);
        }

    }

    function add_post() {

        if(!$this->post('id')) {
            $this->response(NULL, 400);
        }

        $this->load->model('appointments_model');
        $result = $this->appointments_model->add();

        if(!empty($result)) {
            $this->response(array('success' => 'Appointment saved successfully!'), 200); // 200 being the HTTP response code
        }
        else {
            $this->response(array('error' => 'Appointment could not be saved!'), 404);
        }
    }

    function delete_by_id_get() {

        $this->load->model('appointments_model');
        $result = $this->appointments_model->delete();

        if($result) {
            $this->response(array('success' => 'Appointment deleted successfully!'), 200); // 200 being the HTTP response code
        }

        else {
            $this->response(array('error' => 'Appointment could not be deleted!'), 404);
        }
    }
}