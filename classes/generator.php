<?php

namespace Kntnt\Newsletter_Feed;

use Psr\Log\AbstractLogger;

class Generator {

	private $ns;

	private $charset;

	private $posts;

	public function __construct() {
		$this->ns = Plugin::ns();
	}

	public function run() {
		add_feed( Plugin::option( 'name' ), [ $this, 'feed' ] );
		Plugin::log( "Added the feed '%s'.", Plugin::option( 'name' ) );
	}

	public function feed() {
		$this->init();
		header( "Content-Type: application/rss+xml; charset=$this->charset", true );
		Plugin::include_template( 'feed.php', $this->variables() );
	}

	private function init() {
		$this->charset = get_option( 'blog_charset' );
		$this->posts = get_posts( [
			'post_type' => 'post',
			'post_status' => 'publish',
			'suppress_filters' => false,
			'offset' => 0,
			'posts_per_page' => Plugin::option( 'max_length' ),
			'orderby' => 'date',
			'order' => 'DESC',
			'tax_query' => [
				[
					'taxonomy' => 'category',
					'field' => 'slug',
					'terms' => Plugin::option( 'categories' ),
					'include_children' => true,
				],
			],
		] );
	}

	private function variables() {

		$variables = [
			'charset' => $this->charset,
			'feed_title' => Plugin::option( 'feed_title' ),
			'feed_description' => Plugin::option( 'feed_description' ),
			'feed_site' => Plugin::option( 'feed_site' ),
			'feed_url' => $this->self_link(),
			'feed_build_date' => $this->build_date(),
			'feed_language' => $this->language(),
			'feed_update_period' => $this->update_period(),
			'feed_update_frequency' => $this->update_frequency(),
			'feed_image_url' => get_site_icon_url( 32 ),
			'feed_image_title' => Plugin::option( 'feed_title' ),
			'feed_image_link' => Plugin::option( 'feed_site' ),
			'feed_image_width' => 32,
			'feed_image_height' => 32,
			'posts' => $this->posts(),
		];

		Plugin::log( "Variables = %s", $variables );

		return $variables;

	}

	private function self_link() {
		$host = @parse_url( home_url() );
		return esc_url( apply_filters( 'self_link', set_url_scheme( 'http://' . $host['host'] . wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
	}

	private function build_date() {

		if ( ! $this->posts ) {
			return get_lastpostmodified( 'GMT' );
		}

		$modified_times = wp_list_pluck( $this->posts, 'post_modified_gmt' );
		$max_modified_time = mysql2date( 'r', max( $modified_times ), false );
		return apply_filters( 'get_feed_build_date', $max_modified_time, 'r' );

	}

	private function language() {
		return get_bloginfo_rss( 'language' );
	}

	private function update_period() {
		return apply_filters( 'rss_update_period', 'hourly' );
	}

	private function update_frequency() {
		return apply_filters( 'rss_update_frequency', 1 );
	}

	private function posts() {

		$posts = [];

		foreach ( $this->posts as $item ) {

			$post = new \stdClass;

			$post->title = apply_filters( 'the_title_rss', get_the_title( $item ) );

			$post->link = esc_url( apply_filters( 'the_permalink_rss', get_permalink( $item ) ) );

			$post->date = mysql2date( 'D, d M Y H:i:s +0000', get_post_time( 'Y-m-d H:i:s', true, $item ), false );

			$post->author = apply_filters( 'the_author', get_the_author_meta( 'display_name', $item->author ) );

			$post->categories = $this->categories( $item );

			$post->guid = get_the_guid( $item );

			$post->description = $this->description( $item );

			if ( $image = Plugin::option( 'image' ) ) {

				$thumbnail_id = get_post_thumbnail_id( $item );
				$thumbnail = image_get_intermediate_size( $thumbnail_id, $image );

				$post->image_url = $thumbnail['url'];
				$post->image_size = filesize( Plugin::str_join( wp_upload_dir()['basedir'], $thumbnail['path'] ) );
				$post->image_type = get_post_mime_type( $thumbnail_id );

			}

			$posts[] = $post;

		}

		if ( 'ASC' == Plugin::option( 'order' ) ) {
			$posts = array_reverse( $posts );
		}

		return $posts;

	}

	private function categories( $post ) {
		$names = [];
		$categories = get_the_category( $post->ID );
		foreach ( $categories as $category ) {
			$name = sanitize_term_field( 'name', $category->name, $category->term_id, 'category', 'rss' );
			$names[] = @html_entity_decode( $name, ENT_COMPAT, $this->charset );
		}
		return array_unique( $names );
	}

	private function description( $post ) {
		$description = $post->post_excerpt;
		if ( ! Plugin::option( 'raw_content' ) ) {
			$description = apply_filters( 'the_excerpt_rss', $description, $post );
		}
		return $description;
	}

}
