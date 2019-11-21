<?php

defined( 'WPINC' ) || die;

add_option( 'kntnt-newsletter-feed', [
	'name' => 'newsletter',
    'include_children' => true,
	'order' => 'DESC',
    'max_length' => 10,
	'image' => false,
] );
