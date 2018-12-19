<?php


class Users extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->json_result(true, '', $this->user_model->order_by('created_at')->get_all());
    }

    public function query()
    {
        $us = $this->login_user();
        $this->json_result(true, '', $this->user_model->filter($this->input->get_post('page'), $this->input->get_post('count'), array(
            'email' => $this->input->get_post('email'),
            'hotel_name' => $this->input->get_post('hotel_name'),
            'hotel_rating' => $this->input->get_post('hotel_rating'),
            'country_code' => $this->input->get_post('country_code'),
            'region' => $this->input->get_post('region'),
            'city' => $this->input->get_post('city'),
            'name' => $this->input->get_post('name'),
            'file_id_type' => $this->input->get_post('file_id_type'),
            'verification_id_status' => $this->input->get_post('verification_id_status'),
            'role' => $this->input->get_post('role'),
        ), $this->input->get_post('order_by')));
    }

    public function get($id)
    {
        $this->check_access();
            
        $this->json_result(true, '', $this->user_model->get($id));
    }

    public function insert()
    {
        try {
            $this->check_access('admin');

            $data = array();

            $this->input->post('email') != null && $data['email'] = $this->input->post('email');
            $this->input->post('password') != null && $data['password'] = $this->input->post('password');
            $this->input->post('dni') != null && $data['dni'] = $this->input->post('dni');
            $this->input->post('hotel_name') != null && $data['hotel_name'] = $this->input->post('hotel_name');
            $this->input->post('hotel_rating') != null && $data['hotel_rating'] = $this->input->post('hotel_rating');
            $this->input->post('first_name') != null && $data['first_name'] = $this->input->post('first_name');
            $this->input->post('last_name') != null && $data['last_name'] = $this->input->post('last_name');
            $this->input->post('phone') != null && $data['phone'] = $this->input->post('phone');
            $this->input->post('phone_emergency') != null && $data['phone_emergency'] = $this->input->post('phone_emergency');
            $this->input->post('country_code') != null && $data['country_code'] = $this->input->post('country_code');
            $this->input->post('region') != null && $data['region'] = $this->input->post('region');
            $this->input->post('city') != null && $data['city'] = $this->input->post('city');
            $this->input->post('zip_code') != null && $data['zip_code'] = $this->input->post('zip_code');
            $this->input->post('time_zone') != null && $data['time_zone'] = $this->input->post('time_zone');
            $this->input->post('latitude') != null && $data['latitude'] = $this->input->post('latitude');
            $this->input->post('longitude') != null && $data['longitude'] = $this->input->post('longitude');
            $this->input->post('about_me') != null && $data['about_me'] = $this->input->post('about_me');
            $this->input->post('file_id_type') != null && $data['file_id_type'] = $this->input->post('file_id_type');
            $this->input->post('verification_id_status') != null && $data['verification_id_status'] = $this->input->post('verification_id_status');
            $this->input->post('role') != null && $data['role'] = $this->input->post('role');

            $this->input->post('cancel_term_id') != null && $data['cancel_term_id'] = $this->input->post('cancel_term_id');
            $this->input->post('cancel_prev_cost_id') != null && $data['cancel_prev_cost_id'] = $this->input->post('cancel_prev_cost_id');
            $this->input->post('cancel_total_cost_id') != null && $data['cancel_total_cost_id'] = $this->input->post('cancel_total_cost_id');

            $this->input->post('hotel_id') != null && $data['hotel_id'] = $this->input->post('hotel_id');
            
            $this->input->post('bank_account_id') != null && $data['bank_account_id'] = $this->input->post('bank_account_id');
            $this->input->post('bank_account_number') != null && $data['bank_account_number'] = $this->input->post('bank_account_number');


            $this->user_model->add_validation(array('field' => 'password', 'label' => lang_extension('user_model.password'), 'rules' => 'required|min_length[7]'));

            $r = $this->user_model->insert($data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $this->check_access('admin');

            $data = array();

            $this->input->post('email') != null && $data['email'] = $this->input->post('email');
            $this->input->post('password') != null && $data['password'] = $this->input->post('password');
            $this->input->post('hotel_name') != null && $data['hotel_name'] = $this->input->post('hotel_name');
            $this->input->post('hotel_rating') != null && $data['hotel_rating'] = $this->input->post('hotel_rating');
            $this->input->post('dni') != null && $data['dni'] = $this->input->post('dni');
            $this->input->post('first_name') != null && $data['first_name'] = $this->input->post('first_name');
            $this->input->post('last_name') != null && $data['last_name'] = $this->input->post('last_name');
            $this->input->post('phone') != null && $data['phone'] = $this->input->post('phone');
            $this->input->post('phone_emergency') != null && $data['phone_emergency'] = $this->input->post('phone_emergency');
            $this->input->post('country_code') != null && $data['country_code'] = $this->input->post('country_code');
            $this->input->post('region') != null && $data['region'] = $this->input->post('region');
            $this->input->post('city') != null && $data['city'] = $this->input->post('city');
            $this->input->post('zip_code') != null && $data['zip_code'] = $this->input->post('zip_code');
            $this->input->post('time_zone') != null && $data['time_zone'] = $this->input->post('time_zone');
            $this->input->post('latitude') != null && $data['latitude'] = $this->input->post('latitude');
            $this->input->post('longitude') != null && $data['longitude'] = $this->input->post('longitude');
            $this->input->post('about_me') != null && $data['about_me'] = $this->input->post('about_me');
            $this->input->post('about_me') != null && $data['about_me'] = $this->input->post('about_me');
            $this->input->post('file_id_type') != null && $data['file_id_type'] = $this->input->post('file_id_type');
            $this->input->post('verification_id_status') != null && $data['verification_id_status'] = $this->input->post('verification_id_status');
            $this->input->post('role') != null && $data['role'] = $this->input->post('role');

            $this->input->post('cancel_term_id') != null && $data['cancel_term_id'] = $this->input->post('cancel_term_id');
            $this->input->post('cancel_prev_cost_id') != null && $data['cancel_prev_cost_id'] = $this->input->post('cancel_prev_cost_id');
            $this->input->post('cancel_total_cost_id') != null && $data['cancel_total_cost_id'] = $this->input->post('cancel_total_cost_id');

            $this->input->post('bank_account_id') != null && $data['bank_account_id'] = $this->input->post('bank_account_id');
            $this->input->post('bank_account_number') != null && $data['bank_account_number'] = $this->input->post('bank_account_number');
            $this->input->post('hotel_id') != null && $data['hotel_id'] = $this->input->post('hotel_id');
            

            ($this->input->post('stop_sales') != null && $this->input->post('edit_stop_sales') != null) && $data['stop_sales'] = $this->input->post('stop_sales');

            if ($this->input->post('password') != null) {
                $this->user_model->add_validation(array('field' => 'password', 'label' => lang_extension('user_model.password'), 'rules' => 'required|min_length[7]'));
            }

            $r = $this->user_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access('admin');
            $r = $this->user_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
