<?php
header("Access-Control-Allow-Origin: *");
# Set Facebook SDK Object
$app = ['app_id' => '1607220136179574',
        'app_secret' => '27de02cb84bf6ba860fb19c4d2425dcd',
        'default_graph_version' => 'v2.8'];
$token = "EAADNAoDNw5wBAGXQrmwKXOpMhJT0kPnjd6pUYHdD8AiDk317wchZAvo5G4bWH2H8LG5elSLhXY4EdH7SnI94ZCtFdjOZAoybyfZCc3CIKmLz8VFAebc95W5Nzp3NoYwP1lO5zKaspclyvMp6h7ERtt1ZAqyuVDEcAFZA1NdD9CgAZDZD";

require_once '/Users/taekyoon/API/facebook/php-graph-sdk-5.0.0/src/Facebook/autoload.php';
$fb = new Facebook\Facebook($app);
$fb->setDefaultAccessToken($token);

$id = '';
if (isset($_GET['userid'])) { $id = $_GET['userid']; }

# Request Facebook Graph API with FacebookUserDetailGetter
include 'FacebookUserDetailGetter.php';
$facebook_detail = new FacebookUserDetailGetter($fb);
$facebook_detail->get_user_detail($id);

echo $facebook_detail->get_json_results();
?>
