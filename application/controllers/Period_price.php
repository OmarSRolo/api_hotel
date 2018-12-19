<?php

/**
 * Class Period_price created by Yeiniel Alfonso
 */
class Period_price extends MY_Controller
{

    /**
     * Period_price constructor.
     */
    public function __construct()
    {
        parent::__construct();

    }

    public function query()
    {
        try {

            $periods = $this->period_price_model->findAll(array(
                'owner_id' => $this->input->get_post('owner_id'),
                'date_initial' => $this->input->get_post('date_initial'),
                'date_end' => $this->input->get_post('date_end')
            ));

            for ($i=0; $i < count($periods); $i++){
                $periods[$i]['rooms'] = $this->period_price_room_model->getAllByIdPeriod($periods[$i]['id'], $this->input->get_post('lang'));
            }

            $this->json_result(true, '', $periods);
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function queryRooms()
    {
        try {
            $rooms = $this->period_price_model->findOnlyRoomByUser($this->input->get_post('owner_id'), $this->input->get_post('lang'));
            $this->json_result(true, '', $rooms);
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function queryPeriodByRoom(){
        try {
            $rooms = $this->period_price_model->queryPeriodByRoom($this->input->get_post('idRoom'));
            $this->json_result(true, '', $rooms);
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access();
            $data = $this->period_price_model->delete($this->input->post('id'));

            $this->period_price_room_model->delete_by_idPeriod($this->input->post('id'));

            $this->json_result($data, $data ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function insert()
    {

        try {
            $this->check_access();
            $data = array();

            $dateInitial_i18n = date("Y-m-d", strtotime($this->input->post('date_initial')));
            $dateEnd_i18n = date("Y-m-d", strtotime($this->input->post('date_end')));

            $this->input->post('date_initial') != null && $data['date_initial'] = $dateInitial_i18n;
            $this->input->post('date_end') != null && $data['date_end'] = $dateEnd_i18n;

            $this->input->post('name') != null && $data['name'] = $this->input->post('name');
            $this->input->post('owner_id') != null && $data['owner_id'] = $this->input->post('owner_id');

            $r = $this->period_price_model->insert($data);

            $rooms = $this->input->post('rooms');

            foreach ($rooms as $row){
                $this->period_price_room_model->insertAllRoom($r, $row['price'], $row['id']);
            }

            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function update()
    {

        try {
            $this->check_access();
            $data = array();

            $dateInitial_i18n = date("Y-m-d", strtotime($this->input->post('date_initial')));
            $dateEnd_i18n = date("Y-m-d", strtotime($this->input->post('date_end')));

            $this->input->post('date_initial') != null && $data['date_initial'] = $dateInitial_i18n;
            $this->input->post('date_end') != null && $data['date_end'] = $dateEnd_i18n;

            $this->input->post('name') != null && $data['name'] = $this->input->post('name');
            $this->input->post('owner_id') != null && $data['owner_id'] = $this->input->post('owner_id');

            $r = $this->period_price_model->update($this->input->post('id'), $data);

            $rooms = $this->input->post('rooms');

            foreach ($rooms as $row){
                $parameter = array(
                    'price' => $row['price']
                );
                $this->period_price_room_model->update($row['id'], $parameter);
            }

            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

}
