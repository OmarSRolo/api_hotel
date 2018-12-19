<?php

class Listing_comments extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($id = 0)
    {
        if (!empty($id)) {
            $this->json_result(true, '', $this->listing_comment_model->get_many_by('listing_id', $id));
        } else {
            $this->json_result(true, '', $this->listing_comment_model->get_all());
        }
    }

    public function query()
    {
        $this->json_result(true, '', $this->listing_comment_model->filter($this->input->post('page'), $this->input->post('count'), array(
            'listing_id' => $this->input->post('listing_id'),
            'user_id' => $this->input->post('user_id'),
        ), $this->input->post('order_by')));
    }

    public function get($id)
    {
        $this->json_result(true, '', $this->listing_comment_model->get($id));
    }

    public function insert()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('comment') != null && $data['comment'] = $this->input->post('comment');

            $r = $this->listing_comment_model->insert($data);
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
            $this->input->post('comment') != null && $data['comment'] = $this->input->post('comment');


            $r = $this->listing_comment_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access();
            $r = $this->listing_comment_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
