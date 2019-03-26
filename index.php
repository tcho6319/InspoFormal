<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

$title = "Home";

//Add image php
//Code adapted from Source: Kyle Harms INFO 2300: Lab 8
if (isset($_POST["add_img"]) && is_logged_in()){
  //filter img_file input and other inputs
  $add_info = $_FILES["img_file"];

  if ($add_info["error"] == UPLOAD_ERR_OK){
    $add_success = True;
  }

  // if ($_FILES["img_file"]["error"] == UPLOAD_ERR_OK){
  //   $add_success = True;
  // }
  $add_description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);

  $add_citation = filter_input(INPUT_POST, "citation", FILTER_SANITIZE_STRING);

  //TO DO: FILTER EXISTING TAGS
  //TO DO: FILTER NEW TAG

  //if add is successful -> record new img in db and store img in uploads directory
  if (isset($add_success)){
    // $add_info = $_FILES["img_file"];
    // // global $online_user;
    // echo $add_info["name"];
    // echo "hi";
    $add_basename = basename($_FILES["img_file"]["name"]);
    // echo $add_basename;
    $add_ext = strtolower( pathinfo($add_basename, PATHINFO_EXTENSION) );

    //record new add img into db
    $online_user_id = $online_user['id'];
    //TO DO: MODIFY SQL QUERY TO INCLUDE ADD IMG WITH TAGS
    $sql = "INSERT INTO images ('citation', 'user_id', 'img_ext', 'a_description') VALUES (:add_citation, :online_user_id, :add_ext, :add_description);";

    $params = array(
      ':add_citation' => $add_citation,
      ':online_user_id' => $online_user_id,
      ':add_ext' => $add_ext,
      ':add_description' => $add_description
    );

    $result = exec_sql_query($db, $sql, $params);

    //store add img in uploads directory
    $add_id = $db -> lastInsertId("id");

    $new_path = "uploads/images/" . $add_id . "." . $add_ext;

    move_uploaded_file($_FILES["img_file"]["tmp_name"], $new_path);

    $add_info["tmp_name"]  = $new_path;

  }
}


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
              <input id="img_file" name="img_file" type="file"/>
            </li>

            <li>
              <label for="description">Image Description: </label>
              <input id="description" type="text" name="description"/>

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
              <input id="new_tag" type="text" name="new_tag"/>
            </li>

            <li>
              <label for="citation">Image Citation: </label>
              <input id="citation" type="text" name="citation"/>
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
