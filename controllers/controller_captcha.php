<?php

namespace adapt\captcha{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class controller_captcha extends \adapt\controller{

        public function generateCaptchaImage($captcha_string, $filename = ''){
            $my_img = imagecreate( 200, 80 );
            imagecolorallocatealpha( $my_img, 255, 255, 255, 126 );
            $text_colour = imagecolorallocate( $my_img, 0, 0, 0 );
            $line_colour = imagecolorallocate( $my_img, 16, 21, 61 );

            imagestring( $my_img, 10, 75, 25, $captcha_string, $text_colour );
            imagesavealpha($my_img, true);
            imagesetthickness ( $my_img, 5 );
            imageline( $my_img, 30, 44, 165, 44, $line_colour );
            
            header('Content-type: image/png');

            imagefilter( $my_img, IMG_FILTER_GAUSSIAN_BLUR, 0);
            $scaled_image = imagescale( $my_img, 300);
            $created_image = imagepng( $scaled_image);
            imagepng( $scaled_image, $filename );
            imagecolordeallocate( $line_color );
            imagecolordeallocate( $text_color );
            imagedestroy( $scaled_image );
        }

        public function saveImage($filename) {
            $key = "captcha/" . $this->file_store->get_new_key();
            $this->file_store->set_by_file($key, $filename, 'image/png');
            unlink($filename);
            return $key;
        }

        public function storeCaptcha($guid, $key) {
            $model = new model_captcha();
            if($model->load_by_guid($guid)) {
                $model->file_key = $key;
                $model->save();
            }
        }

        public function checkCaptcha($guid, $check) {
            $model = new model_captcha();
            if($model->load_by_guid($guid)) {
                if($model->used == 'No' && $model->ip_address == $_SERVER['REMOTE_ADDR']) {
                    if($model->value == $check) {
                        return true;
                    }
                }
            } 
            return false;
        }

        public function generateCaptcha($value, $guid) {
            $filename = TEMP_PATH . guid();
            $captcha_image = $this->generateCaptchaImage($value, $filename);
            $key = $this->saveImage($filename);

            $this->storeCaptcha($guid, $key);

            return $captcha_image;
        }

        public function view_default(){
            if($this->request['guid']) {
                $model = new model_captcha();
                if($model->load_by_guid($this->request['guid'])) {
                    if($model->file_key){
                        $this->content_type = $this->file_store->get_content_type($model->file_key);
                        return $this->file_store->get($model->file_key);
                    }else{
                        $this->content_type = 'image/png';
                        return $this->generateCaptcha($model->value, $model->guid);
                    }
                }
            }
        }
    }
}

?>