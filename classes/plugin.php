<?php

namespace Kntnt\Newsletter_Feed;

class Plugin extends Abstract_Plugin {

	static public function image_sizes() {

		foreach ( [ 'thumbnail', 'medium', 'medium_large', 'large' ] as $image_size ) {
			$image_sizes[ $image_size ] = [
				'width' => get_option( $image_size . '_size_w' ),
				'height' => get_option( $image_size . '_size_h' ),
				'crop' => ( 'thumbnail' === $image_size ? (bool) get_option( 'thumbnail_crop' ) : false ),
			];
		}

		global $_wp_additional_image_sizes;
		if ( isset( $_wp_additional_image_sizes ) ) {
			$image_sizes = $image_sizes + $_wp_additional_image_sizes;
		}

		return $image_sizes;

	}

	public function classes_to_load() {

	    return [
            'any' => [
                'init' => [
                    'Setup',
                ],
            ],
			'public' => [
				'init' => [
					'Generator',
				],
			],
			'admin' => [
				'init' => [
					'Settings',
				],
			],
		];

	}

}
