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
		return apply_filters( 'the_title_rss', get_the_title( $this->post ) );
	}

	public function link() {
		return esc_url( apply_filters( 'the_permalink_rss', get_permalink( $this->post ) ) );
	}

	public function date() {
		return mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true, $this->post ), false );
	}

	public function author() {
		return apply_filters( 'the_author', get_the_author_meta( 'display_name', $this->post->author ) );
	}

	public function categories() {
		$names = [];
		foreach ( get_the_category( $this->post->ID ) as $category ) {
			$names[] = @html_entity_decode( sanitize_term_field( 'name', $category->name, $category->term_id, 'category', 'rss' ), ENT_COMPAT, $this->charset );
		}
		return array_unique( $names );
	}

	public function guid() {
		return get_the_guid( $this->post );
	}

	public function description() {
		$description = $this->post->post_excerpt;
		if ( ! Plugin::option( 'raw_content' ) ) {
			$description = apply_filters( 'the_excerpt_rss', $description, $this->post );
		}
		return $description;
	}

	public function has_image() {
		return isset( $this->image );
	}

	public function image_url() {
		return $this->image['url'];
	}

	public function image_size() {
		return filesize( Plugin::str_join( wp_upload_dir()['basedir'], $this->image['path'] ) );
	}

	public function image_type() {
		return get_post_mime_type( $this->image['id'] );
	}

}
