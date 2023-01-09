<?php
require_once('model/DBConnection.php');

function editBook(){

    if(isset($_SESSION['id']) && isset($_SESSION['isbn']) && isset($_SESSION['title']) && isset($_SESSION['author']) && isset($_SESSION['publisher']) && isset($_SESSION['pages']) ){
        $db = new DBConnection('booksDB.db');
        $db->updateBook($_SESSION);
        session_unset();
    }else{
        header("location: index.php?edit&id=");
    }
    
}