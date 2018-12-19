<?php


class Listing_stop_sale_model extends MY_Model
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

    public function __construct()
    {
        parent::__construct();
        $this->_table = 'listing_stop_sales';

        $this->validate = array();
    }

    /*************Callback******************/
    protected function before_get()
    {
    }

    protected function after_get($row)
    {
        if (isset($row)) {
            $CI = &get_instance();
            $user = $CI->login_user(true);
            $row['client_id'] = $user['id'];
            $row['user'] = $this->user_model->get($row['user_id']);

        }

        return $row;
    }

    protected function before_create($row)
    {
        $this->db->trans_start();

        $ci = &get_instance();
        $user = $ci->login_user(true);
        $row['user_id'] = $user['id'];

        return $row;
    }

    protected function after_create($row)
    {
        $ci = &get_instance();
        $us = $this->user_model->get($row[0]['user_id']);

        $ci->sent_advanced_email(array('stopsales@atuhotel.com'), lang_extension('user_model.email_play_sales_subject'), 'stopsales', $vars = array(
            'email' => $us['email'],
            'hotel_name' => $us['hotel_name'],
            'hotel_id' => $us['id'],
            'range_date' => lang_extension('from') . format_date($row[0]['start_date']) . lang_extension('to') . format_date($row[0]['end_date'])
        ), null, null, 'stopsales@atuhotel.com');

        $this->db->trans_complete();
        return $row;
    }

    protected function before_update($row)
    {
        $this->db->trans_start();

        return $row;
    }

    protected function after_update($row)
    {

        $this->db->trans_complete();

        return $row;
    }

    protected function before_delete($row)
    {
        $this->db->trans_start();
        $ci = &get_instance();
        $stop_sale = $this->get($row);
        $us = $this->user_model->get($stop_sale['user_id']);

        $ci->sent_advanced_email(array('stopsales@atuhotel.com'), lang_extension('user_model.email_stop_sales_subject'), 'playsales', $vars = array(
            'email' => $us['email'],
            'hotel_name' => $us['hotel_name'],
            'hotel_id' => $us['id'],
            'range_date' => lang_extension('from') . format_date($stop_sale['start_date']) . lang_extension('to') . format_date($stop_sale['end_date'])
        ), null, null, 'stopsales@atuhotel.com');


        return $row;
    }

    protected function after_delete($row)
    {


        $this->db->trans_complete();

        return $row;
    }

    /*********End Callback****************/

    public function filter($page, $count, $filter = array(), $order = array())
    {
        $this->db->select($this->_table . '.*')->from($this->_table);

        if (!empty($filter['user_id'])) {
            $this->db->where('user_id', $filter['user_id']);
        }

        if (!empty($filter['start_date'])) {
            $this->db->where('substring(start_date,1,10) <=', $filter['start_date']);
        }

        if (!empty($filter['end_date'])) {
            $this->db->where('substring(end_date,1,10) >=', $filter['end_date']);
        }

        $total = $this->count_ext();

        $this->db->limit($count, ($page - 1) * $count);

        $data = $this->get_all_ext();

        return array('total' => $total, 'results' => $data);
    }

    //Yeiniel
    public function findAll($filter = array())
    {
        $end = "date('". $filter['end_date']."')";
        $start = "date('". $filter['start_date']."')";

        $cond1 = "(start_date >= ".$start." and start_date <= ".$end.")"; //condition 1
        $cond2 = "(start_date <= ".$start." and end_date >= ".$end.")"; //condition 2
        $cond3 = "(end_date >= ".$start." and end_date <= ".$end.")"; //condition 3

        $where = "user_id = '".$filter['user_id']."' and (". $cond1." or ". $cond2. " or ".$cond3. ")";

        $data = $this->db->select($this->_table . '.*')->from($this->_table)->where($where)->get()->result_array();

        return array('results' => $data);
    }

    //Yeiniel
    public function findAllForDelete($filter = array())
    {
        $end = "date('". $filter['end_date']."')";
        $start = "date('". $filter['start_date']."')";

        $cond1 = "start_date >= ".$start." and end_date <= ".$end;
        $where = "user_id = '".$filter['user_id']."' and ". $cond1;

        $data = $this->db->select($this->_table . '.*')->from($this->_table)->where($where)->get()->result_array();

        return array('results' => $data);
    }
}
