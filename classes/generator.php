<?php

namespace Kntnt\Newsletter_Feed;

class Generator {

    private $ns;

    private $category;

    private $max_age;

    private $charset;

    private $posts;

    public function __construct() {
        $this->ns = Plugin::ns();
    }

    public function run() {
        add_action( 'template_redirect', [ $this, 'template_redirect' ] );
        Plugin::log( "Added the feed '%s'.", Plugin::option( 'name' ) );
    }

    public function template_redirect() {
        $name = Plugin::option( 'name' );
        if ( $this->category = get_query_var( $name ) ) {
            $this->max_age = get_query_var( "$name-max-age" );
            Plugin::log( "Generate feed '%s' with articles not older than %s days from category '%s'.", $name, $this->max_age, $this->category );
            $this->feed();
        }
    }

    public function feed() {
        $this->init();
        header( "Content-Type: application/rss+xml; charset=$this->charset", true );
        Plugin::include_template( 'feed.php', $this->variables() );
        exit;
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
                    'terms' => [ $this->category ],
                    'include_children' => Plugin::option( 'include_children' ),
                ],
            ],
            'date_query' => [
                [
                    'after' => "$this->max_age days ago",
                ],
            ],
        ] );
    }

    private function variables() {
        return [
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
            $posts[] = new Post( $item, $this->charset );
        }

        if ( 'ASC' == Plugin::option( 'order' ) ) {
            $posts = array_reverse( $posts );
        }

        return $posts;

    }

}
