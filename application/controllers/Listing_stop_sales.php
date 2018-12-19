<?php

class Listing_stop_sales extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function query()
    {
        $this->json_result(true, '', $this->listing_stop_sale_model->filter($this->input->post('page'), $this->input->post('count'), array(
            'user_id' => $this->input->get_post('user_id'),
            'start_date' => $this->input->get_post('start_date'),
            'end_date' => $this->input->get_post('end_date')
        ), $this->input->post('order_by')));
    }

    //Yeiniel [New]
    public function queryDate()
    {
        $this->json_result(true, '', $this->listing_stop_sale_model->findAll(array(
            'user_id' => $this->input->get_post('user_id'),
            'start_date' => $this->input->get_post('start_date'),
            'end_date' => $this->input->get_post('end_date')
        )));
    }

    public function get($id)
    {
        $this->json_result(true, '', $this->listing_stop_sale_model->get($id));
    }

    //Yeiniel [Update]
    public function insert()
    {
        try {
            $this->check_access();
            $data = array();
            $dateInitial_i18n = date("Y-m-d", strtotime($this->input->post('start_date')));
            $dateEnd_i18n = date("Y-m-d", strtotime($this->input->post('end_date')));

            $this->input->post('start_date') != null && $data['start_date'] = $dateInitial_i18n;
            $this->input->post('end_date') != null && $data['end_date'] = $dateEnd_i18n;

            $r = $this->listing_stop_sale_model->insert($data);
            
            $this->json_result($r, $r ? '' : validation_errors(), $r ? $this->listing_stop_sale_model->get($r) : null);
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access();
            $r = $this->listing_stop_sale_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    //Yeiniel [New]
    public function deleteRange(){
        try{
            $this->check_access();

            $lang = $this->input->get_post('lang');

            $items = $this->listing_stop_sale_model->findAllForDelete(array(
                'user_id' => $this->input->get_post('user_id'),
                'start_date' => $this->input->get_post('start_date'),
                'end_date' => $this->input->get_post('end_date')
            ))['results'];

            $data = array();

            for ($i = 0; $i < count($items); $i++){
                $data[$i] = $items[$i]['id'];
            }

            if(count($items)==0){
                $this->json_result(false, $lang=='en'? "Error: You should select other dates intervals." :"Error: El intervalo de fecha seleccionado no coincide con ningún intervalo almacenado.");
            }else{
                $r = $this->listing_stop_sale_model->delete_many($data);
                $this->json_result($r, $r ? '' : validation_errors());
            }

        }catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
