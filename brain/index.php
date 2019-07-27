<?php
//fix page log
//fix add review
    require_once 'views.php';
    require_once 'db.php';    
    require_once 'files.php';
    require_once 'Parsedown.php';
    require_once 'auth.php';

    

    // Markdown Text
    $markdown = read_file('brain.md');

    // Convert the Markdown into HTML
    $Parsedown = new Parsedown();
    $content = $Parsedown->text($markdown);
    

    // Create main part of page content
    $settings = array(
        "site_title" => "Exterior Brain",
        "page_title" => "cosmenaut", 
        "content"    => $content);

    echo render_page($settings);
?>
