<?php
 // INCLUDE ON EVERY TOP-LEVEL PAGE!
include("includes/init.php");

$title = "View Image";

//find image that corresponds to id
if (isset($_GET['id'])){
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    global $id;
    $sql = "SELECT * FROM images WHERE id = :id;";
    $params = array(
        ':id' => $id
    );

    $records = exec_sql_query($db, $sql, $params)->fetchAll();

    $image_post = $records[0];

    global $image_post;

    $image_post_id_isset = True;
}

$image_post_id = $image_post["id"];

//TO DO: Delete Tags
// //Show all tags for the image
// $sql = "SELECT tags.tag FROM tags INNER JOIN image_tags ON tags.id = image_tags.tag_id WHERE :img_id = image_id;";

// $params = array(
//     ':img_id' => $id
// );

// $records_img_tags = exec_sql_query($db, $sql, $params)->fetchAll();

// var_dump($records_img_tags);

// foreach($records_img_tags as $record){
// //TO DO: DELETE TAG SQL UPDATES
//Only uploader can delete tags
// var_dump(isset($_POST['"'. $button_name . '"']));
// if ( isset($_POST['"delete_' . $record['tag'] . '"']) && $online_user["id"] ==  $image_post["user_id"]){
//     echo "pressed delete tag button block";
//     $img_tag = $record["tag"];
//     $sql = "DELETE image_tags.id, image_tags.image_id, image_tags.tag_id FROM image_tags INNER JOIN tags ON tags.id = image_tags.tag_id WHERE (:img_tag = tags.tag);";

//     $params = array(
//         ':img_tag' => $img_tag
//     );

//     $result = exec_sql_query($db, $sql, $params);
// }
// }
//TO DO: FILTER ADD TAG SQL UPDATES

var_dump(isset($_POST["add_exist_tag"]));

if ( isset($_POST["add_tags_button"])){
    //FILTER EXISTING TAGS
  if (isset($_POST["add_exist_tag"]) && $_POST["add_exist_tag"] != ""){
    $add_exist_tags_unfiltered = $_POST["add_exist_tag"];
    $add_exist_tags_filtered = array();
    foreach($add_exist_tags_unfiltered as $add_exist_tag_unfilt){
      $add_exist_tags_filtered[] = filter_var($add_exist_tag_unfilt, FILTER_SANITIZE_STRING);
    }
  }


  //FILTER NEW TAG
  if (isset($_POST["add_new_tag"])&& $_POST["add_new_tag"] != "") {
    $add_new_tag = filter_input(INPUT_POST, "add_new_tag", FILTER_SANITIZE_STRING);
    //Separate string input by commas to create new_tags array
    $add_new_tags_filtered_split = explode(",", $add_new_tag);
    $add_new_tags_filtered = array();
    foreach ($add_new_tags_filtered_split as $add_new_tag_filtered){
      $add_new_tag_filtered = trim($add_new_tag_filtered);
      $add_new_tag_filtered = strtolower($add_new_tag_filtered);
      $add_new_tags_filtered[] = $add_new_tag_filtered;
    }
  }

  //Adding tags and imgs to db with upload
  $all_tags_for_db = array();
  if(isset($_POST["add_exist_tag"]) && $_POST["add_exist_tag"] != ""){
    $all_tags_for_db = $add_exist_tags_filtered;

    if(isset($_POST["add_new_tag"]) && $_POST["add_new_tag"] != ""){
      //merge the existing tag and new tag array w/o dupes
      $all_tags_for_db = array_unique(array_merge($all_tags_for_db, $add_new_tags_filtered));
    }
    var_dump($all_tags_for_db);
  }

  elseif(isset($_POST["add_new_tag"])&& $_POST["add_new_tag"] != ""){
    $all_tags_for_db = $add_new_tags_filtered;
  }

  if(!(empty($all_tags_for_db))){
    if(isset($_POST["add_new_tag"])&& $_POST["add_new_tag"] != ""){
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
    }

    //check for duplicates w existing tags of image

    $all_existing_tags_img = exec_sql_query($db, "SELECT DISTINCT tag FROM tags INNER JOIN image_tags ON image_tags.tag_id = tags.id INNER JOIN images ON image_tags.image_id = images.id WHERE :img_id = images.id", array(':img_id' => $image_post_id))->fetchAll(PDO::FETCH_COLUMN);

    $all_tags_to_add_db = array();
    var_dump($all_tags_for_db);
    foreach($all_tags_for_db as $tag_for_db){
        if(!(in_array($tag_for_db, $all_existing_tags_img))){
          $all_tags_to_add_db[] = $tag_for_db;
        }
      }
    var_dump($all_tags_to_add_db);

    //Add entries in image_tags table
    var_dump($all_tags_for_db);
    $all_tags_to_add_db = array_unique($all_tags_to_add_db);
    foreach($all_tags_to_add_db as $tag_for_db){
      $tag_for_db_id_query = exec_sql_query($db, "SELECT id FROM tags WHERE :tag_for_db = tag", array(':tag_for_db' => $tag_for_db))->fetchAll();

      $tag_for_db_id = $tag_for_db_id_query[0]["id"];
      var_dump($tag_for_db_id_query);




      $sql = "INSERT INTO image_tags ('image_id', 'tag_id') VALUES (:image_post_id, :tag_for_db_id);";

      $params = array(
        ':image_post_id' => $image_post_id,
        ':tag_for_db_id' => $tag_for_db_id

      );

      $result = exec_sql_query($db, $sql, $params);
    }
}
  }
  echo "Executed sql";


//DELETE IMAGE SQL UPDATE
if( isset($_POST["delete_img"]) && $online_user["id"] ==  $image_post["user_id"]){
    //delete image from disk
    $image_post_path = 'uploads/images/' . htmlspecialchars($image_post["id"]) . '.' . htmlspecialchars($image_post["img_ext"]);

    //don't want to unlink seed images
    $seed_img_array = array([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
    if (in_array($image_post_id, $seed_img_array)){
        unlink($image_post_path);
    }

    //delete image record from relevant tables
    $sql = "DELETE FROM images WHERE (:image_post_id = images.id);";

    $params = array(
        ':image_post_id' => $image_post_id
    );

    $result = exec_sql_query($db, $sql, $params);

    $sql = "DELETE FROM image_tags WHERE (:image_post_id = image_tags.image_id);";

    $params = array(
        ':image_post_id' => $image_post_id
    );

    $result = exec_sql_query($db, $sql, $params);


}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" type="text/css" href="styles/all.css" media="all" />

  <title>View Image</title>
</head>

<body>
  <?php include("includes/header.php");?>
  <!-- TODO: This should be your main page for your site. -->
  <hr />
  <?php if (! (isset($_POST["delete_img"]))){ ?>
  <div class="mainImgSoloDiv">
    <div class="imgSoloDiv">
        <?php echo '<img alt="' . htmlspecialchars($image_post["a_description"]) . '" src="uploads/images/' . htmlspecialchars($image_post["id"]) . '.' . htmlspecialchars($image_post["img_ext"]) . '"/>' ?>
    </div>

    <div class = "imgSoloDetailsDiv">
        <h2>Tags</h2>
        <div class="tagListDiv">
        <?php
        //Show all tags for the image

        $sql = "SELECT tags.tag FROM tags INNER JOIN image_tags ON tags.id = image_tags.tag_id WHERE :img_id = image_id;";

        $params = array(
            ':img_id' => $id
        );

        $records_img_tags = exec_sql_query($db, $sql, $params)->fetchAll();

        foreach ($records_img_tags as $record){
            echo '<div class="tagItem"><li>' . ucfirst(htmlspecialchars($record["tag"])) . '</li>';

            //only uploader can delete tags
            if($online_user["id"] ==  $image_post["user_id"]){
                // echo '<form class="delTagForm" action="post.php?' . http_build_query( array( "id" => $image_post["id"] ) ) . '" method="post"><button name="delete_' . $record['tag'] . '"' .  ' type="submit">Delete</button></form>';

                $button_name = "delete_" . $record["tag"];

                echo '<form class="delTagForm" action="post.php?' . http_build_query( array( "id" => $image_post["id"] ) ) . '"  method="post"><button name="' . $button_name .'" type="submit">Delete</button></form>';

            }


            echo "</div>";

    }
        ?>
        </div>

        <div class="addTagsDiv">
        <form id="addTagsForm" action="<?php echo 'post.php?' . http_build_query( array( "id" => $image_post["id"] ) ); ?>" method="post">
      <fieldset>
      <legend>Add Tags</legend>
          <ul>
            <li>
              <label for="add_exist_tag">Add Existing Tags: </label>
              <select name="add_exist_tag[]" multiple>

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
              <label for="add_new_tag">Add New Tags (separated by commas): </label>
              <input id="add_new_tag" type="text" name="add_new_tag"/>
            </li>

            <li>
              <button name="add_tags_button" type="submit">Add Tags</button>
            </li>
          </ul>
      </fieldset>
    </form>
        </div>

    <?php
        //only uploader can delete image
        if($online_user["id"] ==  $image_post["user_id"]){
            echo '<form id="delImgForm" action="post.php?' . http_build_query( array( "id" => $image_post["id"] ) ) . '" method="post"><button name="delete_img" type="submit">Delete Image</button></form>';

        // if($online_user["id"] ==  $image_post["user_id"]){
        //     echo '<form id="delImgForm" action="index.php" method="post"><button name="delete_img" type="submit">Delete Image</button></form>';
}

    ?>



    </div>

  </div>
<?php } ?>
  <button><a href="index.php">Return Home</a></button>
    <?php include("includes/footer.php"); ?>
</body>
</html>
