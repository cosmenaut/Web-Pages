<?php
    // files.php -- Utility functions for files


    // get_file_list
    function get_file_list($path) {
        $files = array();
        if (!is_dir($path)) { return $files; }

        $items = scandir($path);
        foreach ($items as $item) {
             $item_path = $path . DIRECTORY_SEPARATOR . $item;
             if (is_dir($item_path)) {
                 $files[] = $item_path;
             }
        }
        return $files;
    }


    // get_dir_list
    function get_dir_list($path) {
        $files = array();
        if (!is_dir($path)) { return $files; }

        $items = scandir($path);
        foreach ($items as $item) {
             $item_path = $path . DIRECTORY_SEPARATOR . $item;
             if (is_dir($item_path)) {
                 $files[] = $item_path;
             }
        }
        return $files;
    }


    // read_file
    // $text = readfile('index.php');
    function read_file($path) {
        $text = file_get_contents($path);
        $text = htmlspecialchars($text);
        return $text;
    }


    // file_put_contents('/ex04.php', read_file('index.php'));

    
?>