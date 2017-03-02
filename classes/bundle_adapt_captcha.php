<?php

namespace adapt\captcha{

    /* Prevent Direct Access */
    defined('ADAPT_STARTED') or die;
    
    class bundle_adapt_captcha extends \adapt\bundle{

        public function __construct($data){
            parent::__construct('adapt_captcha', $data);
        }
        
        public function boot(){
            if (parent::boot()) {

                \application\controller_root::extend('view__captcha', function($_this) {
                    return $_this->load_controller('\\adapt\\captcha\\controller_captcha');
                });

                return true;
            }
            return false;
        }
    }
}

?>