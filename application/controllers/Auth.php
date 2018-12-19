<?php


class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        try {
            $user = $this->user_model->get_by(array('email' => $this->input->post('email'), 'password' => sha1($this->input->post('password'))));

            if ($user != null) {
                $user['iat'] = time();
                $user['exp'] = time() + 600;
                $jwt = JWT::encode($user, '');
                $this->json_result(true, '', $jwt);
            } else {
                $this->json_result(false, '');
            }
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function register()
    {
        try {
            $data = array();
            $this->input->post('email') != null && $data['email'] = $this->input->post('email');
            $this->input->post('password') != null && $data['password'] = $this->input->post('password');
            $this->input->post('first_name') != null && $data['first_name'] = $this->input->post('first_name');
            $this->input->post('last_name') != null && $data['last_name'] = $this->input->post('last_name');
            $this->input->post('hotel_name') != null && $data['hotel_name'] = $this->input->post('hotel_name');
            $this->input->post('hotel_id') != null && $data['hotel_id'] = $this->input->post('hotel_id');
            

            //Get Country by IP
            $ip = $this->input->ip_address();

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, 'http://freegeoip.net/json/' . $ip);
            $result = curl_exec($ch);
            curl_close($ch);
            $location = json_decode($result, true);

            if (is_array($location)) {
                $data['country_code'] = $location['country_code'];
                $data['region'] = $location['region_name'];
                $data['city'] = $location['city'];
                $data['zip_code'] = $location['zip_code'];
                $data['time_zone'] = $location['time_zone'];
                $data['latitude'] = $location['latitude'];
                $data['longitude'] = $location['longitude'];
            }

            $this->input->post('role') != null && $this->input->post('role') != 'admin' && $data['role'] = $this->input->post('role');

            $this->user_model->add_validation(array('field' => 'password', 'label' => lang_extension('user_model.password'), 'rules' => 'required|min_length[7]'));

            $r = $this->user_model->insert($data);

            if ($r) {
                if ($data['role'] != 'owner') {
                    $user = $this->user_model->get($r);
                    $user['iat'] = time();
                    $user['exp'] = time() + 600;
                    $jwt = JWT::encode($user, '');
                    $this->json_result(true, '', $jwt);
                } else {
                    $this->json_result(true, '');
                }
            } else {
                $this->json_result(false, validation_errors());
            }
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function confirmation($id)
    {
        try {
            $user = $this->user_model->get_by('verification_token', $id);

            if ($user != null) {
                $user['verification_token'] = '';

                $r = $this->user_model->update($user['id'], array('verification_token' => '', 'email' => $user['email']));
                if ($r) {
                    $user['iat'] = time();
                    $user['exp'] = time() + 600;
                    $user['role'] = 'owner';
                    $jwt = JWT::encode($user, '');
                    $this->json_result($r, validation_errors(), $jwt);
                } else {
                    $this->json_result($r, validation_errors());
                }
            } else {
                throw new Exception(lang_extension('user_model.verification_token_invalid'));
            }
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function login_social()
    {
        try {
            if ($this->input->post('uid') == null || $this->input->post('provider') == null) {
                throw new Exception(lang_extension('auth.social_error_security'));
            }

            $user = $this->user_model->get_by('email', $this->input->post('email'));

            if ($user) {
                $user = $this->user_model->get_by($this->input->post('provider'), $this->input->post('uid'));

                if (!$user) {
                    $user = $this->user_model->get_by('email', $this->input->post('email'));
                    $data['email'] = $user['email'];
                    $data[$this->input->post('provider')] = $this->input->post('uid');

                    $r = $this->user_model->update($user['id'], $data);
                }

                if ($user && !empty($user['verification_token'])) {
                    throw new Exception(lang_extension('user_model.not_verificated'));
                }

                if ($user != null) {
                    $user['iat'] = time();
                    $user['exp'] = time() + 600;
                    $jwt = JWT::encode($user, '');
                    $this->json_result(true, '', $jwt);
                }
            } else {
                $data = array();
                $this->input->post('email') != null && $data['email'] = $this->input->post('email');
                $this->load->helper('string');
                $data['password'] = random_string('alpha');
                $data[$this->input->post('provider')] = $this->input->post('uid');
                $name = $this->input->post('name') ? $this->input->post('name') : '';
                $name_split = explode(' ', $name);
                $data['first_name'] = count($name_split) > 0 ? $name_split[0] : '';
                $data['last_name'] = str_replace($data['first_name'], '', $name);

                //Get Country by IP
                $ip = $this->input->ip_address();
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_URL, 'http://freegeoip.net/json/' . $ip);
                $result = curl_exec($ch);
                curl_close($ch);
                $location = json_decode($result, true);

                if (is_array($location)) {
                    $data['country_code'] = $location['country_code'];
                    $data['region'] = $location['region_name'];
                    $data['city'] = $location['city'];
                    $data['zip_code'] = $location['zip_code'];
                    $data['time_zone'] = $location['time_zone'];
                    $data['latitude'] = $location['latitude'];
                    $data['longitude'] = $location['longitude'];
                }

                $r = $this->user_model->insert($data);

                if ($r) {
                    $user = $this->user_model->get($r);
                    $user['iat'] = time();
                    $user['exp'] = time() + 600;
                    $jwt = JWT::encode($user, '');
                    $this->json_result(true, lang_extension('user_model.register_social'), $jwt);
                }
            }
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $this->check_authenticated();
            $us = $this->login_user();
            //$us = $this->user_model->get(1);
            $data = array();

            $this->input->post('email') != null && $data['email'] = $this->input->post('email');
            $this->input->post('password') != null && $data['password'] = $this->input->post('password');
            $this->input->post('dni') != null && $data['dni'] = $this->input->post('dni');
            $this->input->post('hotel_name') != null && $data['hotel_name'] = $this->input->post('hotel_name');
            $this->input->post('hotel_id') != null && $data['hotel_id'] = $this->input->post('hotel_id');
            $this->input->post('hotel_rating') != null && $data['hotel_rating'] = $this->input->post('hotel_rating');
            $this->input->post('first_name') != null && $data['first_name'] = $this->input->post('first_name');
            $this->input->post('hotel_resume_es') != null && $data['hotel_resume_es'] = $this->input->post('hotel_resume_es');
            $this->input->post('last_name') != null && $data['last_name'] = $this->input->post('last_name');
            $this->input->post('phone') != null && $data['phone'] = $this->input->post('phone');
            $this->input->post('phone_emergency') != null && $data['phone_emergency'] = $this->input->post('phone_emergency');
            $this->input->post('country_code') != null && $data['country_code'] = $this->input->post('country_code');
            $this->input->post('region') != null && $data['region'] = $this->input->post('region');
            $this->input->post('city') != null && $data['city'] = $this->input->post('city');
            $this->input->post('zip_code') != null && $data['zip_code'] = $this->input->post('zip_code');
            $this->input->post('time_zone') != null && $data['time_zone'] = $this->input->post('time_zone');
            $this->input->post('latitude') != null && $data['latitude'] = $this->input->post('latitude');
            $this->input->post('longitude') != null && $data['longitude'] = $this->input->post('longitude');
            $this->input->post('about_me') != null && $data['about_me'] = $this->input->post('about_me');
            $this->input->post('file_id_type') != null && $data['file_id_type'] = $this->input->post('file_id_type');

            $this->input->post('cancel_term_id') != null && $data['cancel_term_id'] = $this->input->post('cancel_term_id');
            $this->input->post('cancel_prev_cost_id') != null && $data['cancel_prev_cost_id'] = $this->input->post('cancel_prev_cost_id');
            $this->input->post('cancel_total_cost_id') != null && $data['cancel_total_cost_id'] = $this->input->post('cancel_total_cost_id');

            $this->input->post('bank_account_id') != null && $data['bank_account_id'] = $this->input->post('bank_account_id');
            $this->input->post('bank_account_number') != null && $data['bank_account_number'] = $this->input->post('bank_account_number');

            ($this->input->post('stop_sales') != null && $this->input->post('edit_stop_sales') != null) && $data['stop_sales'] = $this->input->post('stop_sales');

            $this->input->post('role') != null && $this->input->post('role') != '' && $data['role'] = $this->input->post('role');

            if($data['role'] == 'owner')
            {
              $data['verification_id_status'] = 'waiting';
            }     


            $this->input->post('remove_profile_img') != null && $data['remove_profile_img'] = $this->input->post('remove_profile_img');
            $this->input->post('remove_dni_img') != null && $data['remove_dni_img'] = $this->input->post('remove_dni_img');

            if ($this->input->post('password') != null) {
                $this->user_model->add_validation(array('field' => 'password', 'label' => lang_extension('user_model.password'), 'rules' => 'required|min_length[7]'));
            }

            $r = $this->user_model->update($us['id'], $data);
            if ($r) {
                $user = $this->login_user(true);

                $user['iat'] = time();
                $user['exp'] = time() + 600;
                $jwt = JWT::encode($user, '');
                $this->json_result(true, '', $jwt);
            } else {
                $this->json_result(false, validation_errors());
            }
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function get()
    {
        $us = $this->login_user();
        $user = $this->user_model->get($us['id']);
        $this->json_result($user != false, '', $user);
    }

    public function password()
    {
        try {
            $user = $this->user_model->get_by(array('email' => $this->input->post('email')));

            if ($user) {
                $this->load->helper('string');
                $new_password = random_string('alpha');
                $this->user_model->update($user['id'], array(
                    'password' => $new_password,
                ), true);
                $this->sent_advanced_email($user['email'], lang_extension('user_model.mail_recover_password_subject'), 'user_recover_password', array(
                    'first_name' => $user['first_name'],
                    'email' => $user['email'],
                    'new_password' => $new_password,
                ));
                $this->json_result(true, '');
            } else {
                $this->json_result(false, lang_extension('auth_ctr.user_empty'));
            }
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function contact_us()
    {
        try {
            $to = 'hotelsupport@atuhotel.com';//'hotelsupport@atuhotel.com';//$this->input->get_post('role') == 'owner' ? 'propertiessupport@atuhotel.com':'bookingsupport@atuhotel.com';
            $this->sent_advanced_email($to, lang_extension('contact_subject'), 'contact', array(
                'first_name' => $this->input->get_post('first_name'),
                'last_name' => $this->input->get_post('last_name'),
                'reserve_id'=> $this->input->get_post('reserve_id'),
                'category'=> lang_extension('contact_category_'.$this->input->get_post('category')),
                'has_reserve'=>$this->input->get_post('has_reserve') == 'true' ? lang_extension('has_reserve'): lang_extension('has_not_reserve'),
                'email' => $this->input->get_post('email'),
                'subject' => $this->input->get_post('subject'),
                'content' => $this->input->get_post('content'),
            ), array(), 'es');
            $this->json_result(true, '');
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }
}
