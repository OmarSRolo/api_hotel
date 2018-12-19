<?php

class Listing_types extends MY_Controller
{
    public function index()
    {
        $this->json_result(true, '', $this->listing_type_model->get_all());
    }

    public function query()
    {
        $this->json_result(true, '', $this->listing_type_model->filter($this->input->get_post('page'), $this->input->get_post('count'), array(
            'name_es' => $this->input->get_post('name_es'),
            'name_en' => $this->input->get_post('name_en'),
            'name_fr' => $this->input->get_post('name_fr'),
            'name_gr' => $this->input->get_post('name_gr')
        ), $this->input->post('order_by')));
    }

    public function insert()
    {
        try {
            $this->check_access('admin');
            $data = array();
            $this->input->post('price') != null && $data['price'] = $this->input->post('price');
            foreach ($this->langs as $lang) {
                $this->input->post('name_'.$lang) != null && $data['name_'.$lang] = $this->input->post('name_'.$lang);
            }

            $r = $this->listing_type_model->insert($data);
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
            $this->input->post('price') != null && $data['price'] = $this->input->post('price');
            foreach ($this->langs as $lang) {
                $this->input->post('name_'.$lang) != null && $data['name_'.$lang] = $this->input->post('name_'.$lang);
            }

            $r = $this->listing_type_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }



    public function delete()
    {
        try {
            $this->check_access('admin');
            $r = $this->listing_type_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
