<?php

namespace Kntnt\Newsletter_Feed;

class Post {

	private $post;

	private $charset;

	private $image = null;

	public function __construct( $post, $charset ) {

		$this->post = $post;
		$this->charset = $charset;

		if ( $image = Plugin::option( 'image' ) ) {
			$thumbnail_id = get_post_thumbnail_id( $this->post );
			$this->image = image_get_intermediate_size( $thumbnail_id, $image );
			$this->image['id'] = $thumbnail_id;
		}

	}

	public function title() {
		$original = get_the_title( $this->post );
		$filtered = apply_filters( 'the_title_rss', $original );
		Plugin::log( 'Original: %s', $original );
		Plugin::log( 'Filtered: %s', $filtered );
		return $filtered;
	}

	public function link() {
		$original = get_permalink( $this->post );
		$filtered = esc_url( apply_filters( 'the_permalink_rss', $original ) );
		Plugin::log( 'Original: %s', $original );
		Plugin::log( 'Filtered: %s', $filtered );
		return $filtered;
	}

	public function date() {
		$original = get_post_time( 'Y-m-d H:i:s', true, $this->post );
		$filtered = mysql2date( 'D, d M Y H:i:s +0000', $original, false );
		Plugin::log( 'Original: %s', $original );
		Plugin::log( 'Filtered: %s', $filtered );
		return $filtered;
	}

	public function author() {
		$original = get_the_author_meta( 'display_name', $this->post->author );
		$filtered = apply_filters( 'the_author', $original );
		Plugin::log( 'Original: %s', $original );
		Plugin::log( 'Filtered: %s', $filtered );
		return $filtered;
	}

	public function categories() {
		$names = [];
		$categories = get_the_category( $this->post->ID );
		foreach ( $categories as $category ) {
			$original = $category->name;
			$filtered = @html_entity_decode( sanitize_term_field( 'name', $original, $category->term_id, 'category', 'rss' ), ENT_COMPAT, $this->charset );;
			Plugin::log( 'Original: %s', $original );
			Plugin::log( 'Filtered: %s', $filtered );
			$names[] = $filtered;
		}
		return array_unique( $names );
	}

	public function guid() {
		$original = get_the_guid( $this->post );
		Plugin::log( 'Original: %s', $original );
		return $original;
	}

	public function description() {
		$original = $this->post->post_excerpt;
		Plugin::log( 'Original: %s', $original );
		$filtered = $original;
		if ( ! Plugin::option( 'raw_content' ) ) {
			$filtered = apply_filters( 'the_excerpt_rss', $original, $this->post );
			Plugin::log( 'Filtered: %s', $filtered );
		}
		return $filtered;
	}

	public function has_image() {
		return isset( $this->image );
	}

	public function image_url() {
		$original = $this->image['url'];
		Plugin::log( 'Original: %s', $original );
		return $original;
	}

	public function image_size() {
		$original = filesize( Plugin::str_join( wp_upload_dir()['basedir'], $this->image['path'] ) );
		Plugin::log( 'Original: %s', $original );
		return $original;
	}

	public function image_type() {
		$original = get_post_mime_type( $this->image['id'] );
		Plugin::log( 'Original: %s', $original );
		return $original;
	}

}
