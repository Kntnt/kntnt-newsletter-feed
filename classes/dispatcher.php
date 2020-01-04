<?php

namespace Kntnt\Newsletter_Feed;


class Dispatcher {

    public function run() {
        if ( $name = Plugin::option( 'name' ) ) {
            add_rewrite_endpoint( $name, EP_ROOT );
            add_filter( 'request', [ $this, 'add_default_values' ] );
            add_action( 'template_redirect', [ $this, 'dispatch' ] );
        }
        else {
            Plugin::log( 'Plugin options not set.' );
        }
    }

    public function add_default_values( $query_vars ) {
        if ( isset( $query_vars['name'] ) && $query_vars['name'] == Plugin::option( 'name' ) ) {
            $query_vars = $query_vars + [
                    'taxonomy' => 'category',
                    'include' => '',
                    'exclude' => '',
                    'max-age' => 0,
                ] );
            Plugin::log( '$query_vars = %s', $query_vars );
        }
        return $query_vars;
    }

    public function dispatch() {
        if ( ( $name = Plugin::option( 'name' ) ) && ( $query_vars = get_query_var( $name ) ) ) {
            // Note: No need for sanitization, since WP_Query has that built-in.
            new Generator( $query_vars );
            exit;
        }
    }

}
