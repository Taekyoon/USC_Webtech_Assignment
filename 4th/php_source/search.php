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

$name = ''; $type = ''; $distance = ''; $location = '';
if (isset($_GET['name'])) { $name = $_GET['name']; }
if (isset($_GET['distance'])) { $distance = $_GET['distance']; }
if (isset($_GET['location'])) { $location = $_GET['location']; }

# Request Facebook Graph API with FacebookUserSearcher
include 'FacebookUserSearcher.php';
$facebook_search = new FacebookUserSearcher($fb);
$facebook_search->search($name, 'user', $geo_location, $distance);
$user_results = $facebook_search->get_results();

$facebook_search->search($name, 'page', $geo_location, $distance);
$page_results = $facebook_search->get_results();

$facebook_search->search($name, 'event', $geo_location, $distance);
$event_results = $facebook_search->get_results();

$facebook_search->search($name, 'place', $geo_location, $distance);
$place_results = $facebook_search->get_results();

$facebook_search->search($name, 'group', $geo_location, $distance);
$group_results = $facebook_search->get_results();

echo json_encode(['user' => $user_results,
                  'page' => $page_results,
                  'event' => $event_results,
                  'place' => $place_results,
                  'group' => $group_results]);
?>
