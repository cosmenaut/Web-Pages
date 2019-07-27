<?php
    //database connection
    require_once 'db.php';
    
    // Add a new record
    function add_subscriber($db) {
        try {
            $artist  = filter_input(INPUT_POST, 'artist');
            $genre = filter_input(INPUT_POST, 'genre');
            $image  = filter_input(INPUT_POST, 'image');
            $description  = filter_input(INPUT_POST, 'description');
            $query = "INSERT INTO music (artist, genre, image, description,) VALUES (:artist,:genre,:image,:description,);";
            $statement = $db->prepare($query);
            $statement->bindValue(':artist', $artist);
            $statement->bindValue(':genre', $genre);
            $statement->bindValue(':image', $image);
            $statement->bindValue(':description', $description);
            $statement->execute();
            $statement->closeCursor();
            header('Location: artistIndex.php');
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            echo "<p>Error: $error_message</p>";
            die();
        }
    }

    // Show form for adding a record
    function add_subscriber_view() {
        return '
            <div class="card">
                <h3>Add Album</h3>
                <form action="artistIndex.php" method="post">
                    <p><label>artist:</label> &nbsp; <input type="text" name="artist"></p>
                    <p><label>genre:</label> &nbsp; <input type="text" name="genre"></p>
                    <p><label>image:</label> &nbsp; <input type="text" name="image"></p>
                    <p><label>description:</label> &nbsp; <input type="text" name="description"></p>
                    <p><input type="submit" value="Add Album"/></p>
                    <input type="hidden" name="action" value="create">
                </form>
            </div>
        ';
    }


    // Delete Database Record
    function delete_subscriber($db, $id) {
        $action = filter_input(INPUT_GET, 'action');
        $id = filter_input(INPUT_GET, 'id');
        if ($action == 'delete' and !empty($id)) {
            $query = "DELETE from music WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
            $statement->closeCursor();
        }
        header('Location: artistIndex.php');
    }
    

    // Show form for adding a record
    function edit_subscriber_view($record) {
        $id    = $record['id'];
        $artist  = $record['artist'];
        $genre = $record['genre'];
        $image  = $record['image'];
        $description  = $record['description'];
        return '
            <div class="card">
                <h3>Edit Album</h3>
                <form action="artistIndex.php" method="post">
                
                    <p><label>artist:</label> &nbsp; <input type="text" name="artist" value="' . $artist . '"></p>
                    <p><label>genre:</label> &nbsp; <input type="text" name="genre" value="' . $genre . '"></p>
                    <p><label>image:</label> &nbsp; <input type="text" genre="image" value="' . $image . '"></p>
                    <p><label>description:</label> &nbsp; <input type="text" name="description" value="' . $description . '"></p>

                    <p><input type="submit" value="Save Record"/></p>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="' . $id . '">
                </form>
            </div>
        ';
    }


    // Lookup Record using ID
    function get_subscriber($db, $id) {
        $query = "SELECT * FROM music WHERE id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->execute();
        $record = $statement->fetch();
        $statement->closeCursor();
        return $record;
    }


    // Handle all action verbs
    function handle_actions() {
        $id = filter_input(INPUT_GET, 'id');
        global $subscribers;
        // POST
        $action = filter_input(INPUT_POST, 'action');
        if ($action == 'create') {    
            $subscribers->add();
        }
        if ($action == 'update') {
            $subscribers->update();
        }

        // GET
        $action = filter_input(INPUT_GET, 'action');
        if (empty($action)) {                                  
            return $subscribers->list_view();
        }
       if ($action == 'add') {
            return $subscribers->add_view();
        }
        if ($action == 'clear') {
            return $subscribers->clear();
        }
        if ($action == 'delete') {
            return $subscribers->delete($id);
        }
        if ($action == 'edit' and ! empty($id)) {
            return $subscribers->edit_view($id);
        }
    }
       

    // Query for all subscribers
    function query_subscribers ($db) {
        $query = "SELECT * FROM music";
        $statement = $db->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }


    // render_table -- Create a bullet list in HTML
    function subscriber_list_view ($table) {
        $s = render_button('Add music', 'artistIndex.php?action=add') . '<br><br>';
        $s .= '<table>';
        $s .= '<tr><th>Band</th><th>Genre</th><th></th></tr>';
        foreach($table as $row) {
            $edit = render_link($row[1], "artistIndex.php?id=$row[0]&action=edit");
            $genre = $row[2];
            $imgpart = "<img src='".$row['3']."' style='width:200px; height:200px'>";
            $image = $imgpart;
            $buy = $row[4];
            $delete = render_link("delete", "artistIndex.php?id=$row[0]&action=delete");
            $row = array($edit, $genre, $image, $delete);
            $s .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
        }
        $s .= '</table>';
        
        return $s;
    }


    // Update the database
    function update_subscriber ($db) {
        $id    = filter_input(INPUT_POST, 'id');
        $artist  = filter_input(INPUT_POST, 'artist');
        $genre = filter_input(INPUT_POST, 'genre');
        
        // Modify database row
        $query = "UPDATE music SET artist = :artist, genre = :genre WHERE id = :id";
        $statement = $db->prepare($query);

        $statement->bindValue(':id', $id);
        $statement->bindValue(':artist', $artist);
        $statement->bindValue(':genre', $genre);

        $statement->execute();
        $statement->closeCursor();
        
        header('Location: artistIndex.php');
    }
 

    /* -------------------------------------------------------------
    
                        S U B S C R I B E R S
    
     ------------------------------------------------------------- */

    // My Subscriber list
    class Subscribers {

        // Database connection
        private $db;

        
        // Automatically connect
        function __construct() {
            global $db;
            $this->db =  $db;
        }

        
        // CRUD
        
        function add() {
            return add_subscriber ($this->db);
        }
        
        function query() {
            return query_subscribers($this->db);
        }
        
    
        function clear() {
            return clear_subscribers($this->db);
        }
        
        function delete() {
            delete_subscriber($this->db, $id);
        }
        
        function get($id) {
            return get_subscriber($this->db, $id);
        }
        
        function update() {
            update_subscriber($this->db);
        }
        
        
        // Views
        
        function handle_actions() {
            return handle_actions();
        }
        
        function add_view() {
            return add_subscriber_view();
        }
        
        function edit_view($id) {
            return edit_subscriber_view($this->get($id));
        }
        
        function list_view() {
            return subscriber_list_view($this->query());
        }
        
    }


    // Create a list object and connect to the database
    $subscribers = new Subscribers;
?>
