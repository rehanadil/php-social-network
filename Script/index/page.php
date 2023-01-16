<?php
if ($isLogged)
{
    $themeData['follow_button_title'] = ($config['friends'] == 1) ? $lang['add_as_friend_button'] : $lang['follow_button_label'];
    if ($user['admin'] == 1) $themeData['nav_admin_area'] = \SocialKit\UI::view('navigation/admin-area');

    if ($config['page_allow_create'] == 1
        && $user['subscription_plan']['create_pages'] == 1) $themeData['nav_create_page'] = \SocialKit\UI::view('navigation/create-page');

    if ($config['group_allow_create'] == 1
        && $user['subscription_plan']['create_groups'] == 1) $themeData['nav_create_group'] = \SocialKit\UI::view('navigation/create-group');

    if ($config['event_allow_create'] == 1
        && $user['subscription_plan']['create_events'] == 1) $themeData['nav_create_event'] = \SocialKit\UI::view('navigation/create-event');

    $themeData['page_navigation'] = \SocialKit\UI::view('navigation/content');

    //Featured Users
    $featuredUsers = getFeaturedUsers();
    if ($featuredUsers)
    {
        $themeData['li_featured_users'] = $featuredUsers;
        $themeData['featured_users'] = \SocialKit\UI::view('featured-users/content');
    }

    // Suggestions
    $themeData['user_suggestions'] = getUserSuggestions('', 5);
    $themeData['page_suggestions'] = getPageSuggestions('', 5);
    $themeData['group_suggestions'] = getGroupSuggestions('', 5);

    // Events
    $themeData['event_bar'] = getEventBar();

    // Trending
    $themeData['trendings'] = getTrendings('popular');
}

switch ($_GET['a'])
{
    // Welcome page source
    case 'welcome':
        include('sources/welcome.php');
    break;
    
    // Email verification source
    case 'email-verification':
        include('sources/email_verification.php');
    break;
    
    // Start page source
    case 'start':
        include('sources/start.php');
    break;
    
    // Home page source
    case 'home':
        include('sources/home.php');
    break;
    
    // Messages page source
    case 'messages':
        include('sources/messages.php');
    break;
    
    // Timeline page source
    case 'timeline':
        include('sources/timeline.php');
    break;
    
    // Story page source
    case 'story':
        include('sources/story.php');
    break;

    // Album page source
    case 'album':
        include('sources/album.php');
    break;
    
    // Create page source
    case 'create-page':
        include('sources/create_page.php');
    break;
    
    // Create group page source
    case 'create-group':
        include('sources/create_group.php');
    break;

    // Create event page source
    case 'create-event':
        include('sources/create_event.php');
    break;
    
    // Hashtag page source
    case 'hashtag':
        include('sources/hashtag.php');
    break;
    
    // Search page source
    case 'search':
        include('sources/search.php');
    break;
    
    // Photos page source
    case 'photos':
        include('sources/photos.php');
    break;
    
    // Albums page source
    case 'albums':
        include('sources/albums.php');
    break;
    
    // Saved posts page source
    case 'saved-posts':
        include('sources/savedposts.php');
    break;
    
    // Boosted posts page source
    case 'boosted-posts':
        include('sources/boostedposts.php');
    break;
    
    // Events page source
    case 'events':
        include('sources/events.php');
    break;
    
    // User settings page source
    case 'settings':
        include('sources/user_settings.php');
    break;
    
    // My pages
    case 'my-pages':
        include('sources/mypages.php');
    break;
    
    // My pages
    case 'my-groups':
        include('sources/mygroups.php');
    break;
    
    // My pages
    case 'recommended':
        include('sources/recommended.php');
    break;
    
    // Terms page source
    case 'terms':
        include('sources/terms.php');
    break;
    
    // Terms page source
    case 'subscription-plans':
        include('sources/subscription_plans.php');
    break;
    
    // Terms page source
    case 'video-call':
        include('sources/video_call.php');
    break;
    
    // Logout source
    case 'logout':
        include('sources/logout.php');
    break;
}

// If no sources found
if ( empty($themeData['page_content']) )
{
    $themeData['page_content'] = \SocialKit\UI::view('welcome/error');
}