<?php
require_once('model/DBConnection.php');
function getBooksForTable(){
    $db = new DBConnection('booksDB.db');
    if(!$db->checkIfTableExists('Books'))
        $db->executeSQL(file_get_contents("model/Books.sql"));
    if(isset($_GET["order"]))
        $books = $db->selectAll("Books", [$_GET['columnOrder'], $_GET['orderOption']]);
    else
        $books = $db->selectAll("Books");

    $booksInPages = array_chunk($books, 10);

    foreach ($booksInPages[$_GET['page'] ?? 0] as $book) { ?>
        <tr>
            <td><?= $book['isbn'] ?></td>
            <td><?= $book['title'] ?></td>
            <td><?= $book['author'] ?></td>
            <td><?= $book['publisher'] ?></td>
            <td><?= $book['pages'] ?></td>
            <td scope="col" style="width: 17%">
                <a name="" id="" class="btn btn-success" href="?edit&id=<?= $book['id'] ?>" role="button">Edit</a>
                <a name="" id="" class="btn btn-danger" href="?remove&id=<?= $book['id'] ?>" role="button">Remove</a>
                <a name="" id="" class="btn btn-warning" href="?peek&id=<?= $book['id'] ?>" role="button">Peek</a>
            </td>
        </tr>
<?php }
}

function checkPage(){
    if(isset($_GET["order"]))
        return "?columnOrder=".$_GET["columnOrder"]."&orderOption=".$_GET["orderOption"]."&order";
    else
        return "?columnOrder=id&orderOption=ASC&order";
}

function checkPreviousState(){
    if(!isset($_GET["page"]))
        return "disabled";
    if($_GET["page"] == 0)
        return "disabled";
}

function checkPreviousPage(){
    if(checkPreviousState() != "disabled"){
        if($_GET['page'] == 1)
            return checkPage();
        else
            return "?columnOrder=".$_GET["columnOrder"]."&orderOption=".$_GET["orderOption"]."&order&page=".--$_GET['page'];
    }
}

function checkNextState(){
    if(isset($_GET['page'])){
        if($_GET["page"] == 98){
            return "disabled";
        }else{
            return "";
        }
    }
}

function checkNextPage(){
    if(isset($_GET['page'])){
        if(checkNextState() == ""){
            return "?columnOrder=".$_GET["columnOrder"]."&orderOption=".$_GET["orderOption"]."&order&page=".$_GET['page']+=2;
        }
    }else{
        return checkPage()."&page=1";
    }
}