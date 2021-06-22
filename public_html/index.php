<!doctype html>
<html lang="ru">
      <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<head>
  <title>Lab2</title>
</head>
<body>
<?php // 1 - 105, 2 - 97, 3 - 40, 4 - 383, 5 - 224, 6 - 213
require_once $_SERVER['DOCUMENT_ROOT'] . '/libs/rb-mysql.php'; //подключение библиотеки RedBean

    $host = 'localhost'; 
    $user = 'b90817s3_mylab';  
    $pass = 'h&U76Sc1'; 
    $db_name = 'b90817s3_mylab';   
    $link = mysqli_connect($host, $user, $pass, $db_name); 
    
R::setup('mysql:host=localhost; dbname=b90817s3_mylab', 'b90817s3_mylab', 'h&U76Sc1'); //подключение к БД

if (R::testConnection()) 
{
  echo "~ ~ ~Соединение с базой данных установлено через RedBean ~ ~ ~";
}

if (!R::testConnection())
{
  exit ('OOPS! Не удалось подключиться к базе данных!<br>');
}
?>
  
  <?php
  if (isset($_GET['del_id'])) {
    $sql = mysqli_query($link, "DELETE FROM books WHERE id = {$_GET['del_id']}");
  }
  
  if (isset($_GET['delgenre_id'])) { // Задание №3 - удаление жанра
        $genre = R::load('genres', $_GET['delgenre_id']);
        R::trash($genre);
    //$sql = mysqli_query($link, "DELETE FROM genres WHERE id = {$_GET['delgenre_id']}");
  }
  
    if (isset($_GET['delauthor_id'])) {
    $sql = mysqli_query($link, "DELETE FROM authors WHERE id = {$_GET['delauthor_id']}");
  }
  
  
  
    if (isset($_GET['red_id'])) {
      $sql = mysqli_query($link, "SELECT books.author as booksauthor, books.id as idbook, books.name as booksname, books.rel as releas, authors.name as nameauthor, genres.name as genrename, books.desc as descript, genres.id as genrid
    FROM books inner join genres on genres.id = books.genre inner join authors on books.author = authors.id 
    WHERE books.id={$_GET['red_id']}");
    $gored = mysqli_fetch_array($sql); 
    }
    
        if (isset($_GET['redgenre_id'])) {
      $sql = mysqli_query($link, "SELECT genres.name as genresname
    FROM genres
    WHERE genres.id={$_GET['redgenre_id']}");
    $gored = mysqli_fetch_array($sql); 
    }
    
    
        if (isset($_GET['redauthor_id'])) {
      $sql = mysqli_query($link, "SELECT authors.name as authorsname, authors.birth as authorsbirth
    FROM authors
    WHERE authors.id={$_GET['redauthor_id']}");
    $gored = mysqli_fetch_array($sql); 
    }
    
    
    
    
    
        if (isset($_POST['Name'])) {
              if (isset($_GET['red_id'])) {
                  $sql = mysqli_query($link, "UPDATE books SET `name`='{$_POST['Name']}',`rel`='{$_POST['Release']}', `author`='{$_POST['Author']}',`genre`='{$_POST['Genre']}', `desc`='{$_POST['Desc']}' WHERE id='{$_GET['red_id']}'");
              } 
              else {
                  $sql = mysqli_query($link, "INSERT INTO books (`name`, `rel`, `author`, `genre`, `desc` ) VALUES ('{$_POST['Name']}', '{$_POST['Release']}', '{$_POST['Author']}', '{$_POST['Genre']}',    '{$_POST['Desc']}'       )");
              } 
        }
        
        
        if (isset($_POST['Namegenre'])) {
              if (isset($_GET['redgenre_id'])) {
                  $sql = mysqli_query($link, "UPDATE genres SET `name`='{$_POST['Namegenre']}' WHERE id='{$_GET['redgenre_id']}'");
              } 
              else {
                  $sql = mysqli_query($link, "INSERT INTO genres (`name`) VALUES ('{$_POST['Namegenre']}')");
              } 
        }   
        
        if (isset($_POST['Nameauthor'])) {   // Задание №2 - редактирование инф. об авторе
              if (isset($_GET['redauthor_id'])) {
                    $authors = R::load('authors', $_GET['redauthor_id']);
                    $authors->name = $_POST['Nameauthor'];
                    $authors->birth = $_POST['Birthauthor'];
                    R::store($authors);
                  //$sql = mysqli_query($link, "UPDATE authors SET `name`='{$_POST['Nameauthor']}',`birth`='{$_POST['Birthauthor']}'  WHERE id='{$_GET['redauthor_id']}'");
              } 
              else { // Задание №1 - вставка данных формы в табл. автор
                    $authors = R::dispense('authors');
                    $authors["name"]= $_POST["Nameauthor"];
                    $authors["birth"]= $_POST["Birthauthor"];
                    R::store($authors);
                //$sql = mysqli_query($link, "INSERT INTO authors (`name`, `birth`) VALUES ('{$_POST['Nameauthor']}', '{$_POST['Birthauthor']}')");
              } 
        } 
        

    
?>
<div class="container">
<div class="container">
      <div class="row">
    <div class="col-sm-4">
<form action="" method="post">
    <table>
      <tr>
        <td>Название книги:</td>
        <td><input type="text" name="Name" value="<?= isset($_GET['red_id']) ? $gored['booksname'] : ''; ?>"></td>
      </tr>
      <tr>
        <td>Дата публикации:</td>
        <td><input type="text" name="Release" value="<?= isset($_GET['red_id']) ? $gored['releas'] : ''; ?>"></td>
      </tr>
      
            <tr>   <td>Автор: </td> 
            <td> <select class="custom-select" name="Author">  
            <option value="<?= isset($_GET['red_id']) ? $gored['booksauthor'] : ''; ?>"><?= isset($_GET['red_id']) ? $gored['nameauthor'] : 'Select'; ?></option>            <?  
                $autlist=mysqli_query($link, "select * from authors order by id asc");  
            while($aut_list=mysqli_fetch_array($autlist)){  
                ?>  
                    <option value="<? echo $aut_list['id']; ?>">  
                                         <?echo $aut_list['name'];?>  
                    </option>  
                <?  
                }  
                ?>  
            </select> </td>   </tr> 
            
            
              <tr>   <td>Жанр: </td> 
            <td> <select name="Genre" class="custom-select">  
            <option value="<?= isset($_GET['red_id']) ? $gored['genrid'] : ''; ?>"><?= isset($_GET['red_id']) ? $gored['genrename'] : 'Select'; ?></option>            <?  
                $genlist=mysqli_query($link, "select * from genres order by id asc");  
            while($gen_list=mysqli_fetch_array($genlist)){  
                ?>  
                    <option value="<? echo $gen_list['id']; ?>">  
                                         <?echo $gen_list['name'];?>  
                    </option>  
                <?  
                }
                ?>  
            </select> </td>   </tr> 
      
      
                <tr>
        <td>Описание книги:</td>
        <td><input type="text" name="Desc" value="<?= isset($_GET['red_id']) ? $gored['descript'] : ''; ?>"></td>
      </tr>
      
      <tr>
        <td colspan="2"><input class="btn btn-primary" type="submit" value="OK"></td>
      </tr>
    </table>
    <td><p></p><a href=/>Обновить</a></td> <br/></tr></p>
  </form> </div>
  
  
    <div class="col-sm-4">
<form action="" method="post">
    <table>
      <tr>
        <td>Название жанра:</td>
        <td><input type="text" name="Namegenre" value="<?= isset($_GET['redgenre_id']) ? $gored['genresname'] : ''; ?>"></td>
      </tr>
      
      <tr>
        <td colspan="2"><input class="btn btn-primary" type="submit" value="OK"></td>
      </tr>
    </table>
  </form>
  </div>
  
   <div class="col-sm-4">
  <form action="" method="post">
    <table>
      <tr>
        <td>Автор:</td>
        <td><input type="text" name="Nameauthor" value="<?= isset($_GET['redauthor_id']) ? $gored['authorsname'] : ''; ?>"></td>
      </tr>
      <tr>
        <td>Дата рождения:</td>
        <td><input type="text" name="Birthauthor" value="<?= isset($_GET['redauthor_id']) ? $gored['authorsbirth'] : ''; ?>"></td>
      </tr>
      
      <tr>
        <td colspan="2"><input class="btn btn-primary" type="submit" value="OK"></td>
      </tr>
    </table>
  </form>
	</div>
  

</table>
  </div>
  
  </div>
      <h3>Книги за последнее десятилетие</h3> 
 <?php 
  $years = R::find('books', 'rel LIKE ? OR rel = ?', [ "201_", "2020" ]);
  foreach ($years as $ten)
  {
  echo '<ul>';
  echo "<li>" . $ten['name'] . "(" . $ten['rel'] . ")" . "</li>";
  echo "</ul>";
  } 
  ?>
  
  <? //Задание №5 - поиск по ключ. слову
if (isset($_POST['search'])) {
	$query = $_POST['query'];
	$query = trim($query);
	$query_select_books = R::getAll("SELECT * FROM books WHERE name LIKE '%$query%' OR rel LIKE '%$query%'");
}
?>
  
	<div class="queries">
		<form action="" method="POST">
			<p>Поиск по символам: <input type="text" name="query"></p>
			<p><input type="submit" name="search" value="Найти"></p>
		</form>
	</div>
	<div class="results">
		<? if (isset($query_select_books)) { ?>
			<h2>Записи в таблице "Книги":</h2>
			<? foreach ($query_select_books as $val) {?>
			<div class="block_res">
				<p><h5>Название:</h5> <?=$val['name']?></p>
				<p><h5>Дата написания:</h5> <?=$val['rel']?></p>
				<p><h5>Описание:</h5> <?=$val['desc']?></p>
				______________________________________
			</div>
			<? } ?>
		<? } ?>
  


  
  
  
  
  
  

  
  <table class="table table-striped">
  <tr>
    <td>id</td>
    <td>Название книги</td>
    <td>Дата публикации</td>
    <td>Автор</td>
    <td>Жанр</td>
    <td>Описание</td>
    <td>Удаление</td>
    <td>Изменение</td>
  </tr>
  

  
  <?php
    $sql = mysqli_query($link, "SELECT  books.id, books.name as booksname, books.rel, authors.name as nameauthor, genres.name as genrename, books.desc 
    FROM books inner join genres on genres.id = books.genre inner join authors on books.author = authors.id   
    ORDER BY books.id ASC");
    while ($result = mysqli_fetch_array($sql)) {
      echo "<tr><td>{$result['id']}</td> 
      <td>{$result['booksname']}</td> 
      <td>{$result['rel']} </td>
      <td>{$result['nameauthor']}</td> 
      <td>{$result['genrename']} </td> 
      <td>{$result['desc']} </td> 
      <td><a href='?del_id={$result['id']}'>Удалить</a></td>
      <td><a href='?red_id={$result['id']}'>Изменить</a></td> </tr>";

    }
    echo"<hr>";
        echo "<br>";

  ?>
  
  

</table> 


</div>

<div class="container">
    <div class="row">
 <div class="col">
  <table class="table table-striped">
  <tr>
    <td>Автор</td>
    <td>Дата рождения</td>
    <td>Удалить</td>
    <td>Изменить</td>
  </tr>
  

  
  <?php
      echo "<br>";

    $sql = mysqli_query($link, "SELECT  authors.id as idauth, authors.name as authorname, authors.birth as birth
    FROM authors  
    ORDER BY authors.id ASC");
    while ($result = mysqli_fetch_array($sql)) {
      echo "<tr> 
      <td>{$result['authorname']}</td> 
      <td>{$result['birth']} </td>
      <td><a href='?delauthor_id={$result['idauth']}'>Удалить</a></td>
      <td><a href='?redauthor_id={$result['idauth']}'>Изменить</a></td> 
      </tr>";

    }
  ?>
  
  

</table>
</div>
 <div class="col" >
     
   <table class="table table-striped">
  <tr>
    <td>Жанр</td>
    <td>Удалить</td>
    <td>Изменить</td>
  </tr>
  

  
  <?php
      echo "<br>";

    $sql = mysqli_query($link, "SELECT  genres.id as idgenr, genres.name as genresname
    FROM genres  
    ORDER BY genres.id ASC");
    while ($result = mysqli_fetch_array($sql)) {
      echo "<tr> 
      <td>{$result['genresname']}</td> 
      <td><a href='?delgenre_id={$result['idgenr']}'>Удалить</a></td>
      <td><a href='?redgenre_id={$result['idgenr']}'>Изменить</a></td> 
      </tr>";

    }
  ?>
  
  

</table>    
     
</div>
    

</div>

<div class="col" >
     
   <table class="table table-striped"> 
  <tr>
    <td>ID</td>
    <td>Жанр</td>
    <td>Кол-во книг</td>
  </tr>
  

    <?php //Задание №4 - таблица вида Жанр/Кол-во книг
    $genres = R::findAll('genres');
    foreach($genres as $genre) { ?>
    <tr>
        <td scope="row">
        <?=$genre["id"]?>
        </td>
        <td>
        <?= $genre["name"]?>
        </td>
        <td>
        <?=R::count( 'books', ' genre = ? ', [ $genre["id"] ] );?>
        </td>
    </tr>
    <?php 
    }
    ?>
    ?>
    
    
<?php
      //echo "<br>";
    //$sql = mysqli_query($link, "SELECT  genres.id as idgenr, genres.name as genresname
    //FROM genres  
    //ORDER BY genres.id ASC");
    //while ($result = mysqli_fetch_array($sql)) {
      //echo "<tr> 
      //<td>{$result['genresname']}</td> 
      //</tr>";
    //}
?>
  
  

</table>    
     
</div>

</div>
</body>




</html>