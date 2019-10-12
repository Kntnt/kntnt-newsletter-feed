<?php

defined( 'WPINC' ) || die;

add_option( 'kntnt-newsletter-feed', [
	'name' => 'newsletter',
	'categories' => [],
	'order' => 'DESC',
	'max_length' => 10,
	'image' => false,
	'raw_content' => false,
] );
