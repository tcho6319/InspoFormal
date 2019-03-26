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
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

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
          <p><strong>Welcome to InspoFormal!</strong> Our mission is to provide a platform for users to share their styles and inspirations for their special formal events.</p>
    </div>

    <hr />

    <!-- If no tags selected in search form -->
    <h2>All Results</h2>
    <div class="galleryDiv">
        <!-- Do sql query to get all images -->
        <?php
        $sql = "SELECT * FROM images;";

        $params = array();

        $records = exec_sql_query($db, $sql, $params)->fetchAll();

        if (count($records) > 0){
          // echo'<li><img alt="light pink dress" src="uploads/images/1.jpeg"/></li>';
          foreach($records as $record){
            echo '<div class="img"><li><img alt="' . htmlspecialchars($record["a_description"]) . '" src="uploads/images/' . htmlspecialchars($record["id"]) . '.' . htmlspecialchars($record["img_ext"]) . '"/></li></div>';
          }
        }
        ?>




    </div>

    <!-- If tags selected in search form -->
    <!-- <div class="galleryDiv">
      <h2>Search Results</h2> -->
        <!-- Do sql query to get images with the tags -->
    <!-- </div> -->

<hr />

  <!-- If user logged in -->
  <?php if (is_logged_in()){ ?>
  <div id="addImgDiv">
    <form id="addImgForm" action="index.php" method="post" enctype="multipart/form-data">
      <fieldset>
      <legend>Add an Image</legend>
          <ul>
            <li>
              <!-- Set max file size to be 10 MB -->
              <input type="hidden" name="max_file_size" value="10000000"/>
              <label for="img_file">Upload Image: </label>
              <input id="img_file" type="file" name="img_file">
            </li>

            <li>
              <label for="exist_tag">Existing Tags: </label>
              <select multiple id="exist_tag">

                <?php
                  //Getting list of all the tags in the database
                  $tags = exec_sql_query($db, "SELECT DISTINCT tag FROM tags", NULL)->fetchAll(PDO::FETCH_COLUMN);

                  //echoing multiple select option for every tag
                  foreach ($tags as $tag) {
                    echo "<option value='" . strtolower(htmlspecialchars($tag)) . "'>".ucfirst(htmlspecialchars($tag))."</option>";
                  }
                ?>
              </select>
            </li>

            <li>
              <label for="new_tag">New Tags (separated by commas): </label>
              <input type="text" name="new_tag"/>
            </li>

            <li>
              <button name="add_img" type="submit">Add Image</button>
            </li>
          <ul>
      </fieldset>
    </form>

  </div>
<?php } ?>
  <?php include("includes/footer.php"); ?>
</body>
</html>
