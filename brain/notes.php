<?php

    require_once 'views.php'; 
    require_once 'log.php';
    require_once 'files.php';
    require_once 'notes_data.php';
    
    
    // Page content

    $notes =  render_notes_view();

    $content = render_markdown_file('notes.md') . $notes;
    
    // Create main part of page content
    $settings = array(
        "site_title" => "Exterior Brain",
        "page_title" => "Notes App", 
        "style"      => 'https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css',
        "content"    => $content);

    echo render_page($settings);

?>
