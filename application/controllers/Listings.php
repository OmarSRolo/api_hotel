<?php

class Listings extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = 0)
    {
        $this->json_result(true, '', $this->listing_model->get_all());
    }

    public function query()
    {

        $array = array(
            'hotel_name' => $this->input->post('hotel_name'),
            'owner_id' => $this->input->post('owner_id'),
            'listing_type_id' => $this->input->post('listing_type_id'),
            'country_code' => $this->input->post('country_code'),
            'city' => $this->input->get_post('city'),
            'capacity' => $this->input->get_post('capacity'),
            'show_near' => $this->input->get_post('show_near'),
            'services' => $this->input->get_post('services'),
            'created_at' => $this->input->get_post('created_at'),
            'start_date' => $this->input->get_post('start_date'),
            'end_date' => $this->input->get_post('end_date'),
            'user_id' => $this->input->post('user_id'),
            'owner_email' => $this->input->post('owner_email')
        );
        $this->json_result(true, '', $this->listing_model->filter($this->input->post_get('page'), $this->input->post_get('count'),
            $array, $this->input->get_post('order_by')));
    }

    // Metodo que obtiene los listings por usuario
    public function getListingsByUser()
    {
        $this->check_access();
        $array = array(
            'listing_type_id' => $this->input->post('listing_type_id'),
            'country_code' => $this->input->post('country_code'),
            'city' => $this->input->get_post('city'),
            'capacity' => $this->input->get_post('capacity'),
            'created_at' => $this->input->get_post('created_at')
        );
        $this->json_result(true, '', $this->listing_model->filterByUser($this->input->post_get('page'), $this->input->post_get('count'),
            $array, $this->input->get_post('order_by')));
    }


    public function get($id)
    {
        $this->json_result(true, '', $this->listing_model->get($id));
    }

    public function insert()
    {
        try {
            $this->check_access();
            $data = $this->input->post();

            $r = $this->listing_model->insert($data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $this->check_access();
            $data = array();

            $this->input->post('listing_type_id') != null && $data['listing_type_id'] = $this->input->post('listing_type_id');
            $this->input->post('country_code') != null && $data['country_code'] = $this->input->post('country_code');
            $this->input->post('city') != null && $data['city'] = $this->input->post('city');
            $this->input->post('capacity') != null && $data['capacity'] = $this->input->post('capacity');
            $this->input->post('video_youtube') != null && $data['video_youtube'] = $this->input->post('video_youtube');
            $this->input->post('video_vimeo') != null && $data['video_vimeo'] = $this->input->post('video_vimeo');
            $this->input->post('longitude') != null && $data['longitude'] = $this->input->post('longitude');
            $this->input->post('latitude') != null && $data['latitude'] = $this->input->post('latitude');

            $this->input->post('price') != null && $data['price'] = $this->input->post('price');
            $this->input->post('cancel_previusly_days') != null && $data['cancel_previusly_days'] = $this->input->post('cancel_previusly_days');
            $this->input->post('cancel_previusly_amount') != null && $data['cancel_previusly_amount'] = $this->input->post('cancel_previusly_amount');
            $this->input->post('cancel_total_amount') != null && $data['cancel_total_amount'] = $this->input->post('cancel_total_amount');

            $this->input->post('coin_base') != null && $data['coin_base'] = $this->input->post('coin_base');

            $this->input->post('description_en') != null && $data['description_en'] = $this->input->post('description_en');
            $this->input->post('description_es') != null && $data['description_es'] = $this->input->post('description_es');

            $this->input->post('services') != null && $data['services'] = $this->input->post('services');
            $this->input->post('prices') != null && $data['prices'] = $this->input->post('prices');

            $this->input->post('relevant') != null && $data['relevant'] = $this->input->post('relevant');
            $this->input->post('close_sales') != null && $data['close_sales'] = $this->input->post('close_sales');

            $this->input->post('images') != null && $data['images'] = $this->input->post('images');
            $this->input->post('delete_images') != null && $this->input->post('images') == null && $data['images'] = array();

            $r = $this->listing_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access();
            $r = $this->listing_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function insert_admin()
    {
        try {
            $this->check_access();

            $data = array();
            $this->input->post('listing_type_id') != null && $data['listing_type_id'] = $this->input->post('listing_type_id');
            $this->input->post('country_code') != null && $data['country_code'] = $this->input->post('country_code');
            $this->input->post('city') != null && $data['city'] = $this->input->post('city');
            $this->input->post('capacity') != null && $data['capacity'] = $this->input->post('capacity');
            $this->input->post('video_youtube') != null && $data['video_youtube'] = $this->input->post('video_youtube');
            $this->input->post('video_vimeo') != null && $data['video_vimeo'] = $this->input->post('video_vimeo');
            $this->input->post('longitude') != null && $data['longitude'] = $this->input->post('longitude');
            $this->input->post('latitude') != null && $data['latitude'] = $this->input->post('latitude');

            $this->input->post('price') != null && $data['price'] = $this->input->post('price');
            $this->input->post('cancel_previusly_days') != null && $data['cancel_previusly_days'] = $this->input->post('cancel_previusly_days');
            $this->input->post('cancel_previusly_amount') != null && $data['cancel_previusly_amount'] = $this->input->post('cancel_previusly_amount');
            $this->input->post('cancel_total_amount') != null && $data['cancel_total_amount'] = $this->input->post('cancel_total_amount');

            $this->input->post('coin_base') != null && $data['coin_base'] = $this->input->post('coin_base');

            $this->input->post('description_en') != null && $data['description_en'] = $this->input->post('description_en');
            $this->input->post('description_es') != null && $data['description_es'] = $this->input->post('description_es');
            $this->input->post('services') != null && $data['services'] = $this->input->post('services');

            $this->input->post('prices') != null && $data['prices'] = $this->input->post('prices');
            $this->input->post('images') != null && $data['images'] = $this->input->post('images');

            $this->input->post('owner_id') != null && $data['owner_id'] = $this->input->post('owner_id');

            $r = $this->listing_model->insert_room_admin($data);

            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }

    }

}
