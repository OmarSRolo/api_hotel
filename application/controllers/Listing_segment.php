<?php

class Listing_segment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->check_access();
        $this->json_result(true, 'All segment', $this->listing_segment_model->filter($this->input->post('page'), $this->input->post('count')));

    }

    public function get($id)
    {
        $this->json_result(true, '', $this->listing_segment_model->get($id));
    }

    public function insert()
    {
        try {
            $this->check_access();
            $data = $this->input->post();
            $r = $this->listing_segment_model->insert($data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $this->check_access();
            $data = $this->input->post();
            $r = $this->listing_segment_model->update($this->input->post('id'), $data);
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
            $r = $this->listing_segment_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
