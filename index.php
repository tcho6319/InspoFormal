<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

$title = "Home";

//Add image php
//Code adapted from Source: Kyle Harms INFO 2300: Lab 8
const MAX_FILE_SIZE = 1000000;
if (isset($_POST["add_img"]) && is_logged_in()){
  //filter img_file input and other inputs
  $add_info = $_FILES["img_file"];
  $add_description = filter_input(INPUT_POST, "description", FILTER_SANITIZE_STRING);

  $add_citation = filter_input(INPUT_POST, "citation", FILTER_SANITIZE_STRING);

  //FILTER EXISTING TAGS
  if (isset($_POST["exist_tag"]) && $_POST["exist_tag"] != ""){
    $add_exist_tags_unfiltered = $_POST["exist_tag"];
    $add_exist_tags_filtered = array();
    foreach($add_exist_tags_unfiltered as $add_exist_tag_unfilt){
      $add_exist_tags_filtered[] = filter_var($add_exist_tag_unfilt, FILTER_SANITIZE_STRING);
    }
  }


  //FILTER NEW TAG
  if (isset($_POST["new_tag"])&& $_POST["new_tag"] != "") {
    $add_new_tag = filter_input(INPUT_POST, "new_tag", FILTER_SANITIZE_STRING);
    //Separate string input by commas to create new_tags array
    $add_new_tags_filtered_split = explode(",", $add_new_tag);
    $add_new_tags_filtered = array();
    foreach ($add_new_tags_filtered_split as $add_new_tag_filtered){
      $add_new_tag_filtered = trim($add_new_tag_filtered);
      $add_new_tag_filtered = strtolower($add_new_tag_filtered);
      $add_new_tags_filtered[] = $add_new_tag_filtered;
    }
  }

  //if add is successful -> record new img in db and store img in uploads directory
  if ($add_info["error"] == UPLOAD_ERR_OK){

    $add_basename = basename($_FILES["img_file"]["name"]);

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


    //Adding tags and imgs to db with upload
    $all_tags_for_db = array();
    if(isset($_POST["exist_tag"]) && $_POST["exist_tag"] != ""){
      $all_tags_for_db = $add_exist_tags_filtered;

      if(isset($_POST["new_tag"]) && $_POST["new_tag"] != ""){
        //merge the existing tag and new tag array w/o dupes
        $all_tags_for_db = array_unique(array_merge($all_tags_for_db, $add_new_tags_filtered));
      }
    }

    elseif(isset($_POST["new_tag"])&& $_POST["new_tag"] != ""){
      $all_tags_for_db = $add_new_tags_filtered;
    }

    if(!(empty($all_tags_for_db))){
      if(isset($_POST["new_tag"])&& $_POST["new_tag"] != ""){
      //Extract new tags from $all_tags_for_db
      $new_tags_to_add_to_db = array();

      $all_existing_tags = exec_sql_query($db, "SELECT DISTINCT tag FROM tags", NULL)->fetchAll(PDO::FETCH_COLUMN);


      foreach($all_tags_for_db as $tag_for_db){
        if(!(in_array($tag_for_db, $all_existing_tags))){
          $new_tags_to_add_to_db[] = $tag_for_db;
        }
      }

      //Add new tag to db tags table
      $new_tags_to_add_to_db = array_unique($new_tags_to_add_to_db);
      foreach($new_tags_to_add_to_db as $new_tag_for_db){
        $sql = "INSERT INTO tags ('tag') VALUES (:new_tag_for_db);";

        $params = array(
          ':new_tag_for_db' => $new_tag_for_db
        );

        $result = exec_sql_query($db, $sql, $params);
      }

      //Add entries in image_tags table
      foreach($all_tags_for_db as $tag_for_db){
        $tag_for_db_id_query = exec_sql_query($db, "SELECT id FROM tags WHERE :tag_for_db = tag", array(':tag_for_db' => $tag_for_db))->fetchAll();

        $tag_for_db_id = $tag_for_db_id_query[0]["id"];




        $sql = "INSERT INTO image_tags ('image_id', 'tag_id') VALUES (:add_id, :tag_for_db_id);";

        $params = array(
          ':add_id' => $add_id,
          ':tag_for_db_id' => $tag_for_db_id

        );

        $result = exec_sql_query($db, $sql, $params);
      }


    }
  }

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
      </form>
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
              <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo MAX_FILE_SIZE; ?>"/>
              <label for="img_file">Upload Image: </label>
              <input id="img_file" type="file" name="img_file"/>
            </li>

            <li>
              <label for="description">Image Description: </label>
              <input id="description" type="text" name="description"/>
            </li>

            <li>
              <label for="exist_tag">Existing Tags: </label>
              <select name="exist_tag[]" multiple >

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
          </ul>
      </fieldset>
    </form>

  </div>
<?php } ?>
  <?php include("includes/footer.php"); ?>
</body>
</html>
