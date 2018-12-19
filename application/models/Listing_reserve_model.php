<?php

class Listing_reserve_model extends MY_Model
{
    public $before_get = array('before_get');
    public $after_get = array('after_get');

    public $before_create = array('before_create');
    public $after_create = array('after_create');

    public $before_update = array('before_update');
    public $after_update = array('after_update');

    public $before_delete = array('before_delete');
    public $after_delete = array('after_delete');

    public $temps = array();

    public $My_Site_Url;

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'listing_reserves';
        $this->load->helper('date');
        $data = explode('api/', base_url());
        $this->My_Site_Url = $data[0];
        $this->validate = array();
    }


    /*************Callback******************/
    protected function before_get()
    {
    }

    // Trigger que se ejecuta antes de obtener un objeto
    protected function after_get($row)
    {
        if (isset($row)) {
            $ci = &get_instance();
            $user = $ci->login_user();

            $row['client'] = $this->user_model->get($row['client_id']);
            $row['listing'] = $this->listing_model->get($row['listing_id']);
            $row['bank_account'] = $this->bank_model->get($row['bank_account_id']);
            //$row['price'] = $this->listing_price_model->get($row['listing_price_id']);

            $row['created_at'] = mysql_gm_spain($row['created_at']);
            //$row['amount_total'] = $row['amount_owner'] + $row['amount_provider'];

            $created = new DateTime($row['created_at']);
            $created->add(new DateInterval('P3D'));
            //var_dump($created);
            //var_dump(new DateTime());
            //$row['is_flexible_cancellable'] = $row['is_flexible'] == '1' && ($created >= new DateTime());

            //$row['is_editable'] = ($row['client_id'] == $user['id']) || $user['role'] == 'admin';
        }

        return $row;
    }

    // Trigger que se ejecuta despues de crear un objeto
    protected function before_create($row)
    {
        $this->db->trans_start();

        $ci = &get_instance();
        $user = $ci->login_user(true);
        $row['client_id'] = $user['id'];
        $row['status'] = 'created';


        $listing = $this->listing_model->get($row['listing_id']);

        //$price = $listing['price_'.$row['price_type']] * $row['period'];
        //$row['amount_provider'] = $price * 0.1;
        //$row['amount_owner'] = $price - ($row['amount_provider']);
        //$row['amount_salved'] = 0;


        $row = $this->calculatePrice($row, $listing);

        /*if ($user['amount_flexible'] > 0) {
            // $row['amount_provider'] = $user['amount_flexible'] > $row['amount_provider'] ? 0 : ($row['amount_provider'] - $user['amount_flexible']);
            $row['amount_salved'] = $user['amount_flexible'] >= $row['amount_provider'] ? $row['amount_provider'] : $user['amount_flexible'];
        }
        $row['amount_to_pay'] = $row['amount_provider'] - $row['amount_salved'] + ($row['is_flexible'] ? 2 : 0);
        */
        unset($row['period']);

        if ($this->listing_stop_sale_model->count_by("user_id = " . $listing['owner']['id'] . " and (('" . $row['start_date'] . "' between substring(start_date,1,12) and substring(end_date,1,12)) or ('" . $row['end_date'] . "' between substring(start_date,1,12) and substring(end_date,1,12)))   ") > 0) {
            throw new Exception(lang_extension('listing_reserve_model.not_avalaible_stope_sales'));
        }

        //Check availibilty
        if ($this->count_by('listing_id = ' . $row['listing_id'] . " and status = 'created' and (('" . $row['start_date'] . "' between substring(start_date,1,12) and substring(end_date,1,12)) or ('" . $row['end_date'] . "' between substring(start_date,1,12) and substring(end_date,1,12)))   ") > 0) {
            throw new Exception(lang_extension('listing_reserve_model.not_avalaible'));
        }

        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');

        return $row;
    }

    protected function after_create($row)
    {

        $this->db->trans_complete();

        return $row;
    }

    // Trigger que se ejecuta antes de actualizar
    protected function before_update($row)
    {

        if (!empty($row['status']) && $row['status'] == 'cancelled') {
            $reserve = $this->get($row['id']);
            if ($reserve['is_flexible_cancellable']) {
                $a = $reserve['client']['amount_flexible'] + $reserve['amount_to_pay'] - 2;
                $this->user_model->update($reserve['client']['id'], array('amount_flexible' => $a), true);
            }
        }
        if (!empty($row['status']) && $row['status'] == 'created') {
            $reserve = $this->get($row['id']);
            if ($reserve['amount_salved'] > 0) {
                $a = $reserve['client']['amount_flexible'] - $reserve['amount_salved'];
                $this->user_model->update($reserve['client']['id'], array('amount_flexible' => $a), true);
            }
        }

        if (!empty($row['ask_review']) && $row['ask_review'] == '1') {
            $reserve = $this->get($row['id']);
            if ($reserve['ask_review'] == 1) {
                throw new Exception(lang_extension('listing_reserve_model.ask_review_already'));
            }
        }

        $row['updated_at'] = date('Y-m-d H:i:s');

        return $row;
    }

    //yeiniel buscar Base de datos las reservaciones disponibles en un intervalo de tres meses
    private function created_ranged_date($lang, $reserve)
    {
        $data = array('listing_id' => $reserve['listing_id'], 'end_date >' => $reserve['end_date']);
        $result = $this->db->select('start_date, end_date')->get_where('listing_reserves', $data)->result_array();

        $data1 = array('user_id' => $reserve['client_id'], 'end_date >' => $reserve['end_date']);
        $stopsales = $this->db->select('start_date, end_date')->get_where('listing_stop_sales', $data1)->result_array();

        //$month = substr($reserve['end_date'],5,2);

        $dateInitial = date("Y-m-d", strtotime($reserve['end_date']));
        $dateEnd = date('Y-m-d', strtotime('+3 month', strtotime($dateInitial)));
        $dateInitial = date('Y-m-d', strtotime('+1 days', strtotime($dateInitial)));

        $index = 0;
        $arrayDate = array();

        while ($dateInitial != $dateEnd) {
            $insert = true;
            foreach ($result as $item) {
                $initial = date("Y-m-d", strtotime($item['start_date']));
                $final = date("Y-m-d", strtotime($item['end_date']));
                if ($dateInitial >= $initial and $dateInitial <= $final) {
                    $insert = false;
                    break;
                }
            }
            if ($insert) {
                foreach ($stopsales as $item) {
                    $initial = date("Y-m-d", strtotime($item['start_date']));
                    $final = date("Y-m-d", strtotime($item['end_date']));
                    if ($dateInitial >= $initial and $dateInitial <= $final) {
                        $insert = false;
                        break;
                    }
                }
            }

            $dateInitial_i18n = date($lang == "en" ? "Y-m-d" : "d-m-Y", strtotime($dateInitial));
            $arrayDate[$index] = $insert ? $dateInitial_i18n :
                "<del style='color: #E13300'>" . $dateInitial_i18n . "</del>";

            $dateInitial = date('Y-m-d', strtotime('+1 days', strtotime($dateInitial)));
            ++$index;
        }

        return $this->created_ranged_date_Html($arrayDate, $lang);
    }

    //yeiniel Devuelve una tabla html de reservaciones disponibles en un intervalo de tres meses
    private function created_ranged_date_Html($arrayDate, $lang)
    {

        $result = '<tr style="width: 100%">
                    <td                                                      data-color="text"
                        data-size="size text"
                        data-min="10"
                        data-max="26"
                        data-link-color="link text color"    data-link-style="font-weight:bold; text-decoration:underline; color:#40aceb;"
                        align="center;" colspan="4" style="font: 16px/25px Arial, Helvetica, sans-serif; color:#888; padding:0 0 23px; text-align:center;">
                        <strong>' . ($lang == 'en' ? 'Available reservation' : 'Reservaciones Disponibles') . '</strong>
                    </td>
                    </tr>';

        $td = '<td style="width: 25%; text-align: center; font: 16px/25px Arial, Helvetica, sans-serif; 
                color:#888; padding:0 0 23px;" data-color="text" data-size="size text" data-min="10" 
                data-max="26" data-link-color="link text color" 
                data-link-style="font-weight:bold; text-decoration:underline; color:#40aceb;">';

        $col = 4;
        for ($idx = 0; $idx < count($arrayDate); ++$idx) {

            $content = $td . $arrayDate[$idx] . "</td>";
            switch ($col) {
                case 4:
                    $result = $result . '<tr style="width: 100%">' . $content;
                    break;
                case 3:
                case 2:
                    $result = $result . $content;
                    break;
                default:
                    $result = $result . $content . '</tr>';
                    $col = 5;
                    break;
            }
            $col--;
        }

        return $table = '<table style="width: 100%">' . $result . '</table>';
    }

    // Trigger que se ejecuta luego de actualizar
    protected function after_update($row)
    {
        if ($row[0]['update']) {
            $ci = &get_instance();
            $lang = $ci->lang();
            $reserve = $this->get($row[0]['id']);
            $datos = array(

                'client' => $reserve['client_first_name'] . " " . $reserve['client_last_name'],
                'listing_id' => $reserve['listing_id'],
                'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                'hotel' => $reserve['client']['hotel_name'],
                'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                'price' => $reserve['price'],
                'start_date' => format_date($reserve['start_date']),
                'end_date' => format_date($reserve['end_date']),
                'termns' => $reserve['listing']['owner']['cancel_term']['name_' . $lang],
                'email' => $reserve['listing']['owner']['email'],
                'phone' => $reserve['listing']['owner']['phone'],
                'lang' => $lang,
                'owner_email' => $reserve['listing']['owner']['email'],


            );


            $this->send_answer_by_email($datos);


        };

        // Envio de correo si niego la solicitud
        if (!empty($row[0]['status']) && $row[0]['status'] == 'cancelled') {
            $ci = &get_instance();
            $temp_lang = $ci->lang();
            $reserve = $this->get($row[0]['id']);
            $current_user = $ci->login_user();

            //send email to owwner
            $ci->sent_advanced_email($reserve['listing']['owner']['email'], lang_extension('listing_reserve_model.email_listing_cancelled_subject'),
                ($current_user['id'] == $reserve['listing']['owner']['id']) ? 'listing_owner_cancelled_owner' : 'listing_client_cancelled_owner',
                array(
                    'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                    'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                    'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                    'listing_id' => $reserve['listing_id'],
                    'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                    'price' => $reserve['price'],
                    'start_date' => format_date($reserve['start_date']),
                    'end_date' => format_date($reserve['end_date']),
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                ), null, null, 'bookings-noreply@atuhotel.com');

            //send email to client
            $ci->sent_advanced_email($reserve['client']['email'], lang_extension('listing_reserve_model.email_listing_cancelled_subject'),
                ($current_user['id'] == $reserve['listing']['owner']['id']) ? 'listing_owner_cancelled_client' : 'listing_client_cancelled_client',
                array(
                    'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                    'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                    'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                    'listing_id' => $reserve['listing_id'],
                    'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                    'price' => $reserve['price'],
                    'start_date' => format_date($reserve['start_date']),
                    'end_date' => format_date($reserve['end_date']),
                    'termns' => $reserve['listing']['owner']['termns'],
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                    'ciclo' => $this->created_ranged_date($temp_lang, $reserve),
                ), null, null, 'bookings-noreply@atuhotel.com');

            $ci->sent_advanced_email('bookings-noreply@atuhotel.com', lang_extension('listing_reserve_model.email_listing_cancelled_subject'),
                ($current_user['id'] == $reserve['listing']['owner']['id']) ? 'listing_owner_cancelled_admin' : 'listing_client_cancelled_admin',
                array(
                    'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                    'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                    'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                    'listing_id' => $reserve['listing_id'],
                    'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                    'price' => $reserve['price'],
                    'start_date' => format_date($reserve['start_date']),
                    'end_date' => format_date($reserve['end_date']),
                    'termns' => $reserve['listing']['owner']['termns'],
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                ), null, null, 'bookings-noreply@atuhotel.com');
        }

        if (!empty($row[0]['status']) && $row[0]['status'] == 'confirmed') {
            $ci = &get_instance();
            $reserve = $this->get($row[0]['id']);
            $current_user = $ci->login_user();

            //send email to owwner
            $ci->sent_advanced_email($reserve['listing']['owner']['email'], lang_extension('listing_reserve_model.email_listing_confirmed_subject'),
                ($current_user['id'] == $reserve['listing']['owner']['id']) ? 'listing_owner_confirmed_owner' : 'listing_client_confirmed_owner',
                array(
                    'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                    'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                    'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                    'listing_id' => $reserve['listing_id'],
                    'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                    'price' => $reserve['price'],

                    'start_date' => format_date($reserve['start_date']),
                    'end_date' => format_date($reserve['end_date']),
                    'termns' => $reserve['listing']['owner']['cancel_term']['name'],
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                ), null, null, 'bookings-noreply@atuhotel.com');

            //send email to client
            $ci->sent_advanced_email($reserve['client']['email'], lang_extension('listing_reserve_model.email_listing_confirmed_subject'),
                ($current_user['id'] == $reserve['listing']['owner']['id']) ? 'listing_owner_confirmed_client' : 'listing_client_confirmed_client',
                array(
                    'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                    'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                    'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                    'listing_id' => $reserve['listing_id'],
                    'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                    'price' => $reserve['price'],
                    'start_date' => format_date($reserve['start_date']),
                    'end_date' => format_date($reserve['end_date']),
                    'termns' => $reserve['listing']['owner']['termns'],
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                    'email' => $reserve['listing']['owner']['email'],
                    'phone' => $reserve['listing']['owner']['phone'],

                ), null, null, 'bookings-noreply@atuhotel.com');


            // send email to the admin
            $ci->sent_advanced_email('bookings-noreply@atuhotel.com', lang_extension('listing_reserve_model.email_listing_confirmed_subject'),
                ($current_user['id'] == $reserve['listing']['owner']['id']) ? 'listing_owner_confirmed_admin' : 'listing_client_confirmed_admin',
                array(
                    'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                    'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                    'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                    'listing_id' => $reserve['listing_id'],
                    'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                    'price' => $reserve['price'],
                    'start_date' => format_date($reserve['start_date']),
                    'end_date' => format_date($reserve['end_date']),
                    'termns' => $reserve['listing']['owner']['termns'],
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                ), null, null, 'bookings-noreply@atuhotel.com');
        }


        if (!empty($row[0]['status']) && $row[0]['status'] == 'created') {

            $ci = &get_instance();
            $temp_lang = $ci->lang();

            $reserve = $this->get($row[0]['id']);

            //send email to owner
            $ci->sent_advanced_email($reserve['listing']['owner']['email'], lang_extension('listing_reserve_model.email_listing_confirmed_subject'), 'listing_reserved_owner', array(
                'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                'owner_phone' => $reserve['listing']['owner']['phone'],
                'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                'listing_id' => $reserve['listing_id'],
                'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                'client_name' => $reserve['client_first_name'] . ' ' . $reserve['client_last_name'],
                'client_email' => $reserve['client_email'],
                'client_phone' => $reserve['client_phone'],
                'client_reason' => lang_extension('listing_reserve_model.' . $reserve['client_reason']),
                'client_city' => $reserve['client_city'],
                'client_special_request' => $reserve['client_special_request'],
                'start_date' => format_date($reserve['start_date']),
                'end_date' => format_date($reserve['end_date']),
                'price' => $reserve['price'],
                'termns' => $reserve['listing']['owner']['termns'],
                'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
                'yes_url' => $this->My_Site_Url . 'api/listing_reserves/check_reserver/' . $temp_lang . '/' . JWT::encode($reserve['client_email'], '') . '/' . JWT::encode($reserve['listing_id'], '') . '/' . JWT::encode('confirmed', ''),
                'no_url' => $this->My_Site_Url . 'api/listing_reserves/check_reserver/' . $temp_lang . '/' . JWT::encode($reserve['client_email'], '') . '/' . JWT::encode($reserve['listing_id'], '') . '/' . JWT::encode('cancelled', ''),
            ), null, null, 'bookings-noreply@atuhotel.com');


            //send email to client
            $ci->sent_advanced_email($reserve['client']['email'], lang_extension('listing_reserve_model.email_listing_confirmed_subject'), 'listing_reserved_client', array(
                'owner_email' => $reserve['listing']['owner']['email'],
                'owner_phone' => $reserve['listing']['owner']['phone'],
                'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                'listing_id' => $reserve['listing_id'],
                'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                'start_date' => format_date($reserve['start_date']),
                'end_date' => format_date($reserve['end_date']),
                'price' => $reserve['price'],
                'termns' => $reserve['listing']['owner']['termns'],
                'listing_url' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'],
            ), null, null, 'bookings-noreply@atuhotel.com');

            //send email to admin
            $ci->sent_advanced_email('bookings-noreply@atuhotel.com', lang_extension('listing_reserve_model.email_listing_confirmed_subject'), 'listing_reserved_admin', array(
                'owner_account' => $reserve['listing']['owner']['bank_account']['name'] . '->' . $reserve['listing']['owner']['bank_account_number'],
                'owner_phone' => $reserve['listing']['owner']['phone'],
                'hotel_name' => $reserve['listing']['owner']['hotel_name'],
                'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                'listing_id' => $reserve['listing_id'],
                'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                'start_date' => format_date($reserve['start_date']),
                'end_date' => format_date($reserve['end_date']),
                'price' => $reserve['price']
            ), null, null, 'bookings-noreply@atuhotel.com');

        }

        if (!empty($row[0]['ask_review']) && $row[0]['ask_review'] == 1) {
            $ci = &get_instance();
            $reserve = $this->get($row[0]['id']);
            //send email to client
            $ci->sent_advanced_email($reserve['client']['email'], lang_extension('listing_reserve_model.email_listing_review_subject'), 'listing_review_client', array(
                'owner' => $reserve['listing']['owner']['first_name'] . ' ' . $reserve['listing']['owner']['last_name'],
                'client' => $reserve['client']['first_name'] . ' ' . $reserve['client']['last_name'],
                'listing_id' => $reserve['listing_id'],
                'listing_location' => $reserve['listing']['country']['name'] . ',' . $reserve['listing']['city'],
                'listing_url_review' => $this->My_Site_Url . 'listings/details/' . $reserve['listing_id'] . '?review=true',
            ));
        }
        $this->db->trans_complete();

        return $row;
    }

    protected function before_delete($row)
    {
        $this->db->trans_start();

        return $row;
    }

    protected function after_delete($row)
    {
        $this->db->trans_complete();

        return $row;
    }

    /*********End Callback****************/
    // No se usa
    public function filter($page, $count, $filter = array(), $order = array())
    {
        $this->db->select($this->_table . '.*')->from($this->_table)->where('listing_reserves.status !=', 'created');

        if (!empty($filter['listing_id'])) {
            $this->db->where('listing_id', $filter['listing_id']);
        }
        if (!empty($filter['status'])) {
            $this->db->where('status', $filter['status']);
        }
        if (!empty($filter['owner_id'])) {
            $this->db->join('listings', 'listings.id = listing_id')->where('listings.owner_id', $filter['owner_id']);
        }
        if (!empty($filter['client_id'])) {
            $this->db->where('client_id', $filter['client_id']);
        }
        if (!empty($filter['start_date'])) {
            $this->db->where('substring(start_date,1,10) <=', $filter['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $this->db->where('substring(end_date,1,10) <=', $filter['end_date']);
        }

        $total = $this->count_ext();

        $this->db->limit($count, ($page - 1) * $count);

        $data = $this->get_all_ext();

        return array('total' => $total, 'results' => $data);
    }

    // Metodo que filtra las peticiones realizadas y recibidas por usuarios
    public function filterall($page, $count, $filter = array(), $order = array())
    {

        // Selecciona la tabla
        $this->db->select($this->_table . '.*')->from($this->_table);

        // Condiciona segun lo recibido por POST
        if (!empty($filter['listing_id'])) {
            $this->db->where('listing_id', $filter['listing_id']);
        }
        if (!empty($filter['status'])) {
            $this->db->where('status', $filter['status']);
        }
        if (!empty($filter['owner_id'])) {
            $this->db->join('listings', 'listings.id = listing_id')->where('listings.owner_id', $filter['owner_id']);
        }

        if (!empty($filter['client_id'])) {
            $this->db->where('client_id', $filter['client_id']);
        }
        if (!empty($filter['start_date'])) {
            $this->db->where('substring(start_date,1,10) >=', $filter['start_date']);
        }
        if (!empty($filter['end_date'])) {
            $this->db->where('substring(end_date,1,10) <=', $filter['end_date']);
        }

        // Obtiene el numero de objetos de la consultas
        $total = $this->count_ext();

        // Limita el resultado
        $this->db->limit($count, ($page - 1) * $count);

        // Obtiene los resultados de la consulta
        $data = $this->get_all_ext();

        return array('total' => $total, 'results' => $data);
    }

    // Omar
    // Validacion y cambio de estado cuando la reserva viene por el correo
    public function check_status($lang, $email, $id_room, $status)
    {

        // Instancia del controlador que lo ejecuta
        $ci = &get_instance();

        // Datos a validar
        $data = array('listing_id' => $id_room, 'client_email' => $email);

        // Consulta para ver si la reserva es correcta
        $temp_query = $this->db->get_where('listing_reserves', $data, 1)->result_array();

        // Consulta a las reservaciones y usuarios
        $query = $this->get($temp_query[0]['id']);

        $submessage_es = 'Usted puede cancelar su reserva en cualquier momento accediendo a la extranet teniendo en cuenta que los cargos a pagar fueron aceptados por usted al momento de reservar';

        $submessage_en = 'You can cancel your reservation at any time taking into account that the charges to be paid were accepted by you at the time of booking.';


        // Condiciona consulta
        if (!empty($query)) {


            // Actualiza el estado en caso de que exista la reserva
            $this->db->update('listing_reserves', array('status' => $status), $data);


            if ($status == "confirmed") {


                $datos = array(

                    'client' => $query['client_first_name'] . " " . $query['client_last_name'],
                    'listing_id' => $id_room,
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $id_room,
                    'status' => $status,
                    /*'message' => $message,*/
                    'hotel' => $query['client']['hotel_name'],
                    'hotel_name' => $query['listing']['owner']['hotel_name'],
                    'owner' => $query['listing']['owner']['first_name'] . ' ' . $query['listing']['owner']['last_name'],
                    'listing_location' => $query['listing']['country']['name'] . ',' . $query['listing']['city'],
                    'price' => $query['price'],
                    'start_date' => format_date($query['start_date']),
                    'end_date' => format_date($query['end_date']),
                    'termns' => $query['listing']['owner']['cancel_term']['name_' . $lang],
                    'email' => $query['listing']['owner']['email'],
                    'phone' => $query['listing']['owner']['phone'],
                    'submessage_es' => $lang == 'es' ? $submessage_es : "",
                    'submessage_en' => $lang == 'en' ? $submessage_en : "",
                    /*'email' => $email,*/
                    'lang' => $lang,
                    'owner_email' => $query['listing']['owner']['email'],
                    'status_email' => $status,


                );

                $this->send_answer_by_email($datos);
            } else {
                $datos = array(

                    'client' => $query['client_first_name'] . " " . $query['client_last_name'],
                    'listing_id' => $id_room,
                    'listing_url' => $this->My_Site_Url . 'listings/details/' . $id_room,
                    'status' => $status,
                    'hotel_name' => $query['listing']['owner']['hotel_name'],
                    'owner' => $query['listing']['owner']['first_name'] . ' ' . $query['listing']['owner']['last_name'],
                    'listing_location' => $query['listing']['country']['name'] . ',' . $query['listing']['city'],
                    'price' => $query['price'],
                    'start_date' => format_date($query['start_date']),
                    'end_date' => format_date($query['end_date']),
                    'ciclo' => $this->created_ranged_date($lang, $query),
                    'email' => $email,
                    'lang' => $lang,
                    'owner_email' => $query['listing']['owner']['email'],
                    'status_email' => $status,

                );
                $this->send_answer_by_email($datos);
            }

            // No existe la reserva enviada.
        } else {

            return 'La operacion no se completo correctamente. Mensaje no enviado';
        }

    }

    //Yeiniel pdf
    public function created_range_pdf($lang, $id, $type_user)
    {
        $user = $this->db->select('*')->get_where('users', array('id' => $id))->result_array()[0];
        return $type_user == 'client' ? $this->created_range_pdf_client($lang, $user) : $this->created_range_pdf_owner($lang, $user);
    }

    public function created_range_pdf_admin()
    {
        $users = $this->db->select('*')->get_where('users', array('role' => 'owner'))->result_array();

        $content = array();

        for ($i = 0; $i < count($users); $i++) {
            $content[$i] = $this->created_range_pdf_owner('es', $users[$i]);
        }
        return array(
            'content' => $content
        );
    }

    private function created_range_pdf_owner($lang, $user)
    {
        $this->db->distinct();
        $this->db->select('listings.id, listings.city,
                            countries.name_en as countries_name_en, 
                            countries.name_es as countries_name_es,
                            listing_types.name_en as listing_types_name_en, 
                            listing_types.name_es as listing_types_name_es,
                            listings.description_en,
                            listings.description_es');
        $this->db->from('listings');
        $this->db->join('countries', 'countries.code = listings.country_code');
        $this->db->join('listing_types', 'listing_types.id = listings.listing_type_id');
        $roomArray = $this->db->where(array('owner_id' => $user["id"]))->get()->result_array();

        list($dateEnd, $dateStart) = $this->convertDate();

        $reservesArray = array();
        for ($index = 0; $index < count($roomArray); $index++) {
            $reservesArray[$index] = $this->db->distinct()->select('*')->get_where('listing_reserves',
                array(
                    'listing_id' => $roomArray[$index]["id"],
                    'start_date >=' => $dateStart,
                    'end_date <=' => $dateEnd

                ))->result_array();
        }

        list($dateInitial_i18n, $dateEnd_i18n) = $this->convertDetail18n($lang, $dateStart, $dateEnd);

        return array(
            'title' => ($lang == 'en' ? 'Bookings received (between ' . $dateInitial_i18n . ' and ' . $dateEnd_i18n :
                    'Reservas recibidas (entre ' . $dateInitial_i18n . ' y ' . $dateEnd_i18n) . ')',
            'hotel' => $user["hotel_name"],
            'owner' => ($lang == 'en' ? 'Owner: ' : 'Dueño: ') . $user["first_name"] . ' ' . $user["last_name"] . ' (' . $user["email"] . ')',
            'reservesArray' => $reservesArray,
            'roomArray' => $roomArray,
            'lang' => $lang
        );
    }

    private function created_range_pdf_client($lang, $user)
    {
        list($dateEnd, $dateStart) = $this->convertDate();

        $this->db->distinct();
        $this->db->select('listings.id, listings.city,
                            countries.name_en as countries_name_en, 
                            countries.name_es as countries_name_es,
                            listing_types.name_en as listing_types_name_en, 
                            listing_types.name_es as listing_types_name_es,
                            listing_reserves.status,
                            listing_reserves.start_date,
                            listing_reserves.end_date,
                            listing_reserves.price,
                            users.hotel_name,
                            users.email');
        $this->db->from('listings');
        $this->db->join('users', 'users.id = listings.owner_id');
        $this->db->join('countries', 'countries.code = listings.country_code');
        $this->db->join('listing_types', 'listing_types.id = listings.listing_type_id');
        $this->db->join('listing_reserves', 'listing_reserves.listing_id = listings.id');
        $reservesArray = $this->db->distinct()->where(
            array(
                'client_id' => $user["id"],
                'start_date >=' => $dateStart,
                'end_date <=' => $dateEnd
            )
        )->get()->result_array();

        list($dateInitial_i18n, $dateEnd_i18n) = $this->convertDetail18n($lang, $dateStart, $dateEnd);

        $name = $user["first_name"] . ' ' . $user["last_name"] . ' (' . $user['email'] . ')';

        return array(
            'title' => ($lang == 'en' ? 'Bookings made by ' . $name . ' (between ' . $dateInitial_i18n . ' and ' . $dateEnd_i18n :
                    'Reservas realizadas por ' . $name . ' (entre ' . $dateInitial_i18n . ' y ' . $dateEnd_i18n) . ')',
            'hotel' => 'Hotel: ' . $user["hotel_name"],
            'reserves' => $reservesArray,
            'lang' => $lang
        );
    }

    /**
     * @return array
     */
    private function convertDate()
    {
        $now = mdate('%Y-%m-%d', time());
        $dateEnd = date('Y-m-d', strtotime('+3 month', strtotime($now)));
        $dateStart = date('Y-m-d', strtotime('-3 month', strtotime($now)));
        return array($dateEnd, $dateStart);
    }

    /**
     * @param $lang
     * @param $dateStart
     * @param $dateEnd
     * @return array
     */
    private function convertDetail18n($lang, $dateStart, $dateEnd)
    {
        $dateInitial_i18n = date($lang == "en" ? "Y-m-d" : "d-m-Y", strtotime($dateStart));
        $dateEnd_i18n = date($lang == "en" ? "Y-m-d" : "d-m-Y", strtotime($dateEnd));
        return array($dateInitial_i18n, $dateEnd_i18n);
    }

    private function send_answer_by_email($datos)
    {
        $ci = &get_instance();
        var_dump($datos['status_email']);
        if ($datos['status_email'] == 'confirmed') {

            // Envio de correo al cliente
            $ci->sent_advanced_email($datos['email'], 'Reserva atuhotel.com', 'listing_reserved_client_status'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');


            // Envio de correo a los admin            
            $ci->sent_advanced_email('bookings-noreply@atuhotel.com', 'Reserva atuhotel.com', 'listing_reserved_admin_status'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');


            // Envio de correo a los propietarios
            $ci->sent_advanced_email($datos['owner_email'], 'Reserva atuhotel.com', 'listing_reserved_owner_status'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');
        } elseif ($datos['status_email'] == null) {

            // Envio de correo al cliente
            $ci->sent_advanced_email($datos['email'], 'Reserva atuhotel.com', 'listing_reserved_client_updated'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');

            // Envio de correo a los admin            
            $ci->sent_advanced_email('bookings-noreply@atuhotel.com', 'Reserva atuhotel.com', 'listing_reserved_admin_updated'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');


            // Envio de correo a los propietarios
            $ci->sent_advanced_email($datos['owner_email'], 'Reserva atuhotel.com', 'listing_reserved_owner_updated'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');
        } else {

            // Envio de correo al cliente
            $ci->sent_advanced_email($datos['email'], 'Reserva atuhotel.com', 'listing_reserved_client_status_cancelled'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');


            // Envio de correo a los admin            
            $ci->sent_advanced_email('bookings-noreply@atuhotel.com', 'Reserva atuhotel.com', 'listing_reserved_admin_status'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');

            // Envio de correo a los propietarios
            $ci->sent_advanced_email($datos['owner_email'], 'Reserva atuhotel.com', 'listing_reserved_owner_status'
                , $datos, null, $datos['lang'], 'bookings-noreply@atuhotel.com');

        }


    }

    /**
     * @param $row
     * @param $listing
     * @return mixed
     */
    protected function calculatePrice($row, $listing)
    {
        $this->db->select('date_initial, date_end, period_price_room.price')->from('period_price_room');

        // Devuelve todos los Periodos de la habitacion de la reserva actual actual
        $this->db->join('period_price', 'period_price.id = period_price_room.period_id')
            ->join('listings', 'listings.id = period_price_room.listing_id')->where('period_price_room.listing_id', $row['listing_id'])
            ->where('listings.owner_id', $row['client_id']);

        // Ejecuta la consulta
        $data = $this->get_all_ext();

        $dates = array($row['start_date']);

        // Obtiene el rango de fecha de los periodos
        $start = strtotime($data[0]['date_initial']);
        $end = strtotime($data[0]['date_end']);

        // Itera dentro del rango para saber si esta dentro del rango
        foreach ($dates as $date) {
            $timestamp = strtotime($date);
            if ($timestamp >= $start && $timestamp <= $end) {
                // Si esta multiplica por el precio del periodo
                $row['price'] = $listing['price'] * $data[0]['prices'];

            } else {
                // Si no multiplica por el precio por defecto
                $row['price'] = $listing['price'] * $row['period'];

            }
        }
        return $row;
    }

}
