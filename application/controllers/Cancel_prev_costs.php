<?php

class Cancel_prev_costs extends MY_Controller
{
    public function index()
    {
        $this->json_result(true, '', $this->cancel_prev_cost_model->get_all());
    }
}
