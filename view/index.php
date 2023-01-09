<?php
require_once('controller/showBookList.php');
require_once('controller/editBook.php');
require_once('controller/removeBook.php');
require_once('controller/peekBook.php');
require_once('controller/addBook.php');
require_once('controller/generatePDF.php');

session_start();

if(isset($_GET['records'])){
    if(isset($_SESSION['pdfData'])){
        $pdfData=$_SESSION['pdfData'];
        header("Location: index.php?columnOrder=".$pdfData['columnOrder']."&orderOption=".$pdfData['orderOption']."&getpdf&records=".htmlentities($_GET['records']));
    }
    if(isset($_GET['getpdf'])){
        generatePDF();
    }
}

if(isset($_GET['getpdf'])){
    if(!isset($_GET['records'])){
        $_SESSION['pdfData'] = $_GET;
    ?>
        
        <head>
            <meta charset="utf-8">
            <title>Generate PDF</title>
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
            <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> 
            <script>
                $(document).ready(function()
                {
                    $("#mostrarmodal").modal("show");
                });
            </script>
        </head>
        <body>
        <div class="modal fade" id="mostrarmodal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <form action="">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                    <a href="index.php"><button type="button" class="close" aria-hidden="true">&times;</button></a>
                        <h3>Are you sure you want to remove...</h3>
                        <input class="form-control" type="text" name="records" id="records">
                </div>
                <div class="modal-body">
                    <h4>All by default</h4>
                </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Get PDF</button>
                    </div>
                </div>
            </form>
            
        </div>
        </div>
        </body>
        </html>


    <?php
        exit();
    }
}

if(!isset($_GET['edit']) && !isset($_GET['remove']) && !isset($_GET['peek']) && !isset($_GET['add'])){

    if(strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'){
        if(checkInputs()){
            $_SESSION['isbn'] = htmlentities($_POST['isbn']);
            $_SESSION['title'] = htmlentities($_POST['title']);
            $_SESSION['author'] = htmlentities($_POST['author']);
            $_SESSION['publisher'] = htmlentities($_POST['publisher']);
            $_SESSION['pages'] = htmlentities($_POST['pages']);
            if(isset($_GET['edited'])){
                $_SESSION['edited'] = "";
            }else{
                if(isset($_GET['added']))
                    $_SESSION['added'] = "";
            }

            header('location:index.php');
        }
    }else{

        if(strtoupper($_SERVER['REQUEST_METHOD']) === 'GET'){

            if(isset($_SESSION['edited']))
                editBook();
            if(isset($_SESSION['added']))
                addBook();
            
            showIndex();
            session_unset();
            
        }

    }

}else{
    if(isset($_GET['edit'])){
        showEdit();
    }else{
        if(isset($_GET['remove'])){
            if($_GET['remove'] == "1"){
                removeBook();
            }
            else{
                showRemove();
            }
        }else{
            if(isset($_GET['peek']))
                showPeek();
        }
    }
}

function getDataForBook(){
    
    if(isset($_GET['id'])){        
        
        $_SESSION['id'] = htmlentities($_GET['id']);
        $db = new DBConnection('booksDB.db');
        $book = $db->selectOne('Books', 'WHERE id = '.$_SESSION['id']);
        return $book;
        
    }
}

function showIndex(){ head("Book list")?>

    <body>
    <header>
        <div class="p-5 mb-4 bg-light rounded-3">
            <h1 class="display-10 fw-bold">Book List</h1>
            <button onclick="window.location.href='<?= checkPage() ?>&getpdf'" class="btn btn-primary btn-lg" type="button" href>GENERATE PDF</button>
        </div>
    </header>
    <main>
        <form action="" method="GET">
            <div class="container mb-5" style="width: 100%; display:flex; gap: 12%;">
                <label for="" class="fs-4 form-label" style="width: 70%">Order Type</label>
                    <select class="form-select form-select-lg mb-3" name="columnOrder" id="">
                        <option selected>Id</option>
                        <option value="isbn">ISBN</option>
                        <option value="title">Title</option>
                        <option value="author">Author</option>
                        <option value="publisher">Publisher</option>
                        <option value="pages">Pages</option>
                    </select>
                    <select class="form-select form-select mb-3" name="orderOption" id="">
                        <option selected value="ASC">ASC</option>
                        <option value="DESC">DESC</option>
                    </select>
                <input class="btn btn-primary" type="submit" style="height:12%; width:30%" value="order" name="order">
            </div>
        </form>
    <div style="width:95vw; margin: 0 auto">
            <div class="row">

                <div class="col-2">
                    <div class="card">
                        <div class="card-header">Add new book</div>
                        <div class="card-body">
                            <form action="<?= $_SERVER['PHP_SELF']; ?>?added" method="post" enctype="multipart/form-data">

                                <label for="nombre" class="form-label">ISBN</label>
                                <input class="form-control" type="text" name="isbn" id="isbn">

                                <br>

                                <label for="nombre" class="form-label">Title</label>
                                <input class="form-control" type="text" name="title" id="title" required>

                                <br>

                                <label for="nombre" class="form-label">Author</label>
                                <input class="form-control" type="text" name="author" id="author" required>

                                <br>

                                <label for="nombre" class="form-label">Publisher</label>
                                <input class="form-control" type="text" name="publisher" id="publisher" required>

                                <br>

                                <label for="descripcion" class="form-label">Pages</label>
                                <input class="form-control" type="number" name="pages" id="pages" required>

                                <br>

                                <input class="btn btn-primary" type="submit" value="Add book">

                                <div class="badge text-nowrap fs-9" style="color:red; width:50%; margin-top: 5%">
                                    <?= $_SESSION['error'] ?? "" ?>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-10">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">ISBN</th>
                                    <th scope="col">title</th>
                                    <th scope="col">Author</th>
                                    <th scope="col">Publisher</th>
                                    <th scope="col">Pages</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php getBooksForTable() ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
        <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <li class="page-item <?= checkPreviousState() ?>">
            <a class="page-link" href="<?= checkPreviousPage() ?>">Previous</a>
            </li>
            <li class="page-item <?= checkNextState() ?>">
            <a class="page-link" href="<?= checkNextPage() ?>">Next</a>
            </li>
        </ul>
        </nav>

    </main>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"
        integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.min.js"
        integrity="sha384-7VPbUDkoPSGFnVtYi0QogXtr74QeVeeIs99Qfg5YCF+TidwNdjvaKZX19NZ/e6oz" crossorigin="anonymous">
    </script>
    </body>

    </html>
<?php }

function showEdit(){

    head("Edit book");

    $book = getDataForBook();

?>

    <body>

        <div class="container mt-5">

            <div class="row justify-content-center align-items-center g-2">

                <div class="col-md-4"></div>

                <div class="col-md-4">
                    <br>
                    <div class="card">
                        <div class="card-header">
                            Editing book
                        </div>
                        <div class="card-body">
                            <form action="<?= $_SERVER['PHP_SELF']; ?>?edited" method="POST">

                                <label for="user">ISBN</label>
                                <input value="<?= $book['isbn'] ?>" class="form-control" type="text" name="isbn" id="user">

                                <br>

                                <label for="user">Title</label>
                                <input value="<?= $book['title'] ?>" class="form-control" type="text" name="title" id="user">

                                <br>

                                <label for="user">Author</label>
                                <input value="<?= $book['author'] ?>" class="form-control" type="text" name="author" id="user">

                                <br>

                                <label for="user">Publisher</label>
                                <input value="<?= $book['publisher'] ?>" class="form-control" type="text" name="publisher" id="user">

                                <br>

                                <label for="user">Pages</label>
                                <input value="<?= $book['pages'] ?>" class="form-control" type="number" name="pages" id="user">

                                <br>

                                <button class="btn btn-success" type="submit">Save changes</button>

                                <div class="badge text-nowrap fs-6" style="color:red; margin-left:12%;">
                                    <?= $_SESSION['error'] ?? "" ?>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4"></div>

            </div>

        </div>

    </body>
    </html>

<?php } function showRemove(){
    
    $titleBook = getTitleBook();
    
?>
<head>
    <meta charset="utf-8">
    <title>Remove book</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> 
    <script>
        $(document).ready(function()
        {
            $("#mostrarmodal").modal("show");
        });
    </script>
</head>
<body>
   <div class="modal fade" id="mostrarmodal" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
           <div class="modal-header">
          <a href="index.php"><button type="button" class="close" aria-hidden="true">&times;</button></a>
              <h3>Are you sure you want to remove...</h3>
              <p style="font-style: italic">"<?= $titleBook ?>"</p>
           </div>
           <div class="modal-body">
              <h4>The book will completly remove</h4>
              if you dont want it, close this window
       </div>
           <div class="modal-footer">
          <a href="index.php?id=<?=$_GET['id']?>&remove=1" class="btn btn-danger">Im sure</a>
           </div>
      </div>
   </div>
</div>
</body>
</html>
    
    
<?php }

function showPeek(){

    head("Peek book");

    $book = peekBook();

?>

<body class="container" style="margin-top:10vh; width:40vw">
<table class="table table-hover">
  <thead>
    <tr>
      <th scope="col" style="text-align:center">ISBN</th>
      <td scope="row"><?=$book['isbn']?></td>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row" style="text-align:center">Title</th>
      <td scope="row"><?=$book['title']?></td>
    </tr>
    <tr>
      <th scope="row" style="text-align:center">Author</th>
      <td scope="row"><?=$book['author']?></td>
    </tr>
    <tr>
      <th scope="row" style="text-align:center">Publisher</th>
      <td scope="row"><?=$book['publisher']?></td>
    </tr>
    <tr>
      <th scope="row" style="text-align:center">Pages</th>
      <td scope="row" style="width: 60vh"><?=$book['pages']?></td>
    </tr>
  </tbody>
</table>
</body>
</html>

<?php }

function showMsgError($error){
    $_SESSION['error'] = $error;
    if(isset($_GET['edited'])){
        header("Location: index.php?edit&id=" . $_SESSION['id']);
    }else{
        if(isset($_GET['added']))
            header("Location: index.php");
    }
}

function checkInputs(){
    if(isset($_POST['isbn']) && isset($_POST['title']) && isset($_POST['author']) && isset($_POST['publisher']) && isset($_POST['pages'])){
        if(empty($_POST['isbn']) || empty($_POST['title']) || empty($_POST['author']) || empty($_POST['publisher']) || empty($_POST['pages'])){
            showMsgError('All fields are required');
            exit();
        }else{
            if(!preg_match('/^[[:digit:]]{9}-[[:alnum:]]{1}$/', $_POST['isbn'])){
                showMsgError('isbn must be valid');
            }else{
                if(!preg_match('/^[[:digit:]]+$/', $_POST['pages'])){
                    showMsgError('pages must be a number');
                }else{
                    return true;
                }
            }
        }
    }
    return false;
}

function head($title){
    echo <<< END
    <!doctype html>
    <html lang="en">

    <head>
    <title>${title}</title>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">

    </head>

    END;
}

?>



