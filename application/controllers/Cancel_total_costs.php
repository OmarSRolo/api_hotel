<?php

class Cancel_total_costs extends MY_Controller
{
    public function index()
    {
        $this->json_result(true, '', $this->cancel_total_cost_model->get_all());
    }
}
