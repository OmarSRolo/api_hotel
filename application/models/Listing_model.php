<?php


class Listing_model extends MY_Model

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

        $this->_table = 'listings';


        $this->validate = array(

            array('field' => 'listing_type_id',

                'label' => lang_extension('listing_model.listing_type_id'),

                'rules' => 'required',)

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


            $row['owner'] = $this->user_model->get($row['owner_id']);

            $row['listing_type'] = $this->listing_type_model->get($row['listing_type_id']);


            $row['country'] = $this->country_model->get_by('code', $row['country_code']);


            $row['images'] = $this->listing_image_model->get_many_by('listing_id', $row['id']);


            $row['stopped_sales_dates'] = $this->listing_stop_sale_model->get_many_by("user_id = " . $row['owner_id'] . " and end_date >= CURDATE()");


            $row['image_primary'] = $this->listing_image_model->get_by(array('listing_id' => $row['id'], 'is_primary' => 1));

            !isset($row['image_primary']['id']) && $row['image_primary'] = array('path' => api_url('files/listings/default.png'));


            $row['services'] = $this->db->query('select s.*,' . 'name_' . $ci->config->item('lang') . ' as name from services s inner join listing_services l on l.service_id = s.id where l.listing_id = ' . $row['id'])->result_array();


            $row['rating'] = $this->db->query('select avg(r1.rating) as rating from listing_reviews r1 inner join listings r2 on r1.listing_id = r2.id  where r1.listing_id = ' . $row['id'])->row_array()['rating'];

            !isset($row['rating']) && $row['rating'] = 0;


            $row['reviews_total'] = $this->listing_review_model->count_by('listing_id', $row['id']);


            $row['comments_total'] = $this->listing_comment_model->count_by('listing_id', $row['id']);


            $row['is_verificated'] = $row['owner']['verification_id_status'] == 'approved';

            $row['description'] = $row['description_' . $ci->config->item('lang')];


            $row['price'] = $row['listing_type']['price'];


            $row['created_at'] = $row['created_at'];

            $row['is_editable'] = ($row['owner_id'] == $user['id']) || $user['role'] == 'admin';

        }

        return $row;

    }


    protected function before_create($row)

    {

        $this->db->trans_start();


        $ci = &get_instance();

        $user = $ci->login_user();

        $row['owner_id'] = $user['id'];

        $row['status'] = 'draft';


        $row['created_at'] = date('Y-m-d H:i:s');

        $row['updated_at'] = date('Y-m-d H:i:s');

        if (isset($row['images'])) {

            $this->tmp_images = $row['images'];

            unset($row['images']);

        }

        if (isset($row['services'])) {

            $this->tmp_services = $row['services'];

            unset($row['services']);

        }
        return $row;

    }


    protected function after_create($row)

    {
        $this->update_images($row);
        $this->update_services($row);
        $ci = &get_instance();

        $listing = $this->get($row[0]['id']);

        $ci->sent_advanced_email('contracting@atuhotel.com', lang_extension('listing_model.email_listing_cretaed_subject'), 'listing_created_admin', array(

            'owner' => $listing['owner']['first_name'] . ' ' . $listing['owner']['last_name'],

            'hotel_name' => $listing['owner']['hotel_name'],

            'listing_id' => $listing['id'],

            'listing_location' => $listing['country']['name'] . ',' . $listing['city'],

            'listing_url' => 'http://atuhotel.com/listings/details/' . $listing['id'],

        ), array(), 'es', 'contracting@atuhotel.com');

        $this->db->trans_complete();

        return $row;

    }


    protected function before_update($row)

    {

        $this->db->trans_start();


        if (isset($row['prices'])) {

            $this->tmp_prices = $row['prices'];

            unset($row['prices']);

        }

        if (isset($row['images'])) {

            $this->tmp_images = $row['images'];

            unset($row['images']);

        }

        if (isset($row['services'])) {

            $this->tmp_services = $row['services'];

            unset($row['services']);

        }


        $row['updated_at'] = date('Y-m-d H:i:s');


        return $row;

    }


    protected function after_update($row)

    {

        //Images

        $this->update_images($row);


        //Services

        $this->update_services($row);


        //Prices

        //$this->update_prices($row);


        $this->db->trans_complete();


        return $row;

    }


    protected function before_delete($row)

    {

        $this->db->trans_start();

        get_instance()->load->helper('directory');

        rrmdir('files/listings/' . $row);


        return $row;

    }


    protected function after_delete($row)

    {

        $this->db->trans_complete();


        return $row;

    }


    /*********End Callback****************/

    protected function update_images($row)

    {

        //$image_files = $this->pretty_files($_FILES['images']);

        //var_dump($_FILES['images']);

        if (isset($this->tmp_images)) {

            $ci = &get_instance();

            $ci->load->library('image_manipulator_ci');


            $ids = implode(',', $this->items_by_key('id', $this->tmp_images));

            $ids_where = !empty($ids) ? 'and id not in (' . $ids . ')' : '';

            $to_remove = $this->db->query('select * from listing_images where listing_id = ? ' . $ids_where, array($row[0]['id']))->result_array();


            foreach ($to_remove as $r) {

                file_exists($r['path']) && unlink($r['path']);

                $this->listing_image_model->delete($r['id']);

            }


            $image_index = 0;

            foreach ($this->tmp_images as $image) {

                if (isset($image['id'])) {

                    $this->listing_image_model->update($image['id'], array(

                        'caption' => $image['caption'],

                        'is_primary' => $image['is_primary'],

                    ));

                } else {

                    $file = $this->get_image_file($image_index);


                    $manipulator = new ImageManipulator($file['tmp_name']);

                    $manipulator->resample_after_check(1024, 1024);

                    $manipulator->save('files/listings/' . $row[0]['id'] . '/' . $file['name']);

                    $this->listing_image_model->insert(array(

                        'listing_id' => $row[0]['id'],

                        'caption' => $image['caption'],

                        'is_primary' => $image['is_primary'],

                        'path' => 'files/listings/' . $row[0]['id'] . '/' . $file['name'],

                    ));

                }

                ++$image_index;

            }

        }

    }


    private function get_image_file($index = 0, $file_post = 'images')

    {

        $file_ary = array();

        $file = $_FILES[$file_post];

        if (isset($file)) {

            //$file_count = count($file['name']);


            $file_key = array_keys($file);


            foreach ($file_key as $val) {

                $file_ary[$val] = $file[$val][$index]['file'];

            }


            return $file_ary;

        }


        return 0;

    }


    private function update_services($row)

    {

        if (isset($this->tmp_services)) {

            $this->db->query('delete from listing_services where listing_id = ?', array($row[0]['id']));


            foreach ($this->tmp_services as $id => $service) {

                if ($service == 'true') {

                    $this->listing_service_model->insert(array(

                        'listing_id' => $row[0]['id'],

                        'service_id' => $id,

                    ));

                }

            }

        }

    }


    public function filter($page, $count, $filter = array(), $order = array())

    {

        $this->db->select('listings.*')
            ->from($this->_table)->join('users', 'users.id = listings.owner_id')->where('users.verification_id_status', 'approved');

        if (!empty($filter['owner_id'])) {

            $this->db->where('owner_id', $filter['owner_id']);

        }


        if (!empty($filter['hotel_name'])) {

            $this->db->like('users.hotel_name', $filter['hotel_name']);

        }


        if (!empty($filter['country_code'])) {

            $this->db->where('listings.country_code', $filter['country_code']);

        }


        if (!empty($filter['listing_type_id'])) {

            $this->db->where('listing_type_id', $filter['listing_type_id']);

        }

        if (!empty($filter['hotel_rating'])) {

            $this->db->where('hotel_rating', $filter['hotel_rating']);

        }


        if (!empty($filter['capacity'])) {

            if ($filter['capacity'] == 5) {

                $this->db->where('capacity >=', $filter['capacity']);

            } else {

                $this->db->where('capacity', $filter['capacity']);

            }

        }


        if (!empty($filter['city'])) {

            $this->db->like('listings.city', $filter['city']);

        }


        if (!empty($filter['hotel_name'])) {

            $this->db->like('hotel_name', $filter['hotel_name']);

        }


        if (!empty($filter['created_at'])) {

            $this->db->where('substring(created_at,1,10) <=', $filter['created_at']);

        }
        if (!empty($filter['start_date'])) {
            $this->db->join("listing_reserves", "listings.id = listing_reserves.listing_id", 'left');

            if (!empty($filter['end_date'])) {

                $this->db->where("listings.id NOT IN (SELECT DISTINCT
            listing_reserves.listing_id
            FROM
            listing_reserves
            WHERE listing_reserves.status = 'confirmed' AND
            listing_reserves.start_date <= '" . $filter['start_date'] . "' AND
            listing_reserves.end_date >= '" . $filter['start_date'] . "'" .
                    " OR listing_reserves.start_date <= '" . $filter['end_date'] . "' AND
            listing_reserves.end_date >= '" . $filter['end_date'] . "')");

            } else {

                $this->db->where("listings.id NOT IN (SELECT DISTINCT
            listing_reserves.listing_id
            FROM
            listing_reserves
            WHERE
            listing_reserves.status = 'confirmed' AND 
            listing_reserves.start_date <= '" . $filter['start_date'] . "' AND
            listing_reserves.end_date >= '" . $filter['start_date'] . "')"
                );

            }
        }


        if (empty($filter['start_date']) && !empty($filter['end_date'])) {
            $this->db->join("listing_reserves", "listings.id = listing_reserves.listing_id", 'left');

            $this->db->where("listings.id NOT IN (SELECT DISTINCT
                listing_reserves.listing_id
                FROM
                listing_reserves
                WHERE listing_reserves.status = 'confirmed' AND
                listing_reserves.start_date <= '" . $filter['end_date'] . "' AND
                listing_reserves.end_date >= '" . $filter['end_date'] . "')"
            );


        }


        $this->db->group_by('listings.id');


        if (!empty($filter['created_at'])) {

            $this->db->where('substring(created_at,1,10)', $filter['created_at']);

        }


        if (!empty($filter['owner_email'])) {

            $this->db->like('users.email', $filter['owner_email']);

        }


        if (!empty($filter['services'])) {

            $services = json_decode($filter['services'], true);

            $i = '0';

            $include = false;

            foreach ($services as $key => $value) {

                if ($value == true) {

                    $i .= ',' . $key;

                    $include = true;

                }

            }

            if ($include) {

                $this->db->join('listing_services s', 's.listing_id = listings.id')->where("s.service_id in ($i)");

            }

        }


        if (!empty($order)) {


            $order = json_decode($order, true);

            if ($order['field'] == 'price') {

                $this->db->order_by('price', $order['direction']);
            } elseif ($order['field'] == 'rating') {
                $this->db->select('avg(listing_reviews.rating)', 'rating')->join('listing_reviews', 'listing_reviews.listing_id = listings.id', 'left')->group_by('listings.id');
                $this->db->order_by('rating', $order['direction']);
            } else {
                $this->db->order_by($order['field'], $order['direction']);
            }

        }


        $total = $this->count_ext();
        $this->db->limit($count, ($page - 1) * $count);
        $data = $this->get_all_ext();
        return array('total' => $total, 'results' => $data);

    }

    // Query que obtiene los listings por usuario
    public function filterByUser($page, $count, $filter = array(), $order = array())
    {
        $ci = &get_instance();
        $user = $ci->login_user();

        $this->db->select('*');

        $this->db->from($this->_table)
            ->join('users', 'users.id = listings.owner_id')
            ->join('listing_types', 'listing_types.id = listings.listing_type_id')
            ->join('countries', 'listings.country_code = countries.code')
            ->where('users.verification_id_status', 'approved');
        $this->db->where('owner_id', $user["id"]);

        $total = $this->count_ext();
        $this->db->limit($count, ($page - 1) * $count);
        $data = $this->db->get()->result_array();
        return array('total' => $total, 'results' => $data);
    }


}

