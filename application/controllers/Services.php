<?php

class Services extends MY_Controller
{
    public function index()
    {
        $this->json_result(true, '', $this->service_model->get_all());
    }

    public function query()
    {
        $this->json_result(true, '', $this->service_model->filter($this->input->get_post('page'), $this->input->get_post('count'), array(
            'name_'.$this->lang() => array('like' => $this->input->get_post('name')),
			'name_es' => array('like' => $this->input->get_post('name_es')),
			'name_en' => array('like' => $this->input->get_post('name_en')),
			'name_fr' => array('like' => $this->input->get_post('name_fr')),
            'description_'.$this->lang() => array('like' => $this->input->get_post('description'),'logic'=>'or_')
        )));
    }

    public function insert()
    {
        try {
            $this->check_access('admin');
            $data = array();
            $this->input->post('key') != null && $data['key'] = $this->input->post('key');

            foreach ($this->langs as $lang) {
                $this->input->post('name_'.$lang) != null && $data['name_'.$lang] = $this->input->post('name_'.$lang);
                $this->input->post('description_'.$lang) != null && $data['description_'.$lang] = $this->input->post('description_'.$lang);
            }

            $r = $this->service_model->insert($data);
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

            foreach ($this->langs as $lang) {
                $this->input->post('name_'.$lang) != null && $data['name_'.$lang] = $this->input->post('name_'.$lang);
                $this->input->post('description_'.$lang) != null && $data['description_'.$lang] = $this->input->post('description_'.$lang);
            }

            $r = $this->service_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access('admin');
            $r = $this->service_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
