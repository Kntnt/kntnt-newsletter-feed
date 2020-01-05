<?php

namespace Kntnt\Newsletter_Feed;


class Rewrite {

    public function run() {
        if ( $name = Plugin::option( 'name' ) ) {
            add_rewrite_endpoint( $name, EP_ROOT );
            Plugin::log( 'Added rewrite rule for /%s. Remember to flush the rewrite rules.', $name );
        }
        else {
            Plugin::log( 'Plugin options not set.' );
        }
    }

}
