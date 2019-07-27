<?php
    require_once 'views.php';
    require_once 'Parsedown.php';
    require_once 'files.php';

    // Markdown Text
    $markdown = read_file('art.md');

    // Convert the Markdown into HTML
    $Parsedown = new Parsedown();
    $content = $Parsedown->text($markdown);
    

    // Create main part of page content
    $settings = array(
//        "site_title" => "",
        "page_title" => "Cosmenaut Designs", 
        "content"    => $content);

    echo render_page($settings);
?>
