<?php

class Contents extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->json_result(true, '', $this->content_model->get_all());
    }

    public function query()
    {
        $this->json_result(true, '', $this->content_model->filter($this->input->post('page'), $this->input->post('count'), array(
          'key' => array('like' => $this->input->get_post('key')),
          'content_'.$this->lang() => array('like' => $this->input->get_post('content'),'logic'=>'or_')
        ),array('created_at'=>'desc')));
    }

    public function get($id)
    {
        $r = $this->content_model->get_by('key', $id);
        !$r && $r = $this->content_model->get($id);
        $this->json_result(true, '', $r);
    }

    public function insert()
    {
        try {
            $this->check_access('admin');
            $data = array();
            $this->input->post('key') != null && $data['key'] = $this->input->post('key');

            foreach ($this->langs as $lang) {
                $this->input->post('content_'.$lang) != null && $data['content_'.$lang] = $this->input->post('content_'.$lang);
            }

            $r = $this->content_model->insert($data);
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
            foreach ($this->langs as $lang) {
                $this->input->post('content_'.$lang) != null && $data['content_'.$lang] = $this->input->post('content_'.$lang);
            }

            $r = $this->content_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access('admin');
            $r = $this->content_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
