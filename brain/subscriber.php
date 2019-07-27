<?php

    // Create a database connection
    require_once 'db.php';
    require_once 'log.php';

    $page = 'email_list.php';


    // Add a new record
    function add_subscriber($db) {
        try {
            $name  = filter_input(INPUT_POST, 'name');
            $email = filter_input(INPUT_POST, 'email');
            $query = "INSERT INTO subscribers (name, email) VALUES (:name, :email);";
            $statement = $db->prepare($query);
            $statement->bindValue(':name', $name);
            $statement->bindValue(':email', $email);
            $statement->execute();
            $statement->closeCursor();
            global $page;
            header("Location: $page");
        } catch (PDOException $e) {
            $error_message = $e->getMessage();
            echo "<p>Error: $error_message</p>";
            die();
        }
    }

    // Show form for adding a record
    function add_subscriber_view() {
        global $page;
        return '
            <div class="card">
                <h3>Add Subscriber</h3>
                <form action="' . $page . '" method="post">
                    <p><label>Name:</label> &nbsp; <input type="text" name="name"></p>
                    <p><label>Email:</label> &nbsp; <input type="text" name="email"></p>
                    <p><input type="submit" value="Sign Up"/></p>
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
            $query = "DELETE from subscribers WHERE id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id);
            $statement->execute();
            $statement->closeCursor();
        }
        global $page;
        header("Location: $page");
    }
    

    // Show form for adding a record
    function edit_subscriber_view($record) {
        $id    = $record['id'];
        $name  = $record['name'];
        $email = $record['email'];
        global $page;
        return '
            <div class="card">
                <h3>Edit Subscriber</h3>
                <form action="' . $page . '" method="post">
                    <p><label>Name:</label> &nbsp; <input type="text" name="name" value="' . $name . '"></p>
                    <p><label>Email:</label> &nbsp; <input type="text" name="email" value="' . $email . '"></p>
                    <p><input type="submit" value="Save Record"/></p>
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="' . $id . '">
                </form>
            </div>
        ';
    }


    // Lookup Record using ID
    function get_subscriber($db, $id) {
        $query = "SELECT * FROM subscribers WHERE id = :id";
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
        global $log;

        // POST
        $action = filter_input(INPUT_POST, 'action');
        if ($action == 'create') {    
            $log->log('Subscriber CREATE');                    // CREATE
            $subscribers->add();
        }
        if ($action == 'update') {
            $log->log('Subscriber UPDATE');                    // UPDATE
            $subscribers->update();
        }

        // GET
        $action = filter_input(INPUT_GET, 'action');
        if (empty($action)) {                                  
            $log->log('Subscriber READ');                      // READ
            return $subscribers->list_view();
        }
       if ($action == 'add') {
            $log->log('Subscriber Add View');
            return $subscribers->add_view();
        }
        if ($action == 'clear') {
            $log->log('Subscriber DELETE ALL');
            return $subscribers->clear();
        }
        if ($action == 'delete') {
            $log->log('Subscriber DELETE');                    // DELETE
            return $subscribers->delete($id);
        }
        if ($action == 'edit' and ! empty($id)) {
            $log->log('Subscriber Edit View');
            return $subscribers->edit_view($id);
        }
    }
       

    // Query for all subscribers
    function query_subscribers ($db) {
        $query = "SELECT * FROM subscribers";
        $statement = $db->prepare($query);
        $statement->execute();
        return $statement->fetchAll();
    }


    // render_table -- Create a bullet list in HTML
    function subscriber_list_view ($table) {
        global $page;
        $s = render_button('Add Subscriber', "$page?action=add") . '<br><br>';
        $s .= '<table>';
        $s .= '<tr><th>Name</th><th>Email</th></tr>';
        foreach($table as $row) {
            $edit = render_link($row[1], "$page?id=$row[0]&action=edit");
            $email = $row[2];
            $delete = render_link("delete", "$page?id=$row[0]&action=delete");
            $row = array($edit, $email, $delete);
            $s .= '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
        }
        $s .= '</table>';
        
        return $s;
    }


    // Update the database
    function update_subscriber ($db) {
        $id    = filter_input(INPUT_POST, 'id');
        $name  = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email');
        
        // Modify database row
        $query = "UPDATE subscribers SET name = :name, email = :email WHERE id = :id";
        $statement = $db->prepare($query);

        $statement->bindValue(':id', $id);
        $statement->bindValue(':name', $name);
        $statement->bindValue(':email', $email);

        $statement->execute();
        $statement->closeCursor();
        
        global $page;
        header("Location: $page");
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
