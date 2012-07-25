<?php
// Check for dependencies
if (!function_exists('curl_init'))
  throw new Exception('TwitterUsers needs the CURL PHP extension.');

if (!function_exists('json_decode'))
  throw new Exception('TwitterUsers needs the JSON PHP extension.');

/**
 * Wrapper to work with twitter users api.
 */
class TwitterUsers
{
  const API_BASE_URL = 'https://api.twitter.com/1/users/';

  /**
   * List of available api methods.
   */
  private $_methods = array(
    'lookup'        => array(),
    'profile_image' => array(),
    'search'        => array(),
    'show'          => array('screen_name'),
    'contributees'  => array(),
    'contributors'  => array(),
  );

  /**
   * Default response format.
   */
  private $_format    = '.json';

  /**
   * Default user-agent for requests.
   */
  private $_userAgent = 'TwitterUsers0.1.0';

  public function __construct() { }

  /**
   * Check if API method exists.
   *
   * @param string $name method name.
   * @param string $args list of arguments.
   * @return mixed result of api call if method name is correct.
   */
  public function __call($name, $args) {
    if (array_key_exists($name, $this->_methods)) {
      return $this->_call($name, $args);
    }
  }

  /**
   * Call specified api method.
   *
   * @param string $name method name.
   * @param array $args list of arguments.
   * @return json api response.
   */
  private function _call($name, $args) {
    $url_args = $this->_parseArgs($name, $args);
    $call_url = self::API_BASE_URL . $name . $this->_format . $url_args;
    $response = $this->_get($call_url);
    return json_decode($response);
  }

  /**
   * Parse args according to args schema.
   *
   * @param string $name api method name.
   * @param array $args list of arguments.
   * @return string url query string.
   */
  private function _parseArgs($name, $args) {
    $result = '?';
    $schema = $this->_methods[$name];
    foreach ($args as $i => $arg)
      $result .= $schema[$i] . '=' . $arg . '&';

    return rtrim($result, '&');
  }

  /**
   * Execute http get method.
   *
   * @param string $url request url.
   * @return string response.
   */
  private function _get($url) {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,            $url);
    curl_setopt($ch, CURLOPT_USERAGENT,      $this->_userAgent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    return curl_exec($ch);
  }
}
