<?php
<?php
  /**
   * Plugin Name: WooCommerce Delete Reviews
   * Description: A plugin to delete all WooCommerce product reviews with a single button and option to remove the button from the control panel.
   * Plugin URI: https:/miladjafarigavzan.ir
   * Version: 1.0
   * Author: Milad Jafari Gavzan
   * Author URI: https://miladjafarigavzan.ir
   * License: GPL-2.0+
   */

  */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

// Hook to add admin menu
add_action('admin_menu', 'woo_delete_reviews_menu');

function woo_delete_reviews_menu() {
	add_menu_page(
		'WooCommerce Delete Reviews',
		'Delete Reviews',
		'manage_options',
		'woo-delete-reviews',
		'woo_delete_reviews_page',
		'dashicons-trash',
		26
	);
}

// Create settings page with a delete button
function woo_delete_reviews_page() {
	if (!current_user_can('manage_options')) {
		return;
	}

	if (isset($_POST['woo_delete_all_reviews'])) {
		woo_delete_all_reviews();
		echo '<div class="notice notice-success is-dismissible"><p>All WooCommerce reviews have been deleted!</p></div>';
	}

	// Check if button should be displayed
	$show_delete_button = get_option('woo_show_delete_button', true);

	?>
	<div class="wrap">
		<h1>Delete WooCommerce Reviews</h1>

		<?php if ($show_delete_button): ?>
			<form method="post" action="">
				<?php submit_button('Delete All Reviews', 'delete', 'woo_delete_all_reviews', true); ?>
			</form>
		<?php else: ?>
			<p>The delete button is disabled in the settings.</p>
		<?php endif; ?>

		<hr>

		<form method="post" action="options.php">
			<?php
			settings_fields('woo_delete_reviews_settings');
			do_settings_sections('woo-delete-reviews');
			submit_button();
			?>
		</form>
	</div>
	<?php
}

// Function to delete all reviews
function woo_delete_all_reviews() {
	global $wpdb;

	$comments = $wpdb->get_results("SELECT comment_ID FROM {$wpdb->comments} WHERE comment_type = 'review'");

	foreach ($comments as $comment) {
		wp_delete_comment($comment->comment_ID, true);
	}
}

// Register setting to toggle delete button
add_action('admin_init', 'woo_delete_reviews_settings');

function woo_delete_reviews_settings() {
	register_setting('woo_delete_reviews_settings', 'woo_show_delete_button');

	add_settings_section(
		'woo_delete_reviews_section',
		'Delete Button Settings',
		null,
		'woo-delete-reviews'
	);

	add_settings_field(
		'woo_show_delete_button',
		'Show Delete Button',
		'woo_show_delete_button_callback',
		'woo-delete-reviews',
		'woo_delete_reviews_section'
	);
}

// Callback to render checkbox field
function woo_show_delete_button_callback() {
	$show_delete_button = get_option('woo_show_delete_button', true);
	?>
	<input type="checkbox" name="woo_show_delete_button" value="1" <?php checked(1, $show_delete_button, true); ?> />
	<label for="woo_show_delete_button">Check to show the delete button</label>
	<?php
}

