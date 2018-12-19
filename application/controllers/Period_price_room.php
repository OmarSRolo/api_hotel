<?php

/**
 * Class Period_price_room
 */
class Period_price_room extends MY_Controller
{

    /**
     * Period_price_room constructor.
     */
    public function __construct()
    {
        parent::__construct();

    }

    //Devuelvo todas las temporadas que posea el usuario
    // Espera recibir por POST el user_id, page y count para realizar el paginado
    /**
     *
     */
    public function index()
    {
        $this->check_access();
        $this->json_result(true, '', $this->period_price_room_model->filter($this->input->post('page'), $this->input->post('count'), array(
            'user_id' => $this->input->get_post('user_id'))));

    }

    /**
     * @param $id
     */
    public function get($id)
    {
        $this->check_access();
        $this->json_result(true, '', $this->period_price_room_model->get($id));
    }

    /**
     *
     */
    public function insert()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('period_id') != null && $data['period_id'] = $this->input->post('period_id');
            $this->input->post('prices') != null && $data['price'] = $this->input->post('prices');

            $r = $this->period_price_room_model->insert($data);
            $this->json_result($r, $r ? '' : validation_errors());

        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }

    }


    /**
     *
     */
    public function update()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('period_id') != null && $data['period_id'] = $this->input->post('period_id');
            $this->input->post('price') != null && $data['price'] = $this->input->post('price');

            $r = $this->period_price_room_model->update($data);
            $this->json_result($r, $r ? '' : validation_errors());

        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }


    }


    /**
     *
     */
    public function delete()
    {
        try {
            $this->check_access();
            $data = $this->listing_price_model->delete($this->input->post('id'));
            $this->json_result($data, $data ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    /**
     *
     */
    public function getListingByUser()
    {

        $this->check_access();
        $this->json_result(true, '', $this->period_price_room_model->getAllListingByUser($this->input->post('page'), $this->input->post('count'), array(
            'user_id' => $this->input->get_post('user_id'))));

    }
}
