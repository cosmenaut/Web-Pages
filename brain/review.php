<?php

    require_once 'views.php'; 
    require_once 'files.php';
    require_once 'review_data.php';
    
    
    // Page content

    $content = render_reviews_view();
    
    // Create main part of page content
    $settings = array(
        "site_title" => "Exterior Brain",
        "page_title" => "Design Review App", 
        "style"      => 'https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css',
        "content"    => $content);

    echo render_page($settings);

?>
