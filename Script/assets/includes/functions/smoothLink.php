<?php
/* Smooth Link */
function smoothLink($url='')
{
    global $config;

    $urls = array(
        '/^index\.php\?a=welcome&b=forgot_password$/i',
        '/^index\.php\?a=welcome&b=password_reset&id=([A-Za-z0-9_]+)$/i',
        '/^index\.php\?a=welcome$/i',
        '/^index\.php\?a=subscription\-plans&b=upgrade$/i',
        '/^index\.php\?a=start$/i',
        '/^index\.php\?a=home$/i',
        '/^index\.php\?a=saved\-posts$/i',
        '/^index\.php\?a=boosted\-posts$/i',
        '/^index\.php\?a=photos$/i',
        '/^index\.php\?a=albums$/i',
        '/^index\.php\?a=recommended&b=([A-Za-z0-9_]+)$/i',
        '/^index\.php\?a=more$/i',
        '/^index\.php\?a=create\-page$/i',
        '/^index\.php\?a=create\-group$/i',
        '/^index\.php\?a=create\-event$/i',
        '/^index\.php\?a=my\-groups$/i',
        '/^index\.php\?a=my\-pages$/i',
        '/^index\.php\?a=video\-call&id=([A-Za-z0-9_]+)$/i',
        '/^index\.php\?a=terms&b=([A-Za-z0-9_]+)$/i',
        '/^index\.php\?a=terms$/i',
        '/^index\.php\?a=event$/i',
        '/^index\.php\?a=events&b=search&query=([^\/]+)$/i',
        '/^index\.php\?a=events&b=([^\/]+)$/i',
        '/^index\.php\?a=create\-event$/i',
        '/^index\.php\?a=timeline&type=event&id=([^\/]+)$/i',
        '/^index\.php\?a=album&b=([^\/]+)$/i',
        '/^index\.php\?a=messages&recipient_id=([^\/]+)$/i',
        '/^index\.php\?a=messages$/i',
        '/^index\.php\?a=story&id=([^\/]+)$/i',
        '/^index\.php\?a=search&query=([^\/]+)$/i',
        '/^index\.php\?a=search$/i',
        '/^index\.php\?a=hashtag&query=([^\/]+)$/i',
        '/^index\.php\?a=hashtag$/i',
        '/^index\.php\?a=settings&b=([^\/]+)$/i',
        '/^index\.php\?a=settings$/i',
        '/^index\.php\?a=logout$/i',
        '/^social\-login\.php\?site=([^\/]+)$/i',
        '/^index\.php\?a=timeline&b=([^\/]+)&c=([^\/]+)&recipient_id=([^\/]+)&id=([^\/]+)$/i',
        '/^index\.php\?a=timeline&b=([^\/]+)&c=([^\/]+)&id=([^\/]+)$/i',
        '/^index\.php\?a=timeline&b=([^\/]+)&id=([^\/]+)$/i',
        '/^index\.php\?a=timeline&id=([^\/]+)$/i'
    );

    $mods = array(
        $config['site_url'] . '/forgot-password',
        $config['site_url'] . '/password-reset/$1',
        $config['site_url'] . '/welcome',
        $config['site_url'] . '/subscription-plans/upgrade',
        $config['site_url'] . '/start',
        $config['site_url'] . '/home',
        $config['site_url'] . '/saved-posts',
        $config['site_url'] . '/boosted-posts',
        $config['site_url'] . '/photos',
        $config['site_url'] . '/albums',
        $config['site_url'] . '/recommended/$1',
        $config['site_url'] . '/more',
        $config['site_url'] . '/create-page',
        $config['site_url'] . '/create-group',
        $config['site_url'] . '/create-event',
        $config['site_url'] . '/my-groups',
        $config['site_url'] . '/my-pages',
        $config['site_url'] . '/video-call/$1',
        $config['site_url'] . '/terms/$1',
        $config['site_url'] . '/terms',
        $config['site_url'] . '/events',
        $config['site_url'] . '/events/search/$1',
        $config['site_url'] . '/events/$1',
        $config['site_url'] . '/event/new',
        $config['site_url'] . '/event/$1',
        $config['site_url'] . '/album/$1',
        $config['site_url'] . '/messages/$1',
        $config['site_url'] . '/messages',
        $config['site_url'] . '/story/$1',
        $config['site_url'] . '/search/$1',
        $config['site_url'] . '/search',
        $config['site_url'] . '/hashtag/$1',
        $config['site_url'] . '/hashtag',
        $config['site_url'] . '/settings/$1',
        $config['site_url'] . '/settings',
        $config['site_url'] . '/logout',
        $config['site_url'] . '/social-login/$1',
        $config['site_url'] . '/$4/$1/$2/$3',
        $config['site_url'] . '/$3/$1/$2',
        $config['site_url'] . '/$2/$1',
        $config['site_url'] . '/$1'
    );
    
    if ($config['smooth_links'] == 1)
    {
        $url = preg_replace($urls, $mods, $url);
    }
    else
    {
        $url = $config['site_url'] . '/' . $url;
    }
    
    return $url;
}