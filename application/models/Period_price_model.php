<?php

/**
 * Class Period_price_model
 */
class Period_price_model extends MY_Model
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
     * Period_price_model constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->_table = 'period_price';
        $this->load->helper('date');
        $data = explode('api/', base_url());
        $this->My_Site_Url = $data[0];
        $this->validate = array();
    }

    /**
     *
     */
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

    public function findAll($filter = array())
    {
        $end = "date('". $filter['date_end']."')";
        $start = "date('". $filter['date_initial']."')";

        $cond1 = "(date_initial >= ".$start." and date_initial <= ".$end.")"; //condition 1
        $cond2 = "(date_initial <= ".$start." and date_end >= ".$end.")"; //condition 2
        $cond3 = "(date_end >= ".$start." and date_end <= ".$end.")"; //condition 3

        $where = "owner_id = '".$filter['owner_id']."' and (". $cond1." or ". $cond2. " or ".$cond3. ")";

        return $this->db->select($this->_table . '.*')->from($this->_table)->where($where)->get()->result_array();
    }

    public function findOnlyRoomByUser($idUser, $lang){

        $result_array = $this->db->distinct()
            ->select('listings.id as id, ' .
                'countries.name_' . $lang . ' as country,
                 city, 
                 listings.price AS price_default,

                 listing_types.name_' . $lang . ' AS type')
            ->from('listings')
            ->join('listing_types', 'listing_types.id = listings.listing_type_id')
            ->join('countries', 'countries.code = listings.country_code')
            ->where('listings.owner_id', $idUser)->get()->result_array();

        return $result_array;
    }

    public function queryPeriodByRoom($idRoom){

        return $this->db->distinct()
            ->select('date_initial, date_end, price')
            ->from('period_price')
            ->join('period_price_room', 'period_price_room.period_id = period_price.id')
            ->where('listing_id=' . $idRoom. " and date_initial >= date('".date('Y-m-d')."')")->get()->result_array();
    }

    public function queryPrice($idRoom, $date){

        $d = "date('". $date."')";

        return $this->db->distinct()
            ->select('price')
            ->from('period_price')
            ->join('period_price_room', 'period_price_room.period_id = period_price.id')
            ->where('listing_id=' . $idRoom. " and ".$d." between date_initial and date_end")->get()->result_array();
    }
}


