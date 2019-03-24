<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

$title = "Home";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <title>Home</title>
</head>

<body>
  <?php include("includes/header.php");?>
  <!-- TODO: This should be your main page for your site. -->
  <div class="mainDiv">

    <div id="searchDiv">
      <form id="searchForm" action="index.php" method="get">
        <fieldset>
        <legend>Search For or Filter Images</legend>
        <label for="tag_search">Tags: </label>
        <select multiple id="tag_search">

          <?php
          //Getting list of all the tags in the database
          $tags = exec_sql_query($db, "SELECT DISTINCT tag FROM tags", NULL)->fetchAll(PDO::FETCH_COLUMN);

          //echoing multiple select option for every tag
          foreach ($tags as $tag) {
            echo "<option value='" . strtolower(htmlspecialchars($tag)) . "'>".ucfirst(htmlspecialchars($tag))."</option>";
          }
          ?>
        </select>
    </div>

    <div id="aboutDiv">
      <h2>About</h2>
          <p><strong>Welcome to InspoFormal!</strong>Our mission is to provide a platform for users to share their styles and inspirations for their special formal events.</p>
    </div>

    <hr />

    <!-- If no tags selected in search form -->
    <div class="galleryDiv">
      <h2>All Results</h2>
        <!-- Do sql query to get all -->
    </div>

    <!-- If tags selected in search form -->
    <div class="galleryDiv">
      <h2>Search Results</h2>
        <!-- Do sql query to get images with the tags -->
    </div>

<hr />

  <!-- If user logged in -->
  <div id="addImgDiv">
    <form id="addImgForm">
    </form>
  </div>
  <?php include("includes/footer.php"); ?>
</body>
</html>
