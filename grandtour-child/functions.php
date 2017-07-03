<?php

/**
 * Includes 'style.css'.
 * Disable this filter if you don't use child style.css file.
 *
 * @param  assoc $default_set set of styles that will be loaded to the page
 * @return assoc
 */
function filter_adventure_tours_get_theme_styles( $default_set ) {
	$default_set['child-style'] = get_stylesheet_uri();
	return $default_set;
}
add_filter( 'get-theme-styles', 'filter_adventure_tours_get_theme_styles' );


/**
*	Setup add product to cart function
**/
add_action('wp_ajax_grandtour_child_add_to_cart', 'grandtour_child_add_to_cart');
add_action('wp_ajax_nopriv_grandtour_child_add_to_cart', 'grandtour_child_add_to_cart');

function grandtour_child_add_to_cart() {
	if(isset($_GET['product_id']) && !empty($_GET['product_id']) && class_exists('Woocommerce'))
	{
		$product_ID = $_GET['product_id'];
		$tour_date = $_REQUEST['tour_date'];
		//Check if variable product
		$obj_product = wc_get_product($product_ID);
		$woocommerce = grandtour_get_woocommerce();
		
		if($obj_product->is_type('variable')) 
		{
			//Get all product variation
			$args = array(
			 'post_type'     => 'product_variation',
			 'post_status'   => array( 'private', 'publish' ),
			 'numberposts'   => -1,
			 'orderby'       => 'menu_order',
			 'order'         => 'ASC',
			 'post_parent'   => $product_ID
			);
			$variations = get_posts( $args ); 
			$d = array('tour_date' => $tour_date);
													 
			foreach ($variations as $variation)
			{
				//Get variation ID
				$variation_ID = $variation->ID;		
				if(isset($_POST[$variation_ID]) && !empty($_POST[$variation_ID]))
				{
					$woocommerce->cart->add_to_cart($product_ID, intval($_POST[$variation_ID]), $variation_ID,'',$d);
				}
			}
		}
		else
		{
			$woocommerce->cart->add_to_cart($product_ID);
		}
	}
	
	die();
}

/**
*	End add product to cart function
**/

/**
Adding custom data to woocommerce order
**/

add_action('woocommerce_add_order_item_meta','wdm_add_values_to_order_item_meta',1,2);
if(!function_exists('wdm_add_values_to_order_item_meta'))
{
  function wdm_add_values_to_order_item_meta($item_id, $values)
  {
        global $woocommerce,$wpdb;
        $user_custom_values = $values['tour_date'];
        if(!empty($user_custom_values))
        {
            wc_add_order_item_meta($item_id,'tour_date',$user_custom_values);  
        }
  }
}