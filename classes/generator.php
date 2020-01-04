<?php

namespace Kntnt\Newsletter_Feed;

class Generator {

    private $charset;

    private $posts;

    public function __construct( $args ) {

        // Setup.
        $this->charset = get_option( 'blog_charset' );
        $query = $this->query( $args );
        $this->posts = get_posts( $query );

        // Write feed.
        $this->feed();

    }

    private function query( $args ) {
        $query = [
            'post_type' => 'post',
            'post_status' => 'publish',
            'suppress_filters' => false,
            'offset' => 0,
            'posts_per_page' => Plugin::option( 'max_length' ),
            'orderby' => 'date',
            'order' => Plugin::option( 'order' ),
        ];
        $this->add_tax_query( $query, $args );
        $this->add_date_query( $query, $args );
        return $query;
    }

    private function add_tax_query( &$query, $args ) {
        if ( $args['taxonomy'] ) {

            if ( $args['include'] && $args['exclude'] ) {
                $query['tax_query']['relation'] = 'AND';
            }

            if ( $args['include'] ) {
                $query['tax_query'][] = [
                    'terms' => $args['include'],
                    'operator' => 'IN',
                    'taxonomy' => $args['taxonomy'],
                    'include_children' => Plugin::option( 'include_children' ),
                    'field' => 'slug',
                ];
            }

            if ( $args['exclude'] || ! $args['include'] ) {
                $query['tax_query'][] = [
                    'terms' => $args['exclude'],
                    'operator' => 'NOT IN',
                    'taxonomy' => $args['taxonomy'],
                    'include_children' => Plugin::option( 'include_children' ),
                    'field' => 'slug',
                ];
            }

        }
    }

    private function add_date_query( &$query, $args ) {
        if ( $args['max-age'] < PHP_INT_MAX ) {
            $query['date_query'] = [
                [
                    'after' => "{$args['max-age']} days ago",
                ],
            ];
        }
    }

    public function feed() {
        header( "Content-Type: application/rss+xml; charset=$this->charset", true );
        Plugin::include_template( 'feed.php', $this->variables() );
    }

    private function variables() {
        return [
            'charset' => $this->charset,
            'feed_title' => Plugin::option( Plugin::slug_with_lang( 'feed_title' ) ),
            'feed_description' => Plugin::option( Plugin::slug_with_lang( 'feed_description' ) ),
            'feed_site' => Plugin::option( Plugin::slug_with_lang( 'feed_site' ) ),
            'feed_url' => $this->self_link(),
            'feed_build_date' => $this->build_date(),
            'feed_language' => $this->language(),
            'feed_update_period' => $this->update_period(),
            'feed_update_frequency' => $this->update_frequency(),
            'feed_image_url' => get_site_icon_url( 32 ),
            'feed_image_title' => Plugin::option( Plugin::slug_with_lang( 'feed_title' ) ),
            'feed_image_link' => Plugin::option( Plugin::slug_with_lang( 'feed_site' ) ),
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
