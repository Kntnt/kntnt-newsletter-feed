<?php

namespace Kntnt\Newsletter_Feed;

require_once Plugin::plugin_dir( 'classes/abstract-settings.php' );

class Settings extends Abstract_Settings {

	/**
	 * Returns the settings menu title.
	 */
	protected function menu_title() {
		return __( 'Newsletter Feed', 'kntnt-newsletter-feed' );
	}

	/**
	 * Returns the settings page title.
	 */
	protected function page_title() {
		return __( "Kntnt Newsletter Feed", 'kntnt-newsletter-feed' );
	}

	/**
	 * Returns all fields used on the settings page.
	 */
	protected function fields() {

        $translatable_msg = function_exists('icl_object_id') ? ' This field should be <a href="/wp-admin/admin.php?page=wpml-string-translation%2Fmenu%2Fstring-translation.php&context=admin_texts_kntnt-newsletter-feed">translated</a>.' : '';

		$fields['name'] = [
			'type' => 'text',
			'label' => __( "Slug", 'kntnt-newsletter-feed' ),
			'size' => 80,
			'description' => __( 'The <a href="https://wordpress.org/support/article/wordpress-feeds/#finding-your-feed-url">name used in the URL</a> to access this type of feed.', 'kntnt-newsletter-feed' ),
			'default' => 'newsletter',
			'required' => true,
		];

		$fields['feed_title'] = [
			'type' => 'text',
			'label' => __( "Title", 'kntnt-newsletter-feed' ),
			'size' => 80,
            'description' => sprintf('%s%s', __( 'Title used in the feed.', 'kntnt-newsletter-feed' ), $translatable_msg),
			'default' => get_bloginfo( 'name' ),
			'required' => true,
		];

		$fields['feed_description'] = [
			'type' => 'text area',
			'label' => __( "Description", 'kntnt-newsletter-feed' ),
			'cols' => 80,
			'rows' => 5,
            'description' => sprintf('%s%s', __( 'Description used in the feed.', 'kntnt-newsletter-feed' ), $translatable_msg),
			'default' => get_bloginfo( 'description' ),
			'required' => true,
			'filter-after' => 'stripslashes',
		];

		$fields['feed_site'] = [
			'type' => 'text',
			'label' => __( "Link", 'kntnt-newsletter-feed' ),
			'size' => 80,
            'description' => sprintf('%s%s', __( 'Url for the feed\'s source.', 'kntnt-newsletter-feed' ), $translatable_msg),
			'default' => get_bloginfo( 'wpurl' ),
			'required' => true,
		];

		$fields['categories'] = [
			'type' => 'checkbox group',
			'label' => __( 'Sources', 'kntnt-newsletter-feed' ),
			'description' => __( 'Select one or more categories to be used as source of the newsletter feed.', 'kntnt-newsletter-feed' ),
			'options' => $this->categories(),
		];

		$fields['order'] = [
			'type' => 'select',
			'label' => __( "Order", 'kntnt-newsletter-feed' ),
			'options' => [
				'ASC' => 'Chronological',
				'DESC' => 'Reverse chronological',
			],
			'description' => __( 'The posts appear in the feed in either chronological or reverse chronological order depending on this setting.', 'kntnt-newsletter-feed' ),
			'required' => true,
		];

		$fields['max_length'] = [
			'type' => 'integer',
			'label' => __( "Max articles", 'kntnt-newsletter-feed' ),
			'size' => 20,
			'description' => __( 'The maximum number of items in th feed', 'kntnt-newsletter-feed' ),
			'required' => true,
		];

		$fields['image'] = [
			'type' => 'select',
			'label' => __( "Image as enclosure", 'kntnt-newsletter-feed' ),
			'description' => __( 'Select image size to enclose posts\' featured images.', 'kntnt-newsletter-feed' ),
			'options' => $this->image_sizes(),
		];

		$fields['raw_content'] = [
			'type' => 'checkbox',
			'label' => __( "Raw content", 'kntnt-newsletter-feed' ),
			'description' => __( 'Bypass the filter <code>get_the_excerpt</code>.', 'kntnt-newsletter-feed' ),
		];

		$fields['submit'] = [
			'type' => 'submit',
		];

		return $fields;

	}

	protected function actions_after_saving( $opt, $fields ) {

		global $wp_rewrite;
		if ( ! in_array( $opt['name'], $wp_rewrite->feeds ) ) {
			$wp_rewrite->feeds[] = $opt['name'];
			$wp_rewrite->flush_rules();
		}

		parent::actions_after_saving( $opt, $fields );

	}

	private function categories() {

		$categories = get_terms( [
			'taxonomy' => 'category',
			'hide_empty' => false,
			'orderby' => 'name',
		] );

		$tree = [];
		$this->build_tree( $tree, $categories );

		$categories = [];
		$this->rename( $categories, $tree );

		return $categories;

	}

	private function build_tree( &$tree, &$categories, $parent = 0 ) {

		foreach ( $categories as $i => $category ) {
			if ( $category->parent == $parent ) {
				$tree[ $category->term_id ] = $category;
				unset( $categories[ $i ] );
			}
		}

		foreach ( $tree as $parent ) {
			$parent->children = [];
			$this->build_tree( $parent->children, $categories, $parent->term_id );
		}

	}

	private function rename( &$categories, &$tree, $prefix = '' ) {
		foreach ( $tree as $category ) {
			$categories[ $category->slug ] = $prefix . $category->name;
			if ( $category->children ) {
				$this->rename( $categories, $category->children, "$category->name > " );
			}
		}
	}

	private function image_sizes() {
		foreach ( Plugin::image_sizes() as $name => $size ) {
			$image_sizes[ $name ] = "$name (${size['width']}x${size['height']})";
		}
		return [ '' => '' ] + $image_sizes;
	}

}
