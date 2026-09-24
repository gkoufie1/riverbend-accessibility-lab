<?php
/**
 * Plugin Name: Legacy Vendor Tweaks
 * Description: Left behind by a previous vendor. Disables WordPress's automatic downscaling of large uploads.
 */

add_filter( 'big_image_size_threshold', '__return_false' );
