<?php
require_once('model/DBConnection.php');

function peekBook(){
    $db = new DBConnection('booksDB.db');
    return $db->selectOne('Books', 'WHERE id = ' . $_GET['id']);
}