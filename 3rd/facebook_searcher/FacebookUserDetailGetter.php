<?php
require_once 'FacebookRequester.php';
class FacebookUserDetailGetter extends FacebookRequester {
  public function get_user_detail($user_id) {
    $fields = 'albums.limit(5){name,photos.limit(5){picture.width(700).height(700)}},posts.limit(5)';
    $request = $this->fb->request('GET', "/$user_id", ['fields' => $fields]);
    $this->results = $this->get_response($request);
  }
}
?>
