<?php

class Configurations extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->json_result(true, '', $this->configuration_model->get_all());
    }

    public function query()
    {
        $this->json_result(true, '', $this->configuration_model->filter($this->input->get_post('page'), $this->input->get_post('count'), array(
            'key' => array('like' => $this->input->get_post('key')),
            'value' => array('like' => $this->input->get_post('value'),'logic'=>'or_'),
            'order' => array('like' => $this->input->get_post('order'),'logic'=>'or_')
        )));
    }

    public function get($id)
    {
        $r = $this->configuration_model->get_by('key', $id);
        !$r && $r = $this->configuration_model->get($id);
        $this->json_result(true, '', $r);
    }

    public function insert()
    {
        try {
            $this->check_access('admin');
            $data = array();
            $this->input->post('key') != null && $data['key'] = $this->input->post('key');
            $this->input->post('value') != null && $data['value'] = $this->input->post('value');
            $this->input->post('order') != null && $data['order'] = $this->input->post('order');

            $r = $this->configuration_model->insert($data);
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
            $this->input->post('key') != null && $data['key'] = $this->input->post('key');
            $this->input->post('value') != null && $data['value'] = $this->input->post('value');
            $this->input->post('order') != null && $data['order'] = $this->input->post('order');

            $r = $this->configuration_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            //$this->check_access('admin');
            $r = $this->configuration_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
