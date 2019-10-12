<?xml version = "1.0" encoding = "<?php echo $charset; ?>"?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>
    <channel>
        <title><?php echo $feed_title; ?></title>
        <atom:link href="<?php echo $feed_url; ?>" rel="self" type="application/rss+xml"/>
        <link><?php echo $feed_site; ?></link>
        <description><![CDATA[<?php echo $feed_description; ?>]]></description>
        <lastBuildDate><?php echo $feed_build_date; ?></lastBuildDate>
        <language><?php echo $feed_language; ?></language>
        <sy:updatePeriod><?php echo $feed_update_period; ?></sy:updatePeriod>
        <sy:updateFrequency><?php echo $feed_update_frequency; ?></sy:updateFrequency>
        <image>
            <url><?php echo $feed_image_url; ?></url>
            <title><?php echo $feed_image_title; ?></title>
            <link><?php echo $feed_image_link; ?></link>
            <width><?php echo $feed_image_width; ?></width>
            <height><?php echo $feed_image_height; ?></height>
        </image>
		<?php foreach ( $posts as $post ): ?>
            <item>
                <title><?php echo $post->title(); ?></title>
                <link><?php echo $post->link(); ?></link>
                <pubDate><?php echo $post->date(); ?></pubDate>
                <dc:creator><![CDATA[<?php echo $post->author(); ?>]]></dc:creator>
				<?php foreach ( $post->categories() as $category ): ?>
                    <category><![CDATA[<?php echo $category; ?>]]></category>
				<?php endforeach; ?>
                <guid isPermaLink="false"><?php echo $post->guid(); ?></guid>
                <description><![CDATA[<?php echo $post->description(); ?>]]></description>
				<?php if ( $post->has_image() ): ?>
                    <enclosure url="<?php echo $post->image_url() ?>" length="<?php echo $post->image_size() ?>" type="<?php echo $post->image_type() ?>"/>
				<?php endif; ?>
            </item>
		<?php endforeach; ?>
    </channel>
</rss>