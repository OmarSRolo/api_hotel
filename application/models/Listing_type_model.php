<?php


class Listing_type_model extends MY_Model
{
    public $before_get = array('before_get');
    public $after_get = array('after_get');

    public $before_create = array('before_create');
    public $after_create = array('after_create');

    public $before_update = array('before_update');
    public $after_update = array('after_update');

    public $before_delete = array('before_delete');
    public $after_delete = array('after_delete');


    public function __construct()
    {
        parent::__construct();
        $this->_table = 'listing_types';
    }


    /*************Callback******************/
    protected function before_get()
    {
    }

    protected function after_get($row)
    {
        if (isset($row)) {
            $ci = &get_instance();
            $row['name'] = $row['name_' . $ci->config->item('lang')];
        }

        return $row;
    }

    protected function before_create($row)
    {
        $this->db->trans_start();

        return $row;
    }

    protected function after_create($row)
    {
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

        return $row;
    }

    protected function after_delete($row)
    {
        $this->db->trans_complete();

        return $row;
    }

    /*********End Callback****************/
}
