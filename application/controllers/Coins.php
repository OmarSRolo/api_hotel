<?php

class Coins extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->json_result(true, '', $this->coin_model->order_by('name')->get_all());
    }

    public function query()
    {
        $this->json_result(true, '', $this->coin_model->filter($this->input->get_post('page'), $this->input->get_post('count'), array(
            'code' =>  array('like' => $this->input->get_post('code')),
            'name' =>  array('like' => $this->input->get_post('name'))
        ), $this->input->post('order_by')));
    }

    public function insert()
    {
        try {
            $this->check_access('admin');
            $data = array();

            $this->input->post('code') != null && $data['code'] = $this->input->post('code');
            $this->input->post('name') != null && $data['name'] = $this->input->post('name');

            $r = $this->coin_model->insert($data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function update()
    {
        try {
            $this->check_access('admin');
            $data = array();

            $this->input->post('code') != null && $data['code'] = $this->input->post('code');
            $this->input->post('name') != null && $data['name'] = $this->input->post('name');

            $r = $this->coin_model->update($this->input->post('id'), $data);
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->check_access('admin');
            $r = $this->coin_model->delete($this->input->post('id'));
            $this->json_result($r, $r ? '' : validation_errors());
        } catch (Exception $e) {
            $this->json_result(false, $e->getMessage());
        }
    }

    public function convert()
    {
        $results = $this->convert_currency($this->input->get_post('base'), $this->input->get_post('convert'), $this->input->get_post('amount'));

        $regularExpression = '#\<span class=bld\>(.+?)\<\/span\>#s';
        preg_match($regularExpression, $results, $finalData);
        if (isset($finalData[0])) {
            $this->json_result(true, '', $finalData[0]);
        } else {
            $this->json_result(false, $result['error']);
        }
    }

    private function convert_currency($from, $to, $amount)
    {
        $url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
        $request = curl_init();
        $timeOut = 0;
        curl_setopt($request, CURLOPT_URL, $url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)');
        curl_setopt($request, CURLOPT_CONNECTTIMEOUT, $timeOut);
        $response = curl_exec($request);
        curl_close($request);

        return $response;
    }
}
