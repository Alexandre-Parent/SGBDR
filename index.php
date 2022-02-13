<?php
require "vendor/autoload.php";
//Connection à la bdd

$host = "163.172.130.142";
$port = "3310";
$databaseName = "sakila";
$username = 'etudiant';
$password = 'CrERP29qwMNvcbnAMgLzW9CwuTC5eJHn';

$dsn = "mysql:host=$host;port=$port;dbname=$databaseName";

$limit = isset($_POST ["limit-records"]) ? $_POST ["limit-records"] :20;
define('PER_PAGE', $limit);
try {
    $pdo = new PDO($dsn, $username, $password);
   /* echo "Connection successfull";
    echo '</pre>';*/
} catch (PDOException $error) {
    echo $error->getMessage();
}

$query = "SELECT * FROM film_list";
$queryCount = "SELECT COUNT(fid) as count FROM film_list";
$params = [];
$sortable = ["fid" , "title", "category", "rating", "price", "length"];

// Recherche par film
if (!empty($_GET['q'])){
    $query .= " WHERE title LIKE :title ";
    $queryCount .= "WHERE title LIKE :title";
    $params["title"] = "%" . $_GET['q'] . "%";
}

// Tri
if (!empty($_GET["sort"]) && in_array($_GET['sort'], $sortable)){
    $direction = $_GET['dir'] ?? 'asc';
    if (!in_array($direction, ["asc", "desc"])){
        $direction = "asc";
    }
    $query .= " ORDER BY " . $_GET['sort'] . " $direction";
}


//Pagination

$page = (int)($_GET['p'] ?? 1);
$offset = ($page-1) * PER_PAGE;

$query .= ' LIMIT ' . PER_PAGE . " OFFSET $offset";

$statement = $pdo->prepare("$query");
$statement->execute($params);
$films = $statement->fetchAll();

$statement = $pdo->prepare($queryCount);
$statement->execute($params);
$count =(int)$statement->fetch()['count'];
$pages = ceil($count / PER_PAGE);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Projet Back</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body class="p-4">

     <!--<form action="" class="mb-4">
        <div class="form-group">
            <input type="text" class="form-control" name="q" placeholder="Rechercher par nom de film" value="<?= htmlentities($_GET['q'] ?? null) ?>">
        </div>
        <button class="btn btn-primary">
            Rechercher
        </button>
    </form> -->
     <div class="divnum_rows">
         <span class="paginationtextfield">Number of rows:</span>&nbsp;
         <form method="post" action="#">
             <select id="limit-records" name="limit-records">
                 <option disabled="disabled" selected="selected">Maximum de film affiché</option>
                 <?php foreach ([10,20,50,100,200] as $limit)
                     :?>
                     <option <?php if (isset($_POST["limit-records"]) && $_POST["limit-records"] == $limit) echo "$limit"?> value="<?= $limit; ?>">
                         <?= $limit; ?>
                     </option>
                 <?php endforeach; ?>
             </select>
         </form>
     </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>
                   <?= \App\Table::sort("fid", "Film ID", $_GET) ?>
                </th>
                <th>
                    <?= \App\Table::sort("title", "Film", $_GET) ?>
                </th>
                <th>
                    <?= \App\Table::sort("category", "Genre", $_GET) ?>
                </th>
                <th>
                    <?= \App\Table::sort("rating", "Classification", $_GET) ?>
                </th>
                <th>
                    <?= \App\Table::sort("price", "Prix de la location", $_GET) ?>
                </th>
                <th>
                    <?= \App\Table::sort("length", "Nombre de location", $_GET) ?>
                </th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($films as $film ): ?>
        <tr>
            <td>
               <?= $film["FID"] ?>
            </td>
            <td>
                <?= $film["title"] ?>
            </td>
            <td>
                <?= $film["category"] ?>
            </td>
            <td>
                <?= $film["rating"] ?>
            </td>
            <td>
                <?= $film["price"] ?>
            </td>
            <td>
                <?= $film["length"] ?>
            </td>
        </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <p>
        Page <?php  echo $page ?> / <?php  echo $pages ?>
    </p>

    <?php if ($pages > 1 && $page > 1): ?>
        <a href="?<?= \App\URL::withParam($_GET,"p" , $page - 1)?>" class="btn btn-primary">Page Précedente</a>
    <?php endif  ?>
    <?php if ($pages > 1 && $page < $pages): ?>
        <a href="?<?= \App\URL::withParam($_GET, "p" , $page + 1) ?>" class="btn btn-primary">Page suivante</a>
    <?php endif  ?>


</body>
</html>

<script>
    $(document).ready(function ( ) {
        $("#limit-records").change(function ( ) {
            $('form').submit();
        })

    })
</script>
