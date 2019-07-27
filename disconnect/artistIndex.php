<?php
    require_once 'artists.php';
    require_once 'views.php';

    $content .= $subscribers->handle_actions();


    // Create main part of page content
    $settings = array(
        "site_title" => "Artist Index",
        "page_title" => "Northern Colorado Hardcore/Punk/Metal/Alt",
        "style"      => 'style.css',
        "content"    => $content);

    echo render_page($settings);

?>
