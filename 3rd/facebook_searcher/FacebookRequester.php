<?php
class FacebookRequester {
  protected $fb;
  protected $results;

  public function __construct($facebooksdk) {
    $this->fb = $facebooksdk;
  }

  protected function get_response($request) {
    try {
      $response = $this->fb->getClient()->sendRequest($request);
      $userNode = $response->getDecodedBody();
    } catch(Facebook\Exceptions\FacebookResponseException $e) {
      // When Graph returns an error
      echo 'Graph returned an error: ' . $e->getMessage();
      return null;
    } catch(Facebook\Exceptions\FacebookSDKException $e) {
      // When validation fails or other local issues
      echo 'Facebook SDK returned an error: ' . $e->getMessage();
      return null;
    }

    return $userNode;
  }

  public function get_json_resutls() {
    return json_encode(array('data' => $this->results));
  }

  public function get_results() {
    return $this->results;
  }
}
?>
