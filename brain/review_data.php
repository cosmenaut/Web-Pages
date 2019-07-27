<?php

    require_once 'views.php';
    require_once 'db.php';
    require_once 'log.php';

    $page = 'review.php';


    /************************/
    /*      D A T A         */
    /************************/

    // Add a new record
    function add_review() {
        
        $page       = filter_input(INPUT_POST, 'page');
        $reviewer   = filter_input(INPUT_POST, 'reviewer');
        $designer   = filter_input(INPUT_POST, 'designer');
        $scorecard  = filter_input(INPUT_POST, 'scorecard');
        $score      = filter_input(INPUT_POST, 'score');
        date_default_timezone_set("America/Denver");
        $date       = date('Y-m-d g:i a');
        
        global $log;
        global $db;
        global $page;
        
        try {
            $query = "INSERT INTO reviews 
                    (page, reviewer, designer, date, scorecard, score) 
                VALUES 
                    (:page, :reviewer, :designer, :date, :scorecard, :score);";
            
            $log->log("Add Record: $date, $page, $reviewer, $designer, $score");
            
            $statement = $db->prepare($query);
            
            $statement->bindValue(':page',      $page);
            $statement->bindValue(':reviewer',  $reviewer);
            $statement->bindValue(':designer',  $designer);
            $statement->bindValue(':date',      $date);
            $statement->bindValue(':scorecard', $scorecard);
            $statement->bindValue(':score',     $score);
            
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
    function delete_review($id) {
        global $db;
        global $page;
        
        $action = filter_input(INPUT_GET, 'action');
        $id = filter_input(INPUT_GET, 'id');
        
        if ($action == 'delete' and !empty($id)) {
            $query = "DELETE from reviews WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
            $statement->closeCursor();
        }
        header("Location: $page");
    }
    

    // Lookup Record using ID
    function get_review($id) {
        $query = "SELECT * FROM reviews WHERE id = :id";
        global $db;
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $record = $statement->fetch();
        $statement->closeCursor();
        return $record;
    }


    // Query for all reviews
    function query_reviews () {
        $query = "SELECT * FROM reviews";
        global $db;
        $statement = $db->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }


    // Update the database
    function update_review () {
        $id         = filter_input(INPUT_POST, 'id');
        $page       = filter_input(INPUT_POST, 'page');
        $reviewer   = filter_input(INPUT_POST, 'reviewer');
        $designer   = filter_input(INPUT_POST, 'designer');
        $scorecard  = filter_input(INPUT_POST, 'scorecard');
        $score      = filter_input(INPUT_POST, 'score');
        date_default_timezone_set("America/Denver");
        $date       = date('Y-m-d g:i a');
        
        global $log;
        global $db;       
        global $page;
        
        try {
            // Modify database row
            $query = "UPDATE reviews SET 
                page=:page, reviewer=:reviewer, designer=:designer, date=:date, scorecard=:scorecard, score=:score 
                WHERE id = :id";
            
            $statement = $db->prepare($query);

            $statement->bindValue(':id',        $id);
            $statement->bindValue(':page',      $page);
            $statement->bindValue(':reviewer',  $reviewer);
            $statement->bindValue(':designer',  $designer);
            $statement->bindValue(':date',      $date);
            $statement->bindValue(':scorecard', $scorecard);
            $statement->bindValue(':score',     $score);

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
    function add_review_view() {
        global $page;
        return '
            <h3>Add review</h3>
            <form action="' . $page . '" method="post">
                <p><label>page:</label> &nbsp; <input type="text" name="page"></p>
                <p><label>designer:</label> &nbsp; <input type="text" name="designer"></p>
                <p><label>reviewer:</label> &nbsp; <input type="text" name="reviewer"></p>
                <p><label>scorecard:</label> &nbsp; <textarea name="scorecard" rows="20" cols="80"></textarea></p>
                <p><label>score:</label> &nbsp; <input type="int" name="score"></p>
                <p><input type="submit" value="Add review"/></p>
                <input type="hidden" name="action" value="create">
            </form>
        ';
    }


    // Show form for adding a record
    function edit_review_view($record) {
        
        $id         = $record['id'];
        $page       = $record['page'];
        $reviewer   = $record['reviewer'];
        $designer   = $record['designer'];
        $scorecard  = $record['scorecard'];
        $score      = $record['score'];
        
        global $page;
        return '
            <h3>Edit review</h3>
            <form action="' . $page . '" method="post">
            
                <p><label>page:</label> &nbsp; <input type="text" name="page" value="' . $page . '"></p>
                <p><label>designer:</label> &nbsp; <input type="text" name="designer" value="' . $designer . '"></p>
                <p><label>reviewer:</label> &nbsp; <input type="text" name="reviewer" value="' . $reviewer . '"></p>
                <p><label>scorecard:</label> &nbsp; <textarea name="scorecard">' . $scorecard . '</textarea></p>
                <p><label>score:</label> &nbsp; <input type="int" name="score" value="' . $score . '"></p>
                
                <p><input type="submit" value="Save Record"/></p>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="' . $id . '">
            </form>
        ';
    }


    // Handle all action verbs
    function render_reviews_view() {
        $id = filter_input(INPUT_GET, 'id');
        global $reviews;
        global $log;
        global $db;
        
        // POST
        $action = filter_input(INPUT_POST, 'action');
        if ($action == 'create') {    
            $log->log('review CREATE');                    // CREATE
            add_review();
        }
        if ($action == 'update') {
            $log->log('review UPDATE');                    // UPDATE
            update_review ();
        }

        // GET
        $action = filter_input(INPUT_GET, 'action');
        if (empty($action)) {                                  
            $log->log('review READ');                      // READ
            $intro =  render_markdown_file('reviews.md');
            return $intro . review_list_view(query_reviews());
        }
        if ($action == 'add') {
            $log->log('review Add View');
            return add_review_view();
        }
        if ($action == 'delete') {
            $log->log('review DELETE');                    // DELETE
            return delete_review($id);
        }
        if ($action == 'edit' and ! empty($id)) {
            $log->log('review Edit View');
            return edit_review_view(get_review($id));
        }
        if ($action == 'view' and ! empty($id)) {
            $log->log('review Details View');               // READ
            return edit_review_view(get_review($id));
        }
    }


    // render_table -- Create a bullet list in HTML
    function review_list_view ($table) {
        global $page;
        $s = '<table>';
        $header = array('id', 'date', 'page', 'review', 'score', 'edit', 'delete');
        $s .= '<tr><th>' . implode('</th><th>', $header) . '</th></tr>';
        foreach($table as $row) {
            $id = $row['id'];
            $url = render_link($row['page'], "$page?id=$row[id]&action=view");
            $date = $row['date'];
            $reviewer = $row['reviewer'];
            $score = $row['score'];
            $edit = render_link('edit', "$page?id=$row[id]&action=edit");
            $delete = render_link("delete", "$page?id=$row[0]&action=delete");
            $row = array($id, $date, $url, $reviewer, $score, $edit,  $delete);
            $s .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
        }
        $s .= '</table>';
        
        return $s;
    }


?>
