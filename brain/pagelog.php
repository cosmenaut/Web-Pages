<?php

    require_once 'views.php';
    require_once 'log.php';


    // Show buttons to go to other pages
    $content = render_button('Brain', 'index.php');

    
    // Page Content
    $log->handle_actions();
    $log->log_page();


    // Show page history
    $content .=  $log->show_log();
  

    // Add form
    $content .=  $log->show_add('pagelog.php');


    // Show Page
    $settings = array(
        "site_title" => "Brain",
        "page_title" => "Page Loading History", 
        'logo'       => 'Bear.png',
        "style"      => 'https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css',
        "content"    => $content);

    echo render_page($settings);

?>
