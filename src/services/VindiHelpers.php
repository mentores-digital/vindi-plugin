<?php
class VindiHelpers
{


  function __construct()
  {

    add_action('woocommerce_process_product_meta', array($this, 'wc_post_meta'));
  }

  /**
   * Sanitize statement descriptor text.
   *
   * Vindi requires max of 22 characters and no
   * special characters with ><"'.
   *
   * @since 1.0.0
   * @param string $statement_descriptor
   * @return string $statement_descriptor Sanitized statement descriptor
   */
  public static function clean_statement_descriptor($statement_descriptor = '')
  {
    $disallowed_characters = array('<', '>', '"', "'");

    // Remove special characters.
    $statement_descriptor = str_replace($disallowed_characters, '', $statement_descriptor);

    $statement_descriptor = substr(trim($statement_descriptor), 0, 22);

    return $statement_descriptor;
  }

  /**
   * Get Vindi amount to pay
   *
   * @param float  $total Amount due.
   * @param string $currency Accepted currency.
   *
   * @return float|int
   */

  public static function get_vindi_amount($total, $currency = '')
  {
    if (!$currency) {
      $currency = get_woocommerce_currency();
    }

    return absint(wc_format_decimal(((float) $total * 100), wc_get_price_decimals())); // In cents.

  }

  /**
   * Checks if WC version is less than passed in version.
   *
   * @since 1.0.0
   * @param string $version Version to check against.
   * @return bool
   */
  public static function is_wc_lt($version)
  {
    return version_compare(WC_VERSION, $version, '<');
  }

  /**
   * Save Woocommerce custom attributes
   *
   * @since 1.0.0
   * @param string $version Version to check against.
   * @return null
   */

  public static function wc_post_meta($post_id, $custom_attributes)
  {

    // Get product
    $product = wc_get_product($post_id);

    $i = 0;

    // Loop through the attributes array
    foreach ($custom_attributes as $name => $value) {

      // Check meta value exists
      $product->update_meta_data($name, $value);

      $i++;
    }

    $product->save();
  }

  /**
   * Get a subscription that has an item equals as an order item, if any.
   *
   * @since 1.0.0
   * @param WC_Order $order A WC_Order object
   * @param int $product_id The product/post ID of a subscription
   * @return null
   */
  public static function get_matching_subscription($order, $order_item)
  {
		$subscriptions = wcs_get_subscriptions_for_order($order, array('order_type' => 'parent'));
    $matching_subscription = null;
    foreach ($subscriptions as $subscription) {
      foreach ($subscription->get_items() as $subscription_item) {
        $line_item = wcs_find_matching_line_item($order, $subscription_item, $match_type = 'match_attributes');
        if($order_item === $line_item) {
          $matching_subscription = $subscription;
          break 2;
        }
      }
    }

		if (null === $matching_subscription && !empty($subscriptions)) {
			$matching_subscription = array_pop($subscriptions);
		}

		return $matching_subscription;
	}
}
