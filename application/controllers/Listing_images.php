<?php

class Listing_images extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = 0)
    {
        if (!empty($id)) {
            $this->json_result(true, '', $this->listing_image_model->get_many_by('listing_id', $id));
        } else {
            $this->json_result(true, '', $this->listing_image_model->get_all());
        }
    }

    public function get($id)
    {
        $this->json_result(true, '', $this->listing_image_model->get($id));
    }

    public function insert()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('caption') != null && $data['caption'] = $this->input->post('caption');

            $r = $this->listing_image_model->insert($data);
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
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('caption') != null && $data['caption'] = $this->input->post('caption');

            $r = $this->listing_image_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access();
            $user = $this->login_user();
            $r = $this->listing_image_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
