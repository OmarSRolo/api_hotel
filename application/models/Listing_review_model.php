<?php


class Listing_review_model extends MY_Model
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
        $this->_table = 'listing_reviews';

        $this->validate = array(
            array('field' => 'rating',
                'label' => lang_extension('listing_rating_model.rating'),
                'rules' => 'required' ),
        );
    }

    /*************Callback******************/
    protected function before_get()
    {
    }

    protected function after_get($row)
    {
        if (isset($row)) {
            $ci = &get_instance();
            $user = $ci->login_user();

            $row['user'] = $this->user_model->get($row['user_id']);
            // $row['listing'] = $this->listing_model->get($row['listing_id']);

            $row['created_at'] = $row['created_at'];

            $row['is_editable'] = ($row['user_id'] == $user['id']) || $user['role'] == 'admin';
        }

        return $row;
    }

    protected function before_create($row)
    {
        $this->db->trans_start();

        $ci = &get_instance();
        $user = $ci->login_user();
        $row['user_id'] = $user['id'];
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

    /*********End Callback****************/
}
