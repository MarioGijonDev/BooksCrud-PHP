<?php
require_once("model/DBConnection.php");

function addBook(){

    if(isset($_SESSION['isbn']) && isset($_SESSION['title']) && isset($_SESSION['author']) && isset($_SESSION['publisher']) && isset($_SESSION['pages']) ){
        $db = new DBConnection('booksDB.db');
        $db->createBook($_SESSION);
        session_unset();
    }

}