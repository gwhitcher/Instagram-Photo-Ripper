<?php
/*
 * Instagram Photo Ripper by George Whitcher 12/2015
 */
include("config.php");
include("db.php");

$url = "https://api.instagram.com/v1/users/".$user_id."/media/recent?access_token=".$access_token."";
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$json = curl_exec($ch);
curl_close($ch);

$instaData = json_decode($json);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Instagram Photo Ripper</title>
    <style type="text/css">
        .container a:hover {
            text-decoration: none;
            cursor: pointer;
        }
        .instagram-unit {
            max-width: 310px;
            width: 100%;
            display: block;
            float: left;
            height: 390px;
            overflow: hidden;
            margin: 30px;
            border: 1px solid #CCC;
            padding: 5px;
            border-radius: 5px;
        }
        .instagram-unit img {
            max-width: 300px;
        }
        .img-responsive {
            margin: 0 auto;
        }
    </style>
    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>

</head>
<body>
<div class="container">
    <h1 class="page-header">Instagram Photo Ripper</h1>
    <?php
    //Display for page view
    foreach ($instaData->data as $post) {
        echo '<div class="instagram-unit">';
        echo '<a target="blank" data-toggle="modal" data-target="#myModal'.$post->id.'">';
        echo '<img src="'.$post->images->low_resolution->url.'" alt="'.$post->caption->text.'" />';
        echo '<div class="instagram-desc">'.htmlentities($post->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $post->caption->created_time)).'</div>';
        echo '</a>';
        echo '</div>';

        echo '<div id="myModal'.$post->id.'" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Instagram Image Preview</h4>
      </div>
      <div class="modal-body">
        <img class="img-responsive" src="'.$post->images->standard_resolution->url.'" alt="'.$post->caption->text.'" />
        <p><div class="label label-default">Description</div></br>'.htmlentities($post->caption->text).' | '.htmlentities(date("F j, Y, g:i a", $post->caption->created_time)).'</p>
        <p><div class="label label-default">Link to Instagram post</div></br><a href="'.$post->link.'" target="_blank">'.$post->link.'</a></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>';
    }
    //Save file to folder(s)
    foreach($instaData->data as $post) {
        $image_filename = basename($post->images->standard_resolution->url);
        copy($post->images->standard_resolution->url, ''.getcwd().'/images/'.$image_filename);
    }
    //Insert into database
    foreach($instaData->data as $post) {
        $image_filename = basename($post->images->standard_resolution->url);
        $image_description = $post->caption->text;
        $query = db_select("SELECT * FROM ".MYSQL_TABLE." WHERE filename = '".$image_filename."'");
        if(empty($query)) {
            db_query("INSERT INTO ".MYSQL_TABLE." (filename, description) VALUES ('".$image_filename."', '".$image_description."');");
        }
    }
    ?>
</div>
</body>
</html>