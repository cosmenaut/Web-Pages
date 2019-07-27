<?php

    require_once 'views.php'; 
    require_once 'log.php';
    require_once 'files.php';
    require_once 'slide_data.php';
    
    
    // Page content

    $content = render_slides_view();
    
    // Create main part of page content
    $settings = array(
        "site_title" => "Exterior Brain",
        "page_title" => "Slideshow App", 
        "style"      => 'https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css',
        "content"    => $content);

    echo render_page($settings);

?>