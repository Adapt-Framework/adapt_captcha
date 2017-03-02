<?php

namespace adapt\captcha{
    
    class model_captcha extends \adapt\model{
        
        public function __construct($id = null, $data_source = null){
            parent::__construct('captcha', $id, $data_source);
        }

        public function save(){
            if(!$this->is_loaded) {
                $this->value = $this->generateCaptchaString();
                $this->ip_address = $_SERVER['REMOTE_ADDR'];
            }

            return parent::save();
        }

        public function generateCaptchaString() {

            $captcha_length = $this->setting('captcha_length');
            $generate_characters = 'abcdefghijklmnopqrstuvwxyz';
            $generate_numbers = '1234567890';
            $characters = '';

            if($this->setting('captcha_include_letters') == 'Yes') {
                $characters .= $generate_characters;
            }

            if($this->setting('captcha_include_numbers') == 'Yes') {
                $characters .= $generate_numbers;
            }

            return substr(str_shuffle(str_repeat($characters, ceil($captcha_length/strlen($characters)) )), 1, $captcha_length);
        }
    }
}