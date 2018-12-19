<?php

class Listing_reviews extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }


    public function query()
    {
        $this->json_result(true, '', $this->listing_review_model->filter($this->input->post_get('page'), $this->input->post_get('count'), array(
            'listing_id' => $this->input->post_get('listing_id'),
            'rating' => $this->input->post_get('rating'),
            'user_id' => $this->input->post_get('user_id')
        ), $this->input->post('order_by')));
    }

    public function get($id)
    {
        $this->json_result(true, '', $this->listing_review_model->get($id));
    }

    public function insert()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('comment') != null && $data['comment'] = $this->input->post('comment');
            $this->input->post('rating') != null && $data['rating'] = $this->input->post('rating');


            $r = $this->listing_review_model->insert($data);
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
            $this->input->post('rating') != null && $data['rating'] = $this->input->post('rating');


            $r = $this->listing_review_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access();
            $r = $this->listing_review_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
