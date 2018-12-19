<?php


class User_model extends MY_Model
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
        $this->_table = 'users';

        $this->validate = array(
            array('field' => 'email',
                'label' => lang_extension('user_model.email'),
                'rules' => 'required', ),
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

            unset($row['password']);
            $row['image_profile'] = api_url('/files/users/profile_'.$row['id'].'?t='.rand(0, 1000));
            !file_exists('files/users/profile_'.$row['id']) && $row['image_profile'] = api_url('/files/users/profile_default.png'.'?t='.rand(0, 1000));

            $row['image_dni'] = api_url('/files/users/dni_'.$row['id'].'?t='.rand(0, 1000));
            !file_exists('files/users/dni_'.$row['id']) && $row['image_dni'] = null;

            $row['country'] = $this->country_model->get_by('code', $row['country_code']);
            $row['bank_account'] = $this->bank_model->get($row['bank_account_id']);

            $row['cancel_term'] = $this->cancel_term_model->get($row['cancel_term_id']);
            $row['cancel_prev_cost'] = $this->cancel_prev_cost_model->get($row['cancel_prev_cost_id']);
            $row['cancel_total_cost'] = $this->cancel_total_cost_model->get($row['cancel_total_cost_id']);


            $row['role'] == 'owner' && $row['verification_token'] != '' && $row['role'] = 'owner_unverificated';

            if($row['role']=='owner'){
                $row['termns'] = lang_extension('user_model.termns1') . $row['cancel_term']['name'] . 
                                lang_extension('user_model.termns2').$row['cancel_prev_cost']['name'].
                                lang_extension('user_model.termns3'). str_replace('hasta','',$row['cancel_term']['name']) ." ". 
                                lang_extension('user_model.termns4').$row['cancel_total_cost']['name'];
            }
            $row['not_verificated'] = !empty($row['verification_token']);
            $row['is_editable'] = ($row['id'] == $user['id'] && empty($row['verification_token'])) || $user['role'] == 'admin';
            unset($row['verification_token']);

            $row['hotel_resume'] = $row['hotel_resume_'.$ci->config->item('lang')];


        }

        return $row;
    }

    protected function before_create($row)
    {
        $this->db->trans_start();

        if ($this->exists('email', $row['email'])) {
            throw new Exception(lang_extension('user_model.email_error'));
        }

        if (!empty($row['hotel_name']) && $this->exists('hotel_name', $row['hotel_name'])) {
            throw new Exception(lang_extension('user_model.hotel_name_error'));
        }

        $row['password'] = sha1($row['password']);

        if(!isset($row['role'])){
          $row['role'] = 'client';
        }



        $ci = &get_instance();
        $ci->load->helper('string');
        $row['verification_token'] = random_string('alnum', 50) ;

        $row['created_at'] = date('Y-m-d H:i:s');
        $row['updated_at'] = date('Y-m-d H:i:s');

        return $row;
    }

    protected function after_create($row)
    {
        $this->post_image($row[0]['id']);
        $ci = &get_instance();
        /*if (!empty($row[0]['email']) && $row[0]['role'] == 'owner') {
            
        }*/
        $ci->sent_advanced_email($row[0]['email'], lang_extension('user_model.email_register_subject'), 'user_register', $vars = array(
            'first_name' => $row[0]['first_name'],
            'email' => $row[0]['email'],
            'verification_url' => 'http://atuhotel.com/confirmation/'.$row[0]['verification_token'],
        ));

        if($row[0]['role']=='owner'){
            $ci->sent_advanced_email(array($row[0]['email'],'contracting@atuhotel.com'), lang_extension('user_model.email_owner_subject'), 'owner_registred', $vars = array(
                'owner' => $row[0]['first_name']
            ),null,null,'contracting@atuhotel.com');
        }

        $this->db->trans_complete();

        return $row;
    }

    protected function before_update($row)
    {
        $this->db->trans_start();

        $ci = &get_instance();
        $ci->load->helper('string');

        if ($this->exists('email', $row['email'],$row['id'])) {
            throw new Exception(lang_extension('user_model.email_error'));
        }

        if (!empty($row['hotel_name']) && $this->exists('hotel_name', $row['hotel_name'],$row['id'])) {
            throw new Exception(lang_extension('user_model.hotel_name_error'));
        }

        /*if(isset($row['role']) &&  $row['role'] == 'owner' ){
            $row['verification_token'] = random_string('alnum', 50) ;
        }*/
        //$row['verification_token'] = random_string('alnum', 50) ;


        if (!empty($row['password'])) {
            $row['password'] = sha1($row['password']);
        }

        if (!empty($row['role']) && ($row['role'] != 'client' && $row['role'] != 'owner' && $row['role'] != 'admin' ) ) {
            unset($row['role']);
        }

        if (isset($row['remove_profile_img'])) {
            $row['remove_profile_img'] == true && $this->delete_image($row['id'], 'profile');
            unset($row['remove_profile_img']);
        }
        if (isset($row['remove_dni_img'])) {
            $row['remove_dni_img'] == true && $this->delete_image($row['id'], 'dni');
            unset($row['remove_dni_img']);
        }

        $row['updated_at'] = date('Y-m-d H:i:s');

        return $row;
    }

    protected function after_update($row)
    {
        $ci = &get_instance();
        $us = $this->get($row[0]['id']);

        if (!empty($us['email']) && isset($row[0]['role']) && $row[0]['role'] == 'owner' && !empty($row[0]['verification_token'])) {
            $ci->sent_advanced_email(array($us['email'],'contracting@atuhotel.com'), lang_extension('user_model.email_owner_subject'), 'owner_registred', $vars = array(
              'owner' => $us['first_name']
            ),null,null,'contracting@atuhotel.com');
        }

        //Close sales and play sales
        if (isset($row[0]['stop_sales']) && $row[0]['stop_sales'] ==0  ) {
            $ci->sent_advanced_email(array('stopsales@atuhotel.com'), lang_extension('user_model.email_play_sales_subject'), 'playsales', $vars = array(
                'email' => $us['email'],
                'hotel_name'=> $us['hotel_name'],
                'hotel_id' => $us['id']
            ),null,null,'stopsales@atuhotel.com');
        }
        if (isset($row[0]['stop_sales']) && $row[0]['stop_sales'] ==1  ) {
            $ci->sent_advanced_email(array('stopsales@atuhotel.com'), lang_extension('user_model.email_stop_sales_subject'), 'stopsales', $vars = array(
                'email' => $us['email'],
                'hotel_name'=> $us['hotel_name'],
                'hotel_id' => $us['id']
            ),null,null,'stopsales@atuhotel.com');
        }
        //End Close sales and play sales

        $this->post_image($row[0]['id']);

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
        $this->delete_image($row, 'profile');
        $this->delete_image($row, 'dni');
        $this->db->trans_complete();

        return $row;
    }

    /*********End Callback****************/

    /*Save images*/
    protected function post_image($id)
    {
        $ci = &get_instance();
        $ci->load->library('image_manipulator_ci');
        $img_profile = '';
        $img_id = '';

        if (isset($_FILES['img_profile'])) {
            $tmp_name = $_FILES['img_profile']['tmp_name'];
            $manipulator = new ImageManipulator($tmp_name);
            $manipulator->resample(100, 100);
            $manipulator->save('files/users/profile_'.$id);
        }

        if (isset($_FILES['img_dni'])) {
            $tmp_name = $_FILES['img_dni']['tmp_name'];
            $manipulator = new ImageManipulator($tmp_name);
            $manipulator->resample(500, 500);
            $manipulator->save('files/users/dni_'.$id);
        }
    }
    protected function delete_image($id, $type = 'profile')
    {
        file_exists('files/users/'.$type.'_'.$id) && unlink('files/users/'.$type.'_'.$id);
    }

    public function filter($page, $count, $filter = array(), $order = array())
    {
        $ci = &get_instance();
        $us = $ci->login_user();

        $this->db->select("$this->_table.*")->from("$this->_table");

        if (!empty($filter['email'])) {
            $this->db->where('email', $filter['email']);
        }
        if (!empty($filter['country_code'])) {
            $this->db->where('country_code', $filter['country_code']);
        }
        if (!empty($filter['region'])) {
            $this->db->where('region', $filter['region']);
        }
        if (!empty($filter['city'])) {
            $this->db->where('city', $filter['city']);
        }
        if (!empty($filter['file_id_type'])) {
            $this->db->where('file_id_type', $filter['file_id_type']);
        }
        if (!empty($filter['verification_id_status'])) {
            $this->db->where('verification_id_status', $filter['verification_id_status']);
        }
        if (!empty($filter['role'])) {
            $this->db->where('role', $filter['role']);
        }

        if (!empty($filter['name'])) {
            $this->db->group_start()
                        ->like('concat(first_name,last_name)', $filter['name'])
                        ->or_like('first_name', $filter['name'])
                        ->or_like('last_name', $filter['name'])
                      ->group_end();
        }

        $total = $this->count_ext();
        if (!is_array($order) || empty($order)) {
            $this->db->order_by('created_at', 'desc');
        } else {
            $this->db->order_by('created_at', 'desc');
        }

        $this->db->limit($count, ($page - 1) * $count);
        $data = $this->get_all_ext();

        return array('total' => $total, 'results' => $data);
    }
}
