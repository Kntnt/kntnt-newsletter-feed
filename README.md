# Kntnt Newsletter Feed

WordPress plugin that provide a RSS feed for automatic newsletter generation. 

## Description

Below <name> is the name of the feed as configured in the settings.

To include all posts:

    /<name>

To include posts published not later than `<max-age>` days ago:

    /<name>/max-age=<number-of-days>

To include all posts having at least one term from the taxonomy with the slug
`<taxonomy>`:

    /<name>/<taxonomy>

To include all posts having at least one of the `<term>`:s of the taxonomy
`<taxonomy>`:

    /<name>/<taxonomy>;include=<term>,<term>,…,</term>

To include all posts not having any of the `<term>`:s of the taxonomy
`<taxonomy>`:

    /<name>/<taxonomy>;exclude=<term>,<term>,…,</term>

To use a combination of `include=…`, `exclude=…` and `<max-age>=…`,
separate them with semicolon:

    /<name>/<taxonomy>;include=<term>,<term>,…,</term>;max-age=<number-of-days>
    /<name>/<taxonomy>;exclude=<term>,<term>,…,</term>;max-age=<number-of-days>
    /<name>/<taxonomy>;include=<term>,<term>,…,</term>;exclude=<term>,<term>,…,</term>
    /<name>/<taxonomy>;include=<term>,<term>,…,</term>;exclude=<term>,<term>,…,</term>;max-age=<number-of-days>

Notice that `taxanony` is reqiured if `include=…` and/or `exclude=…` is used, and that the included and/or excluded terms must be in that taxonomy.

Example, If you for instance have named your feed `newsletter` and want all categorized
posts published within the last 14 days, you can use

    /newsletter;exclude=uncategorized;max-age=14

## Installation

Install the plugin [the usually way](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins).

You can also install it with [*GitHub Updater*](https://github.com/afragen/github-updater/archive/develop.zip), which gives you the additional benefit of keeping the plugin up to date from within its administrative interface (i.e. the usually way). Please visit its [wiki](https://github.com/afragen/github-updater/wiki) for more information.

## Frequently Asked Questions

### Where is the setting page?

Look for `Newsletter Feed` in the Settings menu.

### How do I know if there is a new version?

This plugin is currently [hosted on GitHub](https://github.com/kntnt/kntnt-newsletter-feed); one way would be to ["watch" the repository](https://help.github.com/articles/watching-and-unwatching-repositories/).

If you prefer WordPress to nag you about an update and let you update from within its administrative interface (i.e. the usually way) you must [download *GitHub Updater*](https://github.com/afragen/github-updater/archive/develop.zip) and install and activate it the usually way. Please visit its [wiki](https://github.com/afragen/github-updater/wiki) for more information. 

### How can I get help?

If you have a questions about the plugin, and cannot find an answer here, start by looking at [issues](https://github.com/kntnt/kntnt-newsletter-feed/issues) and [pull requests](https://github.com/kntnt/kntnt-newsletter-feed/pulls). If you still cannot find the answer, feel free to ask in the the plugin's [issue tracker](https://github.com/kntnt/kntnt-newsletter-feed/issues) at Github.

### How can I report a bug?

If you have found a potential bug, please report it on the plugin's [issue tracker](https://github.com/kntnt/kntnt-newsletter-feed/issues) at Github.

### How can I contribute?

Contributions to the code or documentation are much appreciated.

If you are unfamiliar with Git, please post it as a new issue on the plugin's [issue tracker](https://github.com/kntnt/kntnt-newsletter-feed/issues) at Github.

If you are familiar with Git, please do a pull request.

## Changelog

### 1.2.0

Improved the abstract plugin and settings classes and rewrote how the feed is generated.

### 1.1.1

Removed unnecessary import directive.

### 1.1.0

Changed how the feed is generated.

### 1.0.2

Changed to standard solution for fetching excerpts to be used as descriptions.

### 1.0.1

Improved information on settings page.

### 1.0.0

Initial release. Fully functional plugin.
