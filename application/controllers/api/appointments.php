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
            $this->response(array('success' => $result), 200); // 200 being the HTTP response code
        }

        else {
            $this->response(array('error' => 'Could not find any categories!'), 404);
        }

    }

    function add_post() {

        $appointment_data = empty($this->post('service')
            || $this->post('provider')
            || $this->post('start_datetime')
            || $this->post('end_datetime')
            || $this->post('user_id'))
            ? FALSE
            : TRUE;

        if ($appointment_data) {

            $this->load->model('appointments_model');
            $this->load->model('providers_model');
            $this->load->model('services_model');
            $this->load->model('customers_model');
            $this->load->helper('greek_string');

            $appointment = array(
                'id_services'       => $this->post('service'),
                'id_users_provider' => $this->post('provider'),
                'start_datetime'    => $this->post('start_datetime'),
                'end_datetime'      => $this->post('end_datetime'),
                'notes'             => grstrtoupper(strip_tags($this->post('notes'))),
                'id_users_customer' => $this->post('user_id')
            );

            $manage_mode = isset($appointment['id']);

            $appointment['id'] = $this->appointments_model->add($appointment);

            //Send mail notification
            if(MAIL_NOTIFICATIONS)
                $this->sendMailNotification($appointment, $manage_mode);

            if(!empty($appointment['id'])) {
                $this->response(array('success' => 'Appointment saved successfully!'), 200); // 200 being the HTTP response code
            }
            else {
                $this->response(array('error' => 'There was a problem while adding an appointment!'), 500);
            }

        }
    }

    function delete_by_id_post() {

        if(empty($this->post('id'))) {
            $this->response(array('error' => 'Bad request!'), 400);
        }

        $this->load->model('appointments_model');
        $result = $this->appointments_model->delete($this->post('id'));

        if($result) {
            $this->response(array('success' => 'Appointment deleted successfully!'), 200); // 200 being the HTTP response code
        }
        else {
            $this->response(array('error' => 'Appointment could not be deleted!'), 500);
        }
    }

    // :: SEND EMAIL NOTIFICATIONS TO PROVIDER AND CUSTOMER
    private function sendMailNotification($appointment, $manage_mode) {

        try {
            $this->load->library('Notifications');
            $this->load->model('settings_model');
            $appointment = $this->appointments_model->get_row($appointment['id']);
            $provider = $this->providers_model->get_row($appointment['id_users_provider']);
            $customer = $this->customers_model->get_row($appointment['id_users_customer']);
            $service = $this->services_model->get_row($appointment['id_services']);

            $company_settings = array(
                'company_name' => $this->settings_model->get_setting('company_name'),
                'company_link' => $this->settings_model->get_setting('company_link'),
                'company_email' => $this->settings_model->get_setting('company_email')
            );

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

        } catch (Exception $exc) {
            return $exc->getMessage();
        }
    }
}