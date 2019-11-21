<?php

namespace Kntnt\Newsletter_Feed;

class Setup {

    private $ns;

    public function __construct() {
        $this->ns = Plugin::ns();
    }

    public function run() {
        add_filter( 'query_vars', function ( $query_vars ) {
            $name = Plugin::option( 'name' );
            $query_vars[] = $name;
            $query_vars[] = "$name-max-age";
            Plugin::log( "Added '%s' and '%s' as query variables.", $name, "$name-max-age" );
            return $query_vars;
        } );
    }

}
