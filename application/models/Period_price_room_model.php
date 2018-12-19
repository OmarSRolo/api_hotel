<?php

class Period_price_room_model extends MY_Model
{
    /**
     * @var array
     */
    public $before_get = array('before_get');
    /**
     * @var array
     */
    public $after_get = array('after_get');

    /**
     * @var array
     */
    public $before_create = array('before_create');
    /**
     * @var array
     */
    public $after_create = array('after_create');

    /**
     * @var array
     */
    public $before_update = array('before_update');
    /**
     * @var array
     */
    public $after_update = array('after_update');

    /**
     * @var array
     */
    public $before_delete = array('before_delete');
    /**
     * @var array
     */
    public $after_delete = array('after_delete');

    /**
     * @var array
     */
    public $temps = array();

    /**
     * @var
     */
    public $My_Site_Url;

    /**
     * Period_price_room_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'period_price_room';
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
    /**
     * @param $row
     * @return mixed
     */
    protected function after_get($row)
    {
        if (isset($row)) {

            $row['period_price'] = $this->db->query("select * from period_price where id='" . $row['period_id'] . "'")->row_array();

        }

        return $row;
    }

    // Trigger que se ejecuta despues de crear un objeto
    /**
     * @param $row
     * @return mixed
     */
    protected function before_create($row)
    {
        $this->db->trans_start();
        $ci =& get_instance();
        $ci->login_user(true);

        return $row;
    }

    /**
     * @param $row
     * @return mixed
     */
    protected function after_create($row)
    {

        $this->db->trans_complete();

        return $row;
    }

    // Trigger que se ejecuta antes de actualizar
    /**
     * @param $row
     * @return mixed
     */
    protected function before_update($row)
    {
        return $row;
    }


    // Trigger que se ejecuta luego de actualizar
    /**
     * @param $row
     * @return mixed
     */
    protected function after_update($row)
    {
        return $row;
    }

    /**
     * @param $row
     * @return mixed
     */
    protected function before_delete($row)
    {
        $this->db->trans_start();

        return $row;
    }

    /**
     * @param $row
     * @return mixed
     */
    protected function after_delete($row)
    {
        $this->db->trans_complete();

        return $row;
    }

    /*********End Callback****************/

    /**
     * @param $id_period
     * @param $prices
     * @param $listing_id
     */
    public function insertAllRoom($id_period, $prices, $listing_id)
    {
        $data = array(
            'period_id' => $id_period,
            'price' => $prices,
            'listing_id' => $listing_id
        );

        return $this->db->insert($this->_table, $data);
    }

    public function getAllByIdPeriod($id, $lang)
    {
        return $this->db->distinct()
            ->select('period_price_room.id,'.
                'countries.name_'.$lang.' as country,
                            city, 
                            listings.price AS price_default,
                            period_price_room.price,
                            listing_types.name_'.$lang.' AS type')
            ->from('period_price_room')
            ->join('listings', 'listings.id = period_price_room.listing_id')
            ->join('listing_types', 'listing_types.id = listings.listing_type_id')
            ->join('countries', 'countries.code = listings.country_code')
            ->where('period_id=' . $id)->get()->result_array();
    }

    public function delete_by_idPeriod($idPeriod){
        return $this->db->delete('period_price_room', array('period_id' => $idPeriod));
    }
}


