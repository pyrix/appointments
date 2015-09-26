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

class Example extends REST_Controller{

    function __construct() {

        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
        parent::__construct();
    }

	function user_get()
    {
        if(!$this->get('id'))
        {
        	$this->response(NULL, 400);
        }

        //$user = $this->some_model->getSomething( $this->get('id') );
        $this->load->model('user_model');
        $users = $this->user_model->get_batch('');

        /*
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!'),
		);
		*/

    	//$user = @$users[$this->get('id')];
    	
        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }
    
    function user_post()
    {
        //$this->some_model->updateUser( $this->get('id') );
        $message = array('id' => $this->get('id'), 'name' => $this->post('name'), 'email' => $this->post('email'), 'message' => 'ADDED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function user_delete()
    {
    	//$this->some_model->deletesomething( $this->get('id') );
        $message = array('id' => $this->get('id'), 'message' => 'DELETED!');
        
        $this->response($message, 200); // 200 being the HTTP response code
    }
    
    function users_get()
    {
        //$user = $this->some_model->getSomething( $this->get('id') );
        $this->load->model('user_model');
        $users = $this->user_model->get_batch('');

        /*
    	$users = array(
			1 => array('id' => 1, 'name' => 'Some Guy', 'email' => 'example1@example.com', 'fact' => 'Loves swimming'),
			2 => array('id' => 2, 'name' => 'Person Face', 'email' => 'example2@example.com', 'fact' => 'Has a huge face'),
			3 => array('id' => 3, 'name' => 'Scotty', 'email' => 'example3@example.com', 'fact' => 'Is a Scott!'),
		);
		*/

        //$user = @$users[$this->get('id')];

        if($users)
        {
            $this->response($users, 200); // 200 being the HTTP response code
        }

        else
        {
            $this->response(array('error' => 'User could not be found'), 404);
        }
    }





    function ajax_filter_services_get() {

       $key=$this->post('key');

        try {
            /*
            if ($this->privileges[PRIV_SERVICES]['view'] == FALSE) {
                throw new Exception('You do not have the required privileges for this task.');
            }
            */
            $this->load->model('services_model');
           // $key = mysql_real_escape_string($_POST['key']);
            $where =
                '(name LIKE "%' . $key . '%" OR duration LIKE "%' . $key . '%" OR ' .
                'price LIKE "%' . $key . '%" OR currency LIKE "%' . $key . '%" OR ' .
                'description LIKE "%' . $key . '%")';
            $services = $this->services_model->get_batch($where);

            $this->response($services, 200); // 200 being the HTTP response code
            echo json_encode($services);
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }




    /*
     * |-----------------------------------------------------------
        TEST FUNCTIONS
     * |-----------------------------------------------------------
    */

    function add_customer_post() {

        $customer_data = empty($this->post('first_name')
            || $this->post('last_name')
            || $this->post('email')
            || $this->post('phone_number'))
            ? FALSE
            : TRUE;

        try {
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

                //$this->response(array('success' => 'Customer saved successfully!'), 200); // 200 being the HTTP response code
                $this->response(array($customer['id']), 200);
            }
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }

    function edit_customer_post() {

        $customer_data = empty($this->post('first_name')
            || $this->post('last_name')
            || $this->post('email')
            || $this->post('id')
            || $this->post('phone_number'))
            ? FALSE
            : TRUE;

        try {
            $this->load->model('customers_model');
            $this->load->model('settings_model');

            // :: SAVE CUSTOMER CHANGES TO DATABASE
            if ($customer_data) {

                $customer = array(
                    'id'            => $this->post('id'),
                    'first_name'    => $this->post('first_name'),
                    'last_name'     => $this->post('last_name'),
                    'email'         => $this->post('email'),
                    'phone_number'  => $this->post('phone_number'),
                    'address'       => $this->post('address'),
                    'city'          => $this->post('city'),
                    'zip_code'      => $this->post('zip_code'),
                    'notes'         => $this->post('notes')
                );

                $customer['id'] = $this->customers_model->update($customer);

                //$this->response(array('success' => 'Customer saved successfully!'), 200); // 200 being the HTTP response code
                $this->response(array($customer['id']), 200);
            }
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }

    function disable_user_post() {

        $user_data = empty($this->post('id'))
            ? FALSE
            : TRUE;

        try {
            $this->load->model('user_model');
            $this->load->model('settings_model');

            // :: SAVE USER CHANGES TO DATABASE
            if ($user_data) {

                $user = array(
                    'id'    => $this->post('id'),
                );

                $user['id'] = $this->user_model->disable($user['id']);

                $this->response(array('success' => 'User account disabled successfully!'), 200); // 200 being the HTTP response code
                //$this->response(array($customer['id']), 200);
            }
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }

    function enable_user_post() {

        $user_data = empty($this->post('id'))
            ? FALSE
            : TRUE;

        try {
            $this->load->model('user_model');
            $this->load->model('settings_model');

            // :: SAVE USER CHANGES TO DATABASE
            if ($user_data) {

                $user = array(
                    'id'    => $this->post('id'),
                );

                $user['id'] = $this->user_model->enable($user['id']);

                $this->response(array('success' => 'User account enabled successfully!'), 200); // 200 being the HTTP response code
                //$this->response(array($customer['id']), 200);
            }
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }






    function add_appointment_post() {

        $customer_data = empty($this->post('first_name')
                                || $this->post('last_name')
                                || $this->post('email')
                                || $this->post('phone_number'))
                            ? FALSE
                            : TRUE;

        $appointment_data = empty($this->post('service')
                                    || $this->post('provider')
                                    || $this->post('start_datetime')
                                    || $this->post('end_datetime'))
                                ? FALSE
                                : TRUE;

        try {
            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('services_model');
            $this->load->model('customers_model');
            $this->load->model('settings_model');

            // :: SAVE CUSTOMER CHANGES TO DATABASE
            if ($customer_data) {

                //$customer = json_decode(stripcslashes($_POST['customer_data']), true);

                $customer = array(
                    'id'            => $this->post('id'),
                    'first_name'    => $this->post('first_name'),
                    'last_name'     => $this->post('last_name'),
                    'email'         => $this->post('email'),
                    'phone_number'  => $this->post('phone_number'),
                    'address'       => $this->post('address'),
                    'city'          => $this->post('city'),
                    'zip_code'      => $this->post('zip_code'),
                    'notes'         => $this->post('notes')
                );



                $customer['id'] = $this->customers_model->add($customer);

                //$this->response(array('success' => 'Customer saved successfully!'), 200); // 200 being the HTTP response code
            }

            // :: SAVE APPOINTMENT CHANGES TO DATABASE
            if ($appointment_data) {

                //$appointment = json_decode(stripcslashes($_POST['appointment_data']), true);

                $appointment = array(
                    'id_services'       => $this->post('service'),
                    'id_users_provider' => $this->post('provider'),
                    'start_datetime'    => $this->post('start_datetime'),
                    'end_datetime'      => $this->post('end_datetime'),
                    'id_users_customer' => $customer['id']
                );

                $appointment['id'] = $this->appointments_model->add($appointment);

                $this->response(array('success' => 'Appointment saved successfully!'), 200); // 200 being the HTTP response code
            }

            /*
            $appointment = $this->appointments_model->get_row($appointment['id']);
            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            $customer = $this->customers_model->get_row($appointment['id_users_customer']);
            $service = $this->services_model->get_row($appointment['id_services']);

            $company_settings = array(
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'company_email' => $this->settings_model->get_setting('company_email')
            );
            */
            // :: SYNC APPOINTMENT CHANGES WITH GOOGLE CALENDAR
            /*
            try {
                $google_sync = $this->providers_model->get_setting('google_sync',
                    $appointment['id_users_provider']);

                if ($google_sync == TRUE) {
                    $google_token = json_decode($this->providers_model->get_setting('google_token',
                        $appointment['id_users_provider']));

                    $this->load->library('Google_Sync');
                    $this->google_sync->refresh_token($google_token->refresh_token);

                    if ($appointment['id_google_calendar'] == NULL) {
                        $google_event = $this->google_sync->add_appointment($appointment, $provider,
                            $service, $customer, $company_settings);
                        $appointment['id_google_calendar'] = $google_event->id;
                        $this->appointments_model->add($appointment); // Store google calendar id.
                    } else {
                        $this->google_sync->update_appointment($appointment, $provider,
                            $service, $customer, $company_settings);
                    }
                }
            } catch(Exception $exc) {
                $warnings[] = exceptionToJavaScript($exc);
            }
            */
            // :: SEND EMAIL NOTIFICATIONS TO PROVIDER AND CUSTOMER
            /*
            try {
                $this->load->library('Notifications');

                $send_provider = $this->providers_model
                    ->get_setting('notifications', $provider['id']);

                if (!$manage_mode) {
                    $customer_title = $this->lang->line('appointment_booked');
                    $customer_message = $this->lang->line('thank_your_for_appointment');
                    $customer_link = $this->config->item('base_url') . 'appointments/index/'
                        . $appointment['hash'];

                    $provider_title = $this->lang->line('appointment_added_to_your_plan');
                    $provider_message = $this->lang->line('appointment_link_description');
                    $provider_link = $this->config->item('base_url') . 'backend/index/'
                        . $appointment['hash'];
                } else {
                    $customer_title = $this->lang->line('appointment_changes_saved');
                    $customer_message = '';
                    $customer_link = $this->config->item('base_url') . 'appointments/index/'
                        . $appointment['hash'];

                    $provider_title = $this->lang->line('appointment_details_changed');
                    $provider_message = '';
                    $provider_link = $this->config->item('base_url') . 'backend/index/'
                        . $appointment['hash'];
                }

                $this->notifications->send_appointment_details($appointment, $provider,
                    $service, $customer, $company_settings, $customer_title,
                    $customer_message, $customer_link, $customer['email']);

                if ($send_provider == TRUE) {
                    $this->notifications->send_appointment_details($appointment, $provider,
                        $service, $customer, $company_settings, $provider_title,
                        $provider_message, $provider_link, $provider['email']);
                }

            } catch(Exception $exc) {
                $warnings[] = exceptionToJavaScript($exc);
            }

            if (!isset($warnings)) {
                echo json_encode(AJAX_SUCCESS);
            } else {
                echo json_encode(array(
                    'warnings' => $warnings
                ));
            }
            */
        } catch(Exception $exc) {
            echo json_encode(array(
                'exceptions' => array(exceptionToJavaScript($exc))
            ));
        }
    }
}