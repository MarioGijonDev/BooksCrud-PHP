<?php

class DBConnection{
    private $mdb;

    public function __construct($dbName){
        try{
            new SQLite3(__DIR__ . "/".$dbName);
            $this->mdb = new PDO("sqlite:" . __DIR__ . "/" . $dbName);
            $this->mdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //echo "Connection succesfuly<br>";

        }catch(PDOException $e){

            echo $e->getMessage();

        }
    }

    public function getMdb(){
        return $this->mdb;
    }

    public function dropTable($tableName){
        try{
            $this->mdb->exec("DROP TABLE IF EXISTS $tableName");
            echo "Tabla $tableName eliminada";
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function createTable($tableName){
        try{
            $this->mdb->exec("CREATE TABLE IF NOT EXISTS $tableName (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                isbn TEXT,
                title TEXT,
                author TEXT,
                publisher TEXT,
                pages TEXT
            )");
            echo "Tabla $tableName creada";
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function select($sql){
        try{
            $data = $this->mdb->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            //echo "<pre>";
            //var_dump($data);
            //echo "</pre>";
            //echo "consulta realizada";
            return $data;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function selectAll($table, $option = ["id", "ASC"]){
        try{
            $data = $this->mdb->query(
                "SELECT * FROM $table ORDER BY $option[0] $option[1];"
            )->fetchAll(PDO::FETCH_ASSOC);
            //echo "<pre>";
            //var_dump($data);
            //echo "</pre>";
            //echo "consulta realizada";
            return $data;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function selectOne($table, $option){
        try{
            $data = $this->mdb->query(
                "SELECT * FROM $table $option;"
            )->fetchAll(PDO::FETCH_ASSOC);
            //echo "<pre>";
            //var_dump($data);
            //echo "</pre>";
            //echo "consulta realizada";
            return $data[0];
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function executeSQL($sql){
        try{
            $this->mdb->exec($sql);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function selectAllTables(){
        $data = $this->mdb->query(
            "SELECT * FROM sqlite_master WHERE type = \"table\""
        )->fetchAll(PDO::FETCH_ASSOC);
        //echo "<pre>";
        //var_dump($data);
        //echo "</pre>";
        return $data;
    }

    public function checkIfTableExists($tableName){
        try{
            foreach($this->selectAllTables() as $table){
                if($table['name'] == $tableName)
                    return true;
            }
            return false;
        }catch(PDOException $e){
            
        }
    }

    public function createBook($data){
        try{
            $lastId = $this->selectOne('Books', 'ORDER BY id DESC LIMIT 1')['id'];
            //var_dump($lastId);
            $stmt = $this->mdb->prepare("INSERT INTO Books VALUES(:id, :isbn, :title, :author, :publisher, :pages)");
            $stmt->execute([
                ':isbn' => $data['isbn'],
                ':title' => $data['title'],
                ':author' => $data['author'],
                ':publisher' => $data['publisher'],
                ':pages' => $data['pages'],
                ':id' => ++$lastId
            ]);
            //echo "Books creados";
        }catch(PDOException $e){
            $e->getMessage();
        }
    }

    public function selectBooks($option = ["id", "ASC"]){
        try{
            $data = $this->mdb->query(
                "SELECT isbn, title, author, publisher, pages
                FROM Books 
                ORDER BY $option[0] $option[1];"
            )->fetchAll(PDO::FETCH_ASSOC);
            //echo "<pre>";
            //var_dump($data);
            //echo "</pre>";
            //echo "consulta realizada";
            return $data;
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }

    public function updateBook($data){
        try{
            $stmt = $this->mdb->prepare(
                "UPDATE Books 
                SET isbn = :isbn, title = :title, author = :author, publisher = :publisher, pages = :pages
                WHERE id = :id");
            $stmt->execute([
                ':isbn' => $data['isbn'],
                ':title' => $data['title'],
                ':author' => $data['author'],
                ':publisher' => $data['publisher'],
                ':pages' => $data['pages'],
                ':id' => $data['id']
            ]);
            //echo "Libro actualizado";
        }catch(PDOException $e){
            $e->getMessage();
        }
    }

    public function removeBook($id){
        try{
            $stmt = $this->mdb->prepare("DELETE FROM Books WHERE id = :id");
            $stmt->execute([':id' => $id]);
            echo "Libro eliminado";
        }catch(PDOException $e){
            $e->getMessage();
        }
    }

}