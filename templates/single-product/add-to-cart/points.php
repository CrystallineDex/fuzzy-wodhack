<?php
/**
 * External product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
$points_to_deduct = get_post_meta($post->ID, 'points_product_cost');

?>
<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<?php

if (isset($_POST["submit"]))
{
    if ($_POST["formid"] == $_SESSION["formid"])
    {
        $_SESSION["formid"] = '';

        get_product()->process_points( $post->ID );
        echo 'Process form';
    }
    else
        echo 'Don\'t process form';
}
else
{
    $_SESSION["formid"] = md5(rand(0,10000000));
?>

<form method="post">
    <input type="hidden" name="formid" value="<?php echo $_SESSION["formid"]; ?>" />
    <button type="submit" name="submit">Spend <?php echo $points_to_deduct[0] ?> Point(s).</button>
</form>

<?php } ?>

<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
