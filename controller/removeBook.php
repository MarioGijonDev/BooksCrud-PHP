<?php
require_once("model/DBConnection.php");

function removeBook(){
    $db = new DBConnection('booksDB.db');
    $db->removeBook($_GET['id']);
    header('Location:index.php');
    exit();
}

function getTitleBook(){
    $db = new DBConnection('booksDB.db');
    return $db->selectOne('Books', 'WHERE id = '. $_GET['id'])['title'];
}