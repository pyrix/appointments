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

class Customers extends REST_Controller{

    function __construct() {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
        parent::__construct();
    }

    function add_post() {

        $customer_data = empty($this->post('first_name')
            || $this->post('last_name')
            || $this->post('email')
            || $this->post('phone_number'))
            ? FALSE
            : TRUE;

            $this->load->model('user_model');
            $this->load->model('customers_model');
            $this->load->model('settings_model');

            // :: SAVE CUSTOMER CHANGES TO DATABASE
            if ($customer_data) {

                $customer = array(
                    'first_name'    => $this->post('first_name'),
                    'last_name'     => $this->post('last_name'),
                    'email'         => $this->post('email'),
                    'phone_number'  => $this->post('phone_number'),
                    'address'       => $this->post('address'),
                    'city'          => $this->post('city'),
                    'zip_code'      => $this->post('zip_code'),
                    'notes'         => $this->post('notes'),
                    'latitude'      => $this->post('latitude'),
                    'longitude'     => $this->post('longitude')
                );

                $customer['id'] = $this->customers_model->add($customer);
                $this->user_model->enable($customer['id']);

                $this->response(array('success' => 'Customer saved successfully!'), 200); // 200 being the HTTP response code
                //$this->response(array($customer['id']), 200);
            }
            else {
                $this->response(array('error' => 'There was a problem while adding the customer!'), 500); // 500 being the HTTP response code
            }

    }
}