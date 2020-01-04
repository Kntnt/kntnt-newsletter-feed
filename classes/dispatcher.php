<?php

namespace Kntnt\Newsletter_Feed;


class Dispatcher {

    private $args;

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
        $feed_name = Plugin::option( 'name' );
        if ( isset( $query_vars[ $feed_name ] ) ) {
            $this->args = array_merge( [
                'taxonomy' => '',
                'include' => [],
                'exclude' => [],
                'max-age' => PHP_INT_MAX,
            ], $this->parse( $query_vars[ $feed_name ] ) );
            Plugin::log( 'args = %s', $this->args );
        }
        return $query_vars;
    }

    public function dispatch() {
        if ( $this->args ) {
            new Generator( $this->args );
            exit;
        }
    }

    private function parse( $vars ) {
        $args = [];
        $items = explode( ';', $vars );
        foreach ( $items as $item ) {
            list( $key, $value ) = explode( '=', $item );
            if ( $value ) {
                if ( 'max-age' == $key ) {
                    $args[ $key ] = $value;
                }
                else {
                    $args[ $key ] = explode( ',', $value );
                }
            }
            else if ( $item ) {
                $args['taxonomy'] = $item;
            }
        }
        return $args;
    }

}
