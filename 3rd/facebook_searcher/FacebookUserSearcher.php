<?php
require_once 'FacebookRequester.php';
class FacebookUserSearcher extends FacebookRequester{
  public function search($query, $type, $center, $distance) {
    $query_array = ['q' => $query, 'type' => $type];
    if (isset($center) && strlen($center) > 0 && strlen($distance) > 0) {
      $query_array['center'] = $center;
      $query_array['distance'] = $distance;
    }
    if ($type != "event") {
       $query_array['fields'] = "id,name,picture.width(700).height(700)";
    } else {
       $query_array['fields'] = "name,place,picture.width(700).height(700)";
    }

    $request = $this->fb->request('GET', "/search", $query_array);
    $this->results = $this->get_response($request);
  }
}
?>
