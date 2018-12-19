<?php

class Listing_reserves extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('html2pdf');
    }

    public function index($id = 0)
    {
        if (!empty($post_id)) {
            $this->json_result(true, '', $this->listing_reserve_model->get_many_by('listing_id', $id));
        } else {
            $this->json_result(true, '', $this->listing_reserve_model->get_all());
        }
    }

    // No usado
    public function query()
    {
        $this->json_result(true, '', $this->listing_reserve_model->filter($this->input->post('page'), $this->input->post('count'), array(
            'listing_id' => $this->input->get_post('listing_id'),
            'client_id' => $this->input->get_post('client_id'),
            'owner_id' => $this->input->get_post('owner_id'),
            'start_date' => $this->input->get_post('start_date'),
            'end_date' => $this->input->get_post('end_date')
        ), $this->input->post('order_by')));
    }

    // Metodo que devuelve todas las reservas realizadas y recibidas por el usuario
    public function queryall()
    {
        // Recibe por post los datos de la consulta y filtra en el modelo
        $this->json_result(true, '', $this->listing_reserve_model->filterall($this->input->post('page'), $this->input->post('count'), array(
            'listing_id' => $this->input->get_post('listing_id'),
            'client_id' => $this->input->get_post('client_id'),
            'owner_id' => $this->input->get_post('owner_id'),
            'start_date' => $this->input->get_post('start_date'),
            'end_date' => $this->input->get_post('end_date')
        ), $this->input->post('order_by')));

    }

    public function get($id)
    {
        $this->json_result(true, '', $this->listing_reserve_model->get($id));
    }

    public function insert()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('period') != null && $data['period'] = $this->input->post('period');
            $this->input->post('count_persons') != null && $data['count_persons'] = $this->input->post('count_persons');
            $this->input->post('start_date') != null && $data['start_date'] = $this->input->post('start_date');
            $this->input->post('end_date') != null && $data['end_date'] = $this->input->post('end_date');

            $data['childs'] = $this->input->post('childs') == 'true';
            $this->input->post('account_number') != null && $data['account_number'] = $this->input->post('account_number');
            $this->input->post('account_month_expire') != null && $data['account_month_expire'] = $this->input->post('account_month_expire');
            $this->input->post('account_year_expire') != null && $data['account_year_expire'] = $this->input->post('account_year_expire');

            $this->input->post('client_first_name') != null && $data['client_first_name'] = $this->input->post('client_first_name');
            $this->input->post('client_last_name') != null && $data['client_last_name'] = $this->input->post('client_last_name');
            $this->input->post('client_email') != null && $data['client_email'] = $this->input->post('client_email');
            $this->input->post('client_city') != null && $data['client_city'] = $this->input->post('client_city');
            $this->input->post('client_phone') != null && $data['client_phone'] = $this->input->post('client_phone');
            $this->input->post('client_reason') != null && $data['client_reason'] = $this->input->post('client_reason');
            $this->input->post('client_special_request') != null && $data['client_special_request'] = $this->input->post('client_special_request');


            $r = $this->listing_reserve_model->insert($data);

            $this->json_result($r, $r ? '' : validation_errors(), $r ? $this->listing_reserve_model->get($r) : null);

        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }

    }

    public function update()
    {
        try {

            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('count_persons') != null && $data['count_persons'] = $this->input->post('count_persons');
            $this->input->post('start_date') != null && $data['start_date'] = $this->input->post('start_date');
            $this->input->post('end_date') != null && $data['end_date'] = $this->input->post('end_date');
            $this->input->post('ask_review') != null && $data['ask_review'] = $this->input->post('ask_review');

            $data['childs'] = $this->input->post('childs') == 'true';
            $this->input->post('account_number') != null && $data['account_number'] = $this->input->post('account_number');
            $this->input->post('account_month_expire') != null && $data['account_month_expire'] = $this->input->post('account_month_expire');
            $this->input->post('account_year_expire') != null && $data['account_year_expire'] = $this->input->post('account_year_expire');

            $this->input->post('client_first_name') != null && $data['client_first_name'] = $this->input->post('client_first_name');
            $this->input->post('client_last_name') != null && $data['client_last_name'] = $this->input->post('client_last_name');
            $this->input->post('client_email') != null && $data['client_email'] = $this->input->post('client_email');
            $this->input->post('client_city') != null && $data['client_city'] = $this->input->post('client_city');
            $this->input->post('client_phone') != null && $data['client_phone'] = $this->input->post('client_phone');
            $this->input->post('client_reason') != null && $data['client_reason'] = $this->input->post('client_reason');
            $this->input->post('client_special_request') != null && $data['client_special_request'] = $this->input->post('client_special_request');


            if ($this->input->post('status')) {
                $data['status'] = $this->input->post('status');

            } else {
                $data['status'] = 'created';

            }

            $data['id'] = $this->input->post('id');

            $r = $this->listing_reserve_model->update($data['id'], $data);
            $this->json_result($r, $r ? '' : validation_errors());


        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
    //Yeiniel
    public function updateSecond()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('count_persons') != null && $data['count_persons'] = $this->input->post('count_persons');
            $this->input->post('start_date') != null && $data['start_date'] = $this->input->post('start_date');
            $this->input->post('end_date') != null && $data['end_date'] = $this->input->post('end_date');
            $this->input->post('ask_review') != null && $data['ask_review'] = $this->input->post('ask_review');
            $this->input->post('price') != null && $data['price'] = $this->input->post('price');

            $data['childs'] = $this->input->post('childs');
            $this->input->post('account_number') != null && $data['account_number'] = $this->input->post('account_number');
            $this->input->post('account_month_expire') != null && $data['account_month_expire'] = $this->input->post('account_month_expire');
            $this->input->post('account_year_expire') != null && $data['account_year_expire'] = $this->input->post('account_year_expire');

            $this->input->post('client_first_name') != null && $data['client_first_name'] = $this->input->post('client_first_name');
            $this->input->post('client_last_name') != null && $data['client_last_name'] = $this->input->post('client_last_name');
            $this->input->post('client_email') != null && $data['client_email'] = $this->input->post('client_email');
            $this->input->post('client_city') != null && $data['client_city'] = $this->input->post('client_city');
            $this->input->post('client_phone') != null && $data['client_phone'] = $this->input->post('client_phone');
            $this->input->post('client_reason') != null && $data['client_reason'] = $this->input->post('client_reason');
            $this->input->post('client_special_request') != null && $data['client_special_request'] = $this->input->post('client_special_request');

            $data['id'] = $this->input->post('id');
            $data['update'] = true;

            $r = $this->listing_reserve_model->update($data['id'], $data);

            $this->json_result($r, $r ? '' : validation_errors());

        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function sendCalification()
    {
        try {
            $this->check_access();
            $data = array();
            $this->input->post('listing_id') != null && $data['listing_id'] = $this->input->post('listing_id');
            $this->input->post('count_persons') != null && $data['count_persons'] = $this->input->post('count_persons');
            $this->input->post('start_date') != null && $data['start_date'] = $this->input->post('start_date');
            $this->input->post('end_date') != null && $data['end_date'] = $this->input->post('end_date');
            $this->input->post('ask_review') != null && $data['ask_review'] = $this->input->post('ask_review');

            $data['childs'] = $this->input->post('childs') == 'true';
            $this->input->post('account_number') != null && $data['account_number'] = $this->input->post('account_number');
            $this->input->post('account_month_expire') != null && $data['account_month_expire'] = $this->input->post('account_month_expire');
            $this->input->post('account_year_expire') != null && $data['account_year_expire'] = $this->input->post('account_year_expire');

            $this->input->post('client_first_name') != null && $data['client_first_name'] = $this->input->post('client_first_name');
            $this->input->post('client_last_name') != null && $data['client_last_name'] = $this->input->post('client_last_name');
            $this->input->post('client_email') != null && $data['client_email'] = $this->input->post('client_email');
            $this->input->post('client_city') != null && $data['client_city'] = $this->input->post('client_city');
            $this->input->post('client_phone') != null && $data['client_phone'] = $this->input->post('client_phone');
            $this->input->post('client_reason') != null && $data['client_reason'] = $this->input->post('client_reason');
            $this->input->post('client_special_request') != null && $data['client_special_request'] = $this->input->post('client_special_request');


            if ($this->input->post('status')) {
                $data['status'] = $this->input->post('status');
            }

            $data['id'] = $this->input->post('id');

            $r = $this->listing_reserve_model->update($data['id'], $data);
            $this->json_result($r, $r ? '' : validation_errors());


        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    // Acepta o deniega las reservas por correo
    public function check_reserver($lang, $email, $id_room, $status)
    {

        try {
            // Descifra lo que viene por la url del correo enviado
            $temp_email = JWT::decode($email);
            $temp_id = JWT::decode($id_room);
            $temp_status = JWT::decode($status);

            // Envia los datos de negacion o confirmacion de la reserva
            $this->listing_reserve_model->check_status($lang, $temp_email, $temp_id, $temp_status);


        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
        redirect(JWT::decode($status) == 'confirmed' ? 'http://localhost/listings/confirmed' : 'http://localhost/listings/cancelled');
        //$this->json_result(true, '', $data);
    }

    //Yeiniel
    public function downloadedReservesPdf($lang, $id, $type_user)
    {
        //Set folder to save PDF to
        $this->html2pdf->folder('./assets/pdfs/');

        //Set the filename to save/download as
        $this->html2pdf->filename('reserves.pdf');

        //Set the paper defaults
        $this->html2pdf->paper('a4', 'portrait');

        $content = $this->listing_reserve_model->created_range_pdf($lang, $id, $type_user);
        
        //Load html view
        $this->html2pdf->html($this->load->view($type_user=='owner'?'pdf':'pdfclient', $content, true));
        //$this->html2pdf->create('downloaded');

        if ($this->html2pdf->create('downloaded')) {}
    }

    public function downloadedReservesPdfTotal()
    {
        //Set folder to save PDF to
       $this->html2pdf->folder('./assets/pdfs/');

        //Set the filename to save/download as
        $this->html2pdf->filename('reserves.pdf');

        //Set the paper defaults
        $this->html2pdf->paper('a4', 'portrait');

        $content = $this->listing_reserve_model->created_range_pdf_admin();

        //Load html view
        $this->html2pdf->html($this->load->view('pdfadmin', $content, true));
        //$this->html2pdf->create('downloaded');

        if ($this->html2pdf->create('downloaded')) {}
    }

    public function delete()
    {
        try {
            $this->check_access();
            $data = $this->listing_reserve_model->delete($this->input->post('id'));
            $this->json_result($data, $data ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
