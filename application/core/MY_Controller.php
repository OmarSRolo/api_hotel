<?php

class MY_Controller extends CI_Controller
{
    public $is_public;

    public function __construct()
    {
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            exit(0);
        }
        parent::__construct();
        $this->is_public = false;
        $this->token_header = 'Ax-Auth'; //'Ax-Auth';
        $this->lang_header = 'Lang';


        /***Autoload for language**/
        $this->load_lang($this->input->get_request_header($this->lang_header) ? $this->input->get_request_header($this->lang_header) : $this->input->get('lang'));
        /***End autoload for language**/
    }

    protected function load_lang($lang = 'en')
    {
        switch ($lang) {
            case 'en':
                $this->config->set_item('language', 'english');
                $this->lang->load('app', 'english');
                $this->config->set_item('lang', 'en');
                break;
            case 'es':
                $this->config->set_item('language', 'spanish');
                $this->lang->load('app', 'spanish');
                $this->config->set_item('lang', 'es');
                break;
            case 'fr':
                $this->config->set_item('language', 'french');
                $this->lang->load('app', 'french');
                $this->config->set_item('lang', 'fr');
                break;
            default:
                $this->config->set_item('language', 'spanish');
                $this->lang->load('app', 'spanish');
                $this->config->set_item('lang', 'es');
                break;
        }
    }

    public function login_user($from_db = false)
    {
        $user = null;
        
        if ($this->input->get_request_header($this->token_header)) {
            try {
                $token = explode(' ', $this->input->get_request_header($this->token_header, TRUE));
                
                if (count($token) > 1) {
                    $payload = JWT::decode(trim($token[1], '"'));
                    $user = $payload;

                    if (is_object($user)) {
                        $a = array();
                        foreach (get_object_vars($user) as $f => $v) {
                            $a[$f] = $v;
                        }
                        $user = $a;
                    }
                }
            } catch (UnexpectedValueException $e) {
            }
        }

        if ($from_db && isset($user)) {
            $user = $this->user_model->get($user['id']);
        }

        return empty($user) ? false : $user;
    }

    public function json_result($result, $message, $data = array())
    {
        $r['complete'] = $result;
        $r['message'] = $message;
        $r['data'] = $data;

        if ($this->input->get_request_header($this->token_header)) {
            $jwt = '';
            try {
                $token = explode(' ', $this->input->get_request_header($this->token_header));
                $payload = null;
                isset($token[1]) && $payload = JWT::decode(trim($token[1], '"'));

                is_object($payload) && $payload->iat = time();
                is_object($payload) && $payload->exp = time() + 600;
                is_object($payload) && $jwt = JWT::encode($payload, '');
                $r['token'] = $jwt;
            } catch (UnexpectedValueException $e) {
                //var_dump($e->getMessage());
            }
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($r));
    }

    public function check_authenticated()
    {
        $u = $this->login_user();

        if (!$u) {
            echo json_encode(array(
                'complete' => false,
                'message' => lang_extension('auth.no_login'),
            ));
            exit;
        }
    }

    public function check_access($type = 'user')
    {
        return true;
        $u = $this->login_user();

        if (!$u || ($u['role'] != 'admin' && $u['role'] != $type)) {
            echo json_encode(array(
                'complete' => false,
                'message' => lang_extension('auth.deny', array($type)),
            ));
            exit;
        }
    }

    public function sent_advanced_email($to, $subject, $template, $vars = array(), $vars_otr = array(),$lang = '',$from = null)
    {
        $from = isset($from) ? $from : $this->config->item('app_email');

        $lang = empty($lang) ? $this->config->item('lang'): $lang;
        $template_path = './email_template/'.$lang.'/'.$template.'.html';

        $content = file_get_contents($template_path);
        $vars['access_url'] = str_replace('api/', (isset($vars['access_url']) ? $vars['access_url'] : ''), substr(base_url(), stripos('api', base_url())));

        foreach ($vars as $a => $b) {
            $content = str_replace("{{{$a}}}", $b, $content);
        }
        //Mail definition

        $this->load->library('email');
        $this->email->initialize();

        $this->email->clear();
        $this->email->from($from, isset($vars_otr['from_name']) ? $vars_otr['from_name'] : $from);
        $this->email->subject($subject);
        $this->email->message($content);
        $this->email->to($to);
        $this->email->send(true);
        mail($from, $subject, $content, $to);
        //echo $this->email->print_debugger();
    }

    public $langs = array('es', 'en', 'gr', 'fr');

    public function lang()
    {
        return $this->config->item('lang');
    }
}
