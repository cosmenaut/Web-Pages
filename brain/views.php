<?php
    require_once 'Parsedown.php';


    // article_settings -- Create the HTML page for one article.
    function article_settings($title, $body, $author) {
        return array(
            "title" => $title, 
            "body" => $body, 
            "author" => $author
        );
    }
    

    // render_article -- Create the HTML page for one article.
    function render_article($title, $paragraphs) {
        $body = implode('</p><p>', $paragraphs);
        $settings = array('title' => $title, 'body' => $body);
        $text = transform_text('<h3>{{ title }}</h3><p>{{ body }}</p>', $settings);
        return $text;
    }


    // render_button -- Show a styled button
    function render_button($text, $url) {
        return "<button class=\"btn btn-primary btn-large m-20\" href=\"$url\">$text</button>";
    }


    // render_card -- create HTML for visual card
    function render_card($title, $body) {
        $template = '
            <div class="card">

                <div class="card-header">
                    {{ title }}
                </div>

                <div class="card-body card-padding">
                    {{ body }}
                </div>
            </div>';
        $settings = array('title' => $title, 'body' => $body);
        return transform_text($template, $settings);
    }  


    // render_cards -- create HTML for visual card
    function render_cards($cards) {
        $a = '<div class="container-fluid"><div class="row">';
        $b = '</div></div>';
        $content = array();
        foreach($cards as $title => $body) {
            $content[$title] = render_card($title, $body);
        }
        return $a . render_page_content($content) . $b;
    }

        
    // render_csv  -- Create csv text output
    function render_csv($list) {
        $s = '';
        foreach($list as $row) {
            $s .= implode(',', $row) . "\n";
        }
        return $s;
    }


    // render_links -- Create a bullet list of hyperlinks in HTML
    function render_links($list) {
        $s = '<ul>';
        foreach($list as $text => $url) {
            $s .= "<li>" . render_link($text, $url) . "</li>";
        }
        $s .= '</ul>';
        return $s;
    }

    
    // render_links_card -- Create a card with a collection of links
    function render_links_card($title, $description, $links) {
        $links = render_links($links);
        return render_card($title,"<p>$description</p>$links");
    }


    // render_link -- Create a hyperlink in HTML
    function render_link($text, $url) {
        return "<a href=\"$url\">$text</a>";
    }

 
    // render_list -- Create a bullet list in HTML
    function render_list($list) {
        $s = '<ul>';
        foreach($list as $i) {
            $s .= "<li>$i</li>";
        }
        $s .= '</ul>';
        return $s;
    }

    
    // render_page -- Create one HTML page from a template.
    function render_page($settings) {
        header("Pragma: no-cache");
        header("Expires: 0");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        return render_template("page.html", $settings);
    }


    // render_page_content -- Create page content from cards
    function render_page_content($cards) {
        $a = '';
        $b = '';
        return $a . implode("$b$a", $cards) . $b;
    }


    // render_markdown -- Convert markdown text to HTML
    function render_markdown($markdown) {
        $Parsedown = new Parsedown();
        return $Parsedown->text($markdown);
    }


    // render_markdown_file -- Convert a file to HTML
    function render_markdown_file($path) {
        return render_markdown(read_file($path));
    }


    // render_source_code -- Display the source code from a file
    function render_source_code($path) {
        return '<pre>' . htmlspecialchars(file_get_contents($path)) . '</pre>';
    }

    
    // render_table -- Create a bullet list in HTML
    function render_table($table) {
        $s = '<table>';
        foreach($table as $row) {
            $s .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
        }
        $s .= '</table>';
        return $s;
    }


    // render_template -- Convert template file using the settings
    function render_template($template, $settings) {
        $text = file_get_contents($template); 
        $text = transform_text($text, $settings);
        return $text;
    }

   
    // transform_text -- Convert each setting in a block of text
    function transform_text ($text, $settings) {
        foreach ($settings as $key => $value) {
            $text = str_replace("{{ $key }}",  $value,  $text);
        }
        return $text;
    }


?>
