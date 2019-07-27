<?php

    require_once 'files.php'; 
    require_once 'log.php';
    require_once 'notes_data.php';
    require_once 'views.php'; 
    

    // Page content

    $text = 'Experiment with page rendering, before proceeding to the next steps.';

    $preform = 'render() <pre> function render() { do_this(); }</pre>';

    $markdown = render_markdown("* Bullet lists\n* Styles\n* Headings\n* [Hyperlinks](index.php)");

    $notes = note_list_view(query_notes());

    $source = 'page.html <pre>' . read_file("page.html") . '</pre>';
        

    // Assemble all the cards
    $content = render_cards(array(
        'Page Rendering' => $text,
        'Preformatted Text' => $preform,
        'Markdown' => $markdown,
        'Notes' => $notes,
        'Source Code' => $source
    ));
        


    // Create main part of page content
    $settings = array(
        "site_title" => "Exterior Brain",
        "page_title" => "Test of Dev Features", 
        "content"    => $content);

    echo render_page($settings);

?>
