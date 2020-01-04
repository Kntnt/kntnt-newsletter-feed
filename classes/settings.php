<?php

namespace Kntnt\Newsletter_Feed;

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

        $lang = Plugin::current_language();

        $fields['name'] = [
            'type' => 'text',
            'label' => __( "Slug", 'kntnt-newsletter-feed' ),
            'size' => 80,
            'description' => __( 'The <a href="https://wordpress.org/support/article/wordpress-feeds/#finding-your-feed-url">name used in the URL</a> to access this type of feed.', 'kntnt-newsletter-feed' ),
            'default' => 'newsletter',
            'required' => true,
        ];

        $fields[ Plugin::slug_with_lang( 'feed_title' ) ] = [
            'type' => 'text',
            'label' => __( "Title", 'kntnt-newsletter-feed' ),
            'size' => 80,
            'description' => __( 'Title used in the feed.', 'kntnt-newsletter-feed' ),
            'default' => get_bloginfo( 'name' ),
            'required' => true,
        ];

        $fields[ Plugin::slug_with_lang( 'feed_description' ) ] = [
            'type' => 'text area',
            'label' => __( "Description", 'kntnt-newsletter-feed' ),
            'cols' => 80,
            'rows' => 5,
            'description' => __( 'Description used in the feed.', 'kntnt-newsletter-feed' ),
            'default' => get_bloginfo( 'description' ),
            'required' => true,
            'filter-after' => 'stripslashes',
        ];

        $fields[ Plugin::slug_with_lang( 'feed_site' ) ] = [
            'type' => 'text',
            'label' => __( "Link", 'kntnt-newsletter-feed' ),
            'size' => 80,
            'description' => __( 'Url for the feed\'s source.', 'kntnt-newsletter-feed' ),
            'default' => get_bloginfo( 'wpurl' ),
            'required' => true,
        ];

        $fields['include_children'] = [
            'type' => 'checkbox',
            'label' => __( "Subcategories", 'kntnt-newsletter-feed' ),
            'description' => __( 'Check to include subcategories.', 'kntnt-newsletter-feed' ),
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
            'default' => 10,
            'required' => true,
        ];

        $fields['image'] = [
            'type' => 'select',
            'label' => __( "Image as enclosure", 'kntnt-newsletter-feed' ),
            'description' => __( 'Select image size to enclose posts\' featured images.', 'kntnt-newsletter-feed' ),
            'options' => $this->image_sizes(),
            'default' => 'medium_large',
        ];

        $fields['submit'] = [
            'type' => 'submit',
        ];

        return $fields;

    }

    private function image_sizes() {
        foreach ( Plugin::image_sizes() as $name => $size ) {
            $image_sizes[ $name ] = "$name (${size['width']}x${size['height']})";
        }
        return [ '' => '' ] + $image_sizes;
    }

}
