<?php
if(isset($_POST) || isset($_GET)) {
  # Set Facebook SDK Object
  $app = ['app_id' => '1607220136179574',
          'app_secret' => '27de02cb84bf6ba860fb19c4d2425dcd',
          'default_graph_version' => 'v2.8'];
  $token = "EAADNAoDNw5wBAGXQrmwKXOpMhJT0kPnjd6pUYHdD8AiDk317wchZAvo5G4bWH2H8LG5elSLhXY4EdH7SnI94ZCtFdjOZAoybyfZCc3CIKmLz8VFAebc95W5Nzp3NoYwP1lO5zKaspclyvMp6h7ERtt1ZAqyuVDEcAFZA1NdD9CgAZDZD";

  require_once '/Users/taekyoon/API/facebook/php-graph-sdk-5.0.0/src/Facebook/autoload.php';
  $fb = new Facebook\Facebook($app);
  $fb->setDefaultAccessToken($token);

  # Placeholder for holding form data
  $name = ''; $type = ''; $distance = ''; $location = '';

  if (isset($_POST["name"]) && isset($_POST["type"])) {
    $name = $_POST["name"]; $type = $_POST["type"];
  } else {
    $name = $_GET["name"]; $type = $_GET["type"];
  }

  if (isset($_POST["distance"]) && isset($_POST["location"])) {
    $distance = $_POST["distance"]; $location = $_POST["location"];
  } else {
    $distance = $_GET["distance"]; $location = $_GET["location"];
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
form {
    margin: auto;
    align-content: center;
    width: 50%;
}

h3 {
  text-align: center;
}

img {
  margin-right: auto;
}

table {
  width: 100%;
}

th {
  background-color: lightgray;
  text-align: left;
}

#content-container {
  margin: 3% auto;
  width: 60%;
}

#detail-content-box, #error-content-box {
  margin-top: 3%;
  margin-bottom: 3%;
  width: 100%;
  text-align: center;
  background-color: lightgray;
  border-color: gray;
  border-style: solid;
}

#type_selector {
  margin-left: 4%;
}
</style>
<script type="text/javascript">
function showClassElement(selector) {
  // get a reference to your element, or it's container
  var myElement = document.getElementsByClassName(selector)
  if (myElement) {
    for (var i = 0; i < myElement.length; ++i) {
      myElement[i].style.display = ''
    }
  }
}

function hideClassElement(selector) {
  var myElement = document.getElementsByClassName(selector)
  if (myElement) {
    for (var i = 0; i < myElement.length; ++i) {
      myElement[i].style.display = 'none'
    }
  }
}

function showIdElement(selector) {
  // get a reference to your element, or it's container
  var myElement = document.getElementById(selector)
  if (myElement) {
    myElement.style.display = ''
  }
}

function hideIdElement(selector) {
  var myElement = document.getElementById(selector)
  if (myElement) {
    myElement.style.display = 'none'
  }
}

function showAlbums() {
  var show = document.getElementById('album_button').getAttribute("show")
  if (show === 'true') {
    hideClassElement('albums')
    if (document.getElementById('album_button')) {
      document.getElementById('album_button').setAttribute("show", "false")
    }
      releaseShow()
  } else {
    hideClassElement('posts')
    if (document.getElementById('post_button')) {
      document.getElementById('post_button').setAttribute("show", "false")
    }
    showIdElement('detail_album_result')
    if (document.getElementById('album_button')) {
      document.getElementById('album_button').setAttribute("show", "true")
    }
  }
}

function releaseShow() {
  var elements = document.getElementsByClassName('titles')
  if (elements) {
    for (var i = 0; i < elements.length; ++i) {
      elements[i].getElementsByTagName('a')[0].setAttribute("show", "false")
    }
  }
}

function showPosts() {
  var show = document.getElementById('post_button').getAttribute("show")
  if (show === 'true') {
    hideIdElement('detail-result')
    document.getElementById('post_button').setAttribute("show", "false")
  } else {
    hideClassElement('albums')
    document.getElementById('album_button').setAttribute("show", "false")
    releaseShow()
    showIdElement('detail-result')
    document.getElementById('post_button').setAttribute("show", "true")
  }
}

function showPhotos(id) {
  var show = document.getElementById(id).getAttribute("show")
  var selector = 'photos_' + id.split('_')[1]
  if (show === 'true') {
    hideIdElement(selector)
    document.getElementById(id).setAttribute("show", "false")
  } else {
    showIdElement(selector)
    document.getElementById(id).setAttribute("show", "true")
  }
}

function displayPlaceOption() {
  var selector = document.getElementById("type_selector")
  var selected_item = selector.options[selector.selectedIndex].value
  if (selected_item == "place") {
    showIdElement("place_option")
  } else {
    hideIdElement("place_option")
  }
}

function clearInputForm() {
  // clearing inputs
   var inputs = document.getElementsByTagName('input')
   for (var i = 0; i<inputs.length; i++) {
       switch (inputs[i].type) {
           // case 'hidden':
           case 'text':
               if(inputs[i].name == 'name') {
                 inputs[i].removeAttribute('value')
               }
               inputs[i].value = ''
               break;
           case 'radio':
           case 'checkbox':
               inputs[i].checked = false
       }
   }

   // clearing selects
   var selects = document.getElementsByTagName('select')
   for (var i = 0; i<selects.length; i++)
       selects[i].selectedIndex = 0

   // clearing textarea
   var text= document.getElementsByTagName('textarea')
   for (var i = 0; i<text.length; i++)
       text[i].innerHTML = ''

   var content = document.getElementById("content-container")
   content.innerHTML = ''

   hideIdElement("place_option");
 }
</script>
</head>
<body>
<form id="input-content" method="POST" action="./main.php">
  <fieldset>
    <h3>Facebook Searcher</h3>
    <p><label for="keyword">Keyword</label>
      <input type="text" name="name" <?php echo isset($name) ? 'value="'. $name. '"' : "" ?> required>
    </p>
    <p><label for="type">Type </label>
      <select id="type_selector" name="type" onchange="displayPlaceOption()">
        <?php if(strlen($type) > 0): ?>
          <option value="user" <?php echo $type == "user" ? "selected" : "" ?>>Users</option>
          <option value="page" <?php echo $type == "page" ? "selected" : "" ?>>Pages</option>
          <option value="event" <?php echo $type == "event" ? "selected" : "" ?>>Events</option>
          <option value="place" <?php echo $type == "place" ? "selected" : "" ?>>Places</option>
          <option value="group" <?php echo $type == "group" ? "selected" : "" ?>>Groups</option>
        <?php else: ?>
          <option value="user">Users</option>
          <option value="page">Pages</option>
          <option value="event">Events</option>
          <option value="place">Places</option>
          <option value="group">Groups</option>
        <?php endif; ?>
      </select>
    </p>
    <p id="place_option" style=<?php echo $type == "place" ? "" :"display:none" ?>>
      <label for="location">Location</label>
      <input type="text" name="location" value="<?php echo isset($location) ? $location : "" ?>">
      <label for="distance">Distance</label>
      <input type="text" name="distance" value="<?php echo isset($distance) ? $distance : "" ?>">
    </p>
    <input type="submit" name="search" value="Search">
    <input type="button" name="clear" value="Clear" onclick="clearInputForm()">
  </fieldset>
</form>
<div id="content-container">
<?php if(isset($_POST["name"]) && isset($_POST["type"])): ?>
  <?php
  include 'FacebookUserSearcher.php';

  $geo_location = '';
  if (strlen($location) > 0) {
    include 'GoogleGeocodeTranslator.php';
    $geocode_translator = new GoogleGeocodeTranslator("AIzaSyC1Etvxq5Lb1amniwvvd_bu3h0EfuCEWjg");
    $geocode_translator->request_geocode($location);
    $geo_location = $geocode_translator->get_results();
  }
  $facebook_search = new FacebookUserSearcher($fb);
  $facebook_search->search($name, $type, $geo_location, $distance);
  $results = $facebook_search->get_results();
  ?>
  <?php if (isset($results['data']) && count($results['data']) > 0): ?>
    <table id="search_result" border="1">
    <tr><th>Profile Photo</th><th>Name</th><th><?php echo $type != "event" ? "Details" : "Place" ?></th></tr>
    <?php foreach ($results['data'] as $item): ?>
    <tr>
      <td><a href="<?php echo $item['picture']['data']['url'] ?>">
          <img src="<?php echo $item['picture']['data']['url'] ?>" width='60px' height='60px'/>
      </a></td>
      <td><?php echo $item['name'] ?></td>
      <?php if ($type != "event"): ?>
        <?php
          $link_url = "?name=$name&type=$type";
          if (isset($_POST['distance']) && isset($_POST['location'])) {
            $link_url = $link_url. "&distance=$distance&location=$location";
          }
          $link_url = $link_url. "&userid=". $item['id'];
        ?>
        <td><a href="<?php echo $link_url?>">Details</a></td>
      <?php else: ?>
        <td><?php echo $item['place']['name'] ?></td>
      <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </table>
  <?php else: ?>
    <div id="error-content-box">No Records has been found</div>
  <?php endif; ?>
<?php endif; ?>
<?php if (isset($_GET['userid'])): ?>
  <?php
  include 'FacebookUserDetailGetter.php';
  $facebook_detail = new FacebookUserDetailGetter($fb);
  $facebook_detail->get_user_detail($_GET['userid']);
  $results = $facebook_detail->get_results();
  ?>
  <?php if (isset($results['albums']['data']) && count($results['albums']['data']) > 0): ?>
    <div id='detail-content-box'>
      <a id="album_button" href="javascript:void(0);" onclick="showAlbums()" show="false">Albums</a>
    </div>
    <div id="detail_album_result" class="albums" style="display:none;">
    <table border="1">
    <?php $index = 0 ?>
    <?php foreach ($results['albums']['data'] as $items) : ?>
    <tr class="titles"><td>
      <a id="album_<?php echo $index ?>" href="javascript:void(0);" onclick="showPhotos(this.id)" show="false">
        <?php echo $items['name'] ?>
      </a>
    </td></tr>
    <tr id=photos_<?php echo $index++ ?> class="albums" style="display:none;"><td>
      <?php if (count($items['photos']['data'])>0): ?>
        <?php foreach ($items['photos']['data'] as $sub_items): ?>
        <a href="<?php echo $sub_items['picture'] ?>">
          <img src="<?php echo $sub_items['picture'] ?>" width='100px' height='100px'/>
        </a>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No Photos have been found</P>
      <?php endif; ?>
    </td></tr>
    <?php endforeach; ?>
    </table>
    </div>
  <?php else: ?>
    <div id="error-content-box">No Albums has been found</div>
  <?php endif; ?>
  <?php if (isset($results['posts']['data']) && count($results['posts']['data']) > 0): ?>
    <div id="detail-content-box">
      <a id="post_button" show="false" href="javascript:void(0);" onclick="showPosts()">Posts</a>
    </div>
    <div id="detail-result" style="display:none;">
    <table border="1">
    <tr><th>Messages</th></tr>
    <?php foreach ($results['posts']['data'] as $items): ?>
    <tr><td>
      <p><?php echo $items['message'] ?></p>
    </td></tr>
    <?php endforeach; ?>
    </table>
    </div>
  <?php else: ?>
    <div id="error-content-box">No Posts has been found</div>
  <?php endif; ?>
<?php endif; ?>
</div>
</body>
</html>
