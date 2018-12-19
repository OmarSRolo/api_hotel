<?php

class Faqs extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->json_result(true, '', $this->faq_model->get_all());
    }

    public function query()
    {
        $this->json_result(true, '', $this->faq_model->filter($this->input->get_post('page'), $this->input->get_post('count'), array(
            'question_'.$this->lang() => array('like' => $this->input->get_post('question')),
            'answer_'.$this->lang() => array('like' => $this->input->get_post('answer'),'logic'=>'or_')
        ), array('created_at' => 'desc')));
    }

    public function get($id)
    {
        $r = $this->faq_model->get_by('question_'.$this->lang(), $id);
        !$r && $r = $this->faq_model->get($id);
        $this->json_result(true, '', $r);
    }

    public function insert()
    {
        try {
            $this->check_access('admin');
            $data = array();

            foreach ($this->langs as $lang) {
                $this->input->post('question_'.$lang) != null && $data['question_'.$lang] = $this->input->post('question_'.$lang);
                $this->input->post('answer_'.$lang) != null && $data['answer_'.$lang] = $this->input->post('answer_'.$lang);
            }

            $r = $this->faq_model->insert($data);
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
                $this->input->post('question_'.$lang) != null && $data['question_'.$lang] = $this->input->post('question_'.$lang);
                $this->input->post('answer_'.$lang) != null && $data['answer_'.$lang] = $this->input->post('answer_'.$lang);
            }

            $r = $this->faq_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access('admin');
            $r = $this->faq_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
