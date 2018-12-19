<?php


class Listing_segment_model extends MY_Model
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
        $this->_table = 'listing_segment';
    }

    /*************Callback******************/
    protected function before_get()
    {
    }

    protected function after_get($row)
    {
        return $row;
    }

    protected function before_create($row)
    {
        $this->db->trans_start();
        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');

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

        $row['updated_at'] = date('Y-m-d H:i:s');

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

    public function filter($page, $count, $filter = array(), $order = array())
    {
        $this->db->select("*")->from($this->_table);
        $total = $this->count_ext();

        $this->db->limit($count, ($page - 1) * $count);

        $data = $this->get_all_ext();

        return array('total' => $total, 'results' => $data);
    }
    /*********End Callback****************/
}
