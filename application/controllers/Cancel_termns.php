<?php

class Cancel_termns extends MY_Controller
{
    public function index()
    {
        $this->json_result(true, '', $this->cancel_term_model->get_all());
    }
}
