<?php

class MoneyMailer{


    /**
     * Send an approve email to the buyer and the admin
     * @param $data
     */
    public function newAd( $data ){

        // to admin
        wp_mail(
            $data['admin_email'],
            __( 'A new ad is waiting for you to approve it!', 'ddabout' ) .' - '. get_bloginfo('name'),
            $this->getTemplateContent( 'admin-new-ad', $data ),
            $this->getBuyerEmailHeaders()
        );

        // to buyer
        wp_mail(
            $data['buyer_email'],
            __(  'Thank you for being one of the sponsors on', 'ddabout'  ) .' - '. get_bloginfo('name'),
            $this->getTemplateContent( 'buyer-new-ad', $data ),
            $this->getBuyerEmailHeaders()
        );
    }


    /**
     * Send an approve email to the buyer and the admin
     * @param $data
     */
    public function expired( $data ){
        // to admin
        wp_mail(
            $data['admin_email'],
            __( 'An ad has been expired', 'ddabout' ) .' - '. get_bloginfo('name'),
            $this->getTemplateContent( 'admin-expired', $data ),
            $this->getBuyerEmailHeaders()
        );

        // to buyer
        wp_mail(
            $data['buyer_email'],
            __( 'Your ad has been expired', 'ddabout'  ) .' - '. get_bloginfo('name'),
            $this->getTemplateContent( 'buyer-expired', $data ),
            $this->getBuyerEmailHeaders()
        );
    }


    /**
     * Send an approve email to the buyer
     * @param $data
     */
    public function approved( $data ){
        wp_mail(
            $data['buyer_email'],
            __( 'Your ad has been approved', 'ddabout' ) .' - '. get_bloginfo('name'),
            $this->getTemplateContent( 'buyer-approved-ad', $data ),
            $this->getBuyerEmailHeaders()
        );
    }


    /**
     * Send refund email to the buyer
     * @param $data
     */
    public function refund( $data ){
        wp_mail(
            $data['buyer_email'],
            __( 'Your ad has been deleted', 'ddabout' ) .' '. get_bloginfo('name'),
            $this->getTemplateContent( 'buyer-refund', $data ),
            $this->getBuyerEmailHeaders()
        );
    }


    /**
     * Return headers for buyer email
     * @return string
     */
    private function getBuyerEmailHeaders(){
        return 'Content-type: text/html;charset=utf-8' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
    }


    /**
     * Get Template content
     * @param $filename
     * @param $data
     * @return string
     */
    private function getTemplateContent( $filename, $data ){

        ob_start();
        require __DIR__ . '/templates/'.$filename.'.php';
        $message = ob_get_contents();
        ob_end_clean();

        return $message;
    }

}