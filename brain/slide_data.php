<?php

    require_once 'views.php';
    require_once 'db.php';
    require_once 'log.php';

    $page = 'slides.php';


    /************************/
    /*      D A T A         */
    /************************/

    // Add a new record
    function add_slide() {
        
        $title       = filter_input(INPUT_POST, 'title');
        $author   = filter_input(INPUT_POST, 'author');
        $body   = filter_input(INPUT_POST, 'body');
        date_default_timezone_set("America/Denver");
        $date       = date('Y-m-d g:i:s a');
        
        global $log;
        global $db;
        global $page;
        
        try {
            $query = "INSERT INTO slides 
                    (title, author, body, date) 
                VALUES 
                    (:title, :author, :body, :date);";
            
            $log->log("Add Slideshow: $title, $author, $body, $date");
            
            $statement = $db->prepare($query);
            
            $statement->bindValue(':title', $title);
            $statement->bindValue(':author', $author);
            $statement->bindValue(':body', $body);
            $statement->bindValue(':date', $date);
            
            $statement->execute();
            $statement->closeCursor();
            
            header("Location: $page");
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            $log->log("**Error**: $error_message **");
            die();
        }
    }


    // Delete Database Record
    function delete_slide($id) {
        $action = filter_input(INPUT_GET, 'action');
        $id = filter_input(INPUT_GET, 'id');
        
        if ($action == 'delete' and !empty($id)) {
            $query = "DELETE from slides WHERE id = :id";
            global $db;
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
            $statement->closeCursor();
        }
        global $page;
        header("Location: $page");
    }
    

    // Lookup Record using ID
    function get_slide($id) {
        $query = "SELECT * FROM slides WHERE id = :id";
        global $db;
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $record = $statement->fetch();
        $statement->closeCursor();
        return $record;
    }


    // Query for all slides
    function query_slides () {
        $query = "SELECT * FROM slides";
        global $db;
        $statement = $db->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }


    // Update the database
    function update_slide () {
        $id = filter_input(INPUT_POST, 'id');
        $title = filter_input(INPUT_POST, 'title');
        $author = filter_input(INPUT_POST, 'author');
        $body = filter_input(INPUT_POST, 'body');
        date_default_timezone_set("America/Denver");
        $date = date('Y-m-d g:i:s a');
        
        global $log;
        global $db;       
        global $page;
        
        try {
            // Modify database row
            $query = "UPDATE slides SET 
                title=:title, author=:author, body=:body, date=:date
                WHERE id = :id";
            
            $statement = $db->prepare($query);

            $statement->bindValue(':id', $id);
            $statement->bindValue(':title', $title);
            $statement->bindValue(':author', $author);
            $statement->bindValue(':body', $body);
            $statement->bindValue(':date', $date);

            $statement->execute();
            $statement->closeCursor();

            header("Location: $page");
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            $log->log("**Error**: $error_message **");
            die();
        }
    }


    /************************/
    /*      V I E W S       */
    /************************/

    // Show form for adding a record
    function add_slide_view() {
        global $page;
        return '
            <h3>Add a slideshow</h3>
            <form action="' . $page . '" method="post">
                <p><label>Slide Title:</label> &nbsp; <input type="text" name="title"></p>
                <p><label>Slide Creator:</label> &nbsp; <input type="text" name="author"></p>
                <p><label>Slide Body:</label><br> &nbsp; <textarea name="body" rows = "16" cols = "48"></textarea></p>
                <p><input type="submit" value="Add slideshow"/></p>
                <input type="hidden" name="action" value="create">
            </form>
        ';
    }


    // Show form for adding a record
    function edit_slide_view($record) {
        
        $id = $record['id'];
        $title = $record['title'];
        $author = $record['author'];
        $body = $record['body'];
        global $page;
        return '
            <h3>Edit Slideshow</h3>
            <form action="' . $page . '" method="post">
            
                <p><label>Slide Title:</label> &nbsp; <input type="text" name="title" value="' . $title . '"></p>
                <p><label>Slide Creator:</label> &nbsp; <input type="text" name="author" value="' . $author . '"></p>
                <p><label>Slide Body: </label><br> &nbsp; <textarea name="body" rows = "16" cols = "48">' . $body . '</textarea></p>
                
                <p><input type="submit" value="Save Slideshow"/></p>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="' . $id . '">
            </form>
        ';
    }


    // Handle all action verbs
    function render_slides_view() {
        $id = filter_input(INPUT_GET, 'id');
        global $slides;
        global $log;
        global $db;
        
        // POST
        $action = filter_input(INPUT_POST, 'action');
        if ($action == 'create') {    
            $log->log('slide CREATE');                    // CREATE
            add_slide();
        }
        if ($action == 'update') {
            $log->log('slide UPDATE');                    // UPDATE
            update_slide ();
        }

        // GET
        $action = filter_input(INPUT_GET, 'action');
        if (empty($action)) {                                  
            $log->log('slide READ');                      // READ
            $intro =  render_markdown_file('slideIndex.md');
            return $intro . slide_list_view(query_slides());
        }
        if ($action == 'add') {
            $log->log('slide Add View');
            return add_slide_view();
        }
        if ($action == 'delete') {
            $log->log('slide DELETE');                    // DELETE
            return delete_slide($id);
        }
        if ($action == 'view')
        {
            $log->log('slide VIEW');
            return render_slides(get_slide($id));               // VIEW
        }
        if ($action == 'edit' and ! empty($id)) {
            $log->log('slide Edit View');
            return edit_slide_view(get_slide($id));           // EDIT
        }
    }

    function render_slides($record){
        $body = $record['body'];
        $string = '
        <html>
        <head>
            <link rel="stylesheet" href="https://revealjs.com/css/reveal.css">
            <link rel="stylesheet" href="https://revealjs.com/css/theme/black.css">
            <link rel="stylesheet" href="https://revealjs.com/lib/css/zenburn.css"/>
            <link rel="stylesheet" href="slides.css">
        </head>
        <body>
    
            <div class="reveal">
                <div class="slides">
                    <section data-markdown
                             data-separator="\n---\n" data-separator-vertical="\n--\n">
                        <textarea data-template>'.$body.'</textarea>
                    </section>
                </div>
            </div>
    
            <script src="https://revealjs.com/lib/js/head.min.js"></script>
            <script src="https://revealjs.com/js/reveal.js"></script>
            <script src="slides.js"></script>
    
        </body>
    </html>';
    file_put_contents("Shown.php", $string);
    header('Location: Shown.php');
    //return $string;
    }

    // render_table -- Create a bullet list in HTML
    function slide_list_view ($table) {
        global $page;
        $s = '<table>';
        $header = array('date', 'view/title', 'author', 'edit', 'delete');
        $s .= '<tr><th>' . implode('</th><th>', $header) . '</th></tr>';
        foreach($table as $row) {
            $date = $row['date'];
            //$view = render_link($row['title'], "renderslide.php");
            $title = $row['title'];
            $body = $row['body'];
            $view = '<a href="slides.php?id=' . $row['id'] . '&action=view">' . $row['title'] . '</a>';
            $author = $row['author'];
            $edit = render_link('Edit this slideshow', "$page?id=$row[id]&action=edit");
            $delete = render_link("Delete this slideshow", "$page?id=$row[0]&action=delete");
            $row = array($date, $view, $author, $edit, $delete);
            $s .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
        }
        $s .= '</table>';
        
        return $s;
    }

    
?>
