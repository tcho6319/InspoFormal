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

    $image_post_id_isset = True;
}

//TO DO: DELETE TAG SQL UPDATES
//Only uploader can delete tags
if ( isset($_POST['"delete_' . $record['tag'] . '"']) && $online_user["id"] ==  $image_post["user_id"]){
    $img_tag = $record["tag"];
    $sql = "DELETE image_tags.id, image_tags.image_id, image_tags.tag_id FROM image_tags INNER JOIN tags ON tags.id = image_tags.tag_id WHERE (:img_tag = tags.tag);";

    $params = array(
        ':img_tag' => $img_tag
    );

    $result = exec_sql_query($db, $sql, $params);
}
//TO DO: FILTER ADD TAG SQL UPDATES

//TO DO: DELETE IMAGE SQL UPDATE

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

        $records = exec_sql_query($db, $sql, $params)->fetchAll();

        foreach ($records as $record){
            echo '<div class="tagItem"><li>' . ucfirst(htmlspecialchars($record["tag"])) . '</li>';

            //only uploader can delete tags
            if($online_user["id"] ==  $image_post["user_id"]){
                echo '<form id="delTagForm" action="post.php?' . http_build_query( array( "id" => $record["id"] ) ) . '" method="post"><button name="delete_' . $record['tag'] . '" type="submit">Delete</button></form>';
            }


            echo "</div>";
        }
        ?>
        </div>

        <div class="addTagsDiv">
        <form id="addTagsForm" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] ); ?>" method="post">
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
            echo '<form id="delImgForm" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post"><button name="delete_img" type="submit">Delete Image</button></form>';
}

    ?>



    </div>

  </div>
  <button><a href="index.php">Return Home</a></button>
    <?php include("includes/footer.php"); ?>
</body>
</html>
