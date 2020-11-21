<?php
   /*
   Plugin Name: Woocommerce add-on
   Plugin URI: https://www.infowindtech.com/
   description: This plugin show additional field in product detail page
   Version: 1.0
   Author: Infowindtech
   Author https://www.infowindtech.com/
   License: GPL2
   */

   function wao_plugin_activate() {

    if(!is_plugin_active('woocommerce/woocommerce.php') and current_user_can('activate_plugins')){
       wp_die('Sorry, but this plugin requires the woocommerce Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    }

     
   }

   register_activation_hook( __FILE__, 'wao_plugin_activate' );

   function wao_custom_option(){
    $from_name = isset( $_POST['from_name' ] ) ? sanitize_text_field( $_POST['from_name'] ) : '';
    printf( '<p><label>%s<input name="from_name" value="%s" /></label></p>', __( 'From Name ', 'wao-plugin-textdomain' ), esc_attr( $from_name ) );

    $from_email = isset( $_POST['from_email' ] ) ? sanitize_text_field( $_POST['from_email'] ) : '';
    printf( '<p><label>%s<input type="email" name="from_email" value="%s" /></label></p>', __( 'From Email ', 'wao-plugin-textdomain' ), esc_attr( $from_email ) );

    $from_message = isset( $_POST['from_message' ] ) ? sanitize_text_field( $_POST['from_message'] ) : '';
    printf( '<p><label>%s <textarea  name="from_message" >%s</textarea></label></p>', __( 'Message ', 'wao-plugin-textdomain' ), esc_attr( $from_message ) );

   }
   add_action( 'woocommerce_before_add_to_cart_button', 'wao_custom_option', 9 );


   function wao_add_to_cart_validation( $passed, $product_id, $qty ){

    if( isset( $_POST['from_name'] ) && sanitize_text_field( $_POST['from_name'] ) == '' ||  isset( $_POST['from_email'] ) && sanitize_text_field( $_POST['from_email'] ) == '' ){
        $product = wc_get_product( $product_id );
        wc_add_notice( sprintf( __( '%s cannot be added to the cart until you enter some field.', 'kia-plugin-textdomain' ), $product->get_title() ), 'error' );
        return false;
    }

    return $passed;

  }
  add_filter( 'woocommerce_add_to_cart_validation', 'wao_add_to_cart_validation', 10, 3 );


  function wao_add_cart_item_data( $cart_item, $product_id ){

    if( isset( $_POST['from_name'] ) ) {
        $cart_item['from_name'] = sanitize_text_field( $_POST[ 'from_name' ] );
    }
    if( isset( $_POST['from_email'] ) ) {
        $cart_item['from_email'] = sanitize_text_field( $_POST[ 'from_email' ] );
    }

    if( isset( $_POST['from_message'] ) ) {
        $cart_item['from_message'] = sanitize_text_field( $_POST[ 'from_message' ] );
    }

    return $cart_item;

  }
  add_filter( 'woocommerce_add_cart_item_data', 'wao_add_cart_item_data', 10, 2 );

  function wao_get_cart_item_from_session( $cart_item, $values ) {

    if ( isset( $values['from_name'] ) ){
        $cart_item['from_name'] = $values['from_name'];
    }

    if ( isset( $values['from_email'] ) ){
        $cart_item['from_email'] = $values['from_email'];
    }

    if ( isset( $values['from_message'] ) ){
        $cart_item['from_message'] = $values['from_message'];
    }

    return $cart_item;

  }
 add_filter( 'woocommerce_get_cart_item_from_session', 'wao_get_cart_item_from_session', 20, 2 );


 function wao_add_order_item_meta( $item_id, $values ) {

    if ( ! empty( $values['from_name'] ) ) {
        wc_add_order_item_meta( $item_id, 'from_name', $values['from_name'] );           
    }

    if ( ! empty( $values['from_email'] ) ) {
        wc_add_order_item_meta( $item_id, 'from_email', $values['from_email'] );           
    }

    if ( ! empty( $values['from_message'] ) ) {
        wc_add_order_item_meta( $item_id, 'from_message', $values['from_message'] );           
    }

}
/*add_action( 'woocommerce_add_order_item_meta', 'wao_add_order_item_meta', 10, 2 );

function wao_add_order_item_meta( $order_item, $cart_item_key, $values ) {

    if ( ! empty( $values['from_name'] ) ) {
      $order_item->add_meta_data( 'from_name', sanitize_text_field( $values[ 'from_name' ] ), true );       
    }

    if ( ! empty( $values['from_email'] ) ) {
      $order_item->add_meta_data( 'from_email', sanitize_text_field( $values[ 'from_email' ] ), true );       
    }

    if ( ! empty( $values['from_message'] ) ) {
      $order_item->add_meta_data( 'from_message', sanitize_text_field( $values[ 'from_message' ] ), true );       
    }



}*/
add_action( 'woocommerce_checkout_create_order_line_item', 'wao_add_order_item_meta', 10, 3 );


function wao_get_item_data( $other_data, $cart_item ) {

    if ( isset( $cart_item['from_name'] ) ){

        $other_data[] = array(
            'key' => __( 'From Name', 'wao-plugin-textdomain' ),
            'display' => sanitize_text_field( $cart_item['from_name'] )
        );

    }

    if ( isset( $cart_item['from_email'] ) ){

        $other_data[] = array(
            'key' => __( 'From Email', 'wao-plugin-textdomain' ),
            'display' => sanitize_text_field( $cart_item['from_email'] )
        );

    }

    if ( isset( $cart_item['from_message'] ) ){

        $other_data[] = array(
            'key' => __( 'Message', 'wao-plugin-textdomain' ),
            'display' => sanitize_text_field( $cart_item['from_message'] )
        );

    }

    return $other_data;

}
add_filter( 'woocommerce_get_item_data', 'wao_get_item_data', 10, 2 );
?>