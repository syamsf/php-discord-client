<?php declare(strict_types = 1);

namespace Syams\PhpDiscordClient\HttpClient;

use GuzzleHttp\Client as GuzzleClient;

class HttpRequest {
  private $http_client, $data_adapter;
  private $allowed_method = ['POST', 'GET', 'PUT', 'DELETE', 'HEAD'];

  public function __construct(
    GuzzleClient $guzzle_client,
    DataAdapter $data_adapter
  ) {
    $this->http_client = $guzzle_client;
    $this->data_adapter = $data_adapter;
  }

  public function sendHttpRequest(string $http_method): array {
    try {
      $this->isEndpointHasBeenSet();

      if (!in_array(strtoupper($http_method), $this->allowed_method))
        throw new \Exception('HTTP Method not allowed');

      if ($http_method == 'POST')
        return $this->sendPost();

      if ($http_method == 'GET')
        return $this->sendGet();
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    } catch (\GuzzleHttp\Exception\ClientException $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    }
  }

  private function sendPost(): array {
    $is_form_params_exists = empty($this->data_adapter->getFormParams()) ? false : true;
    $is_form_auth_exists = empty($this->data_adapter->getFormAuth()) ? false : true;
    $is_header_params_exists = empty($this->data_adapter->getHeaderParams()) ? false : true;

    $result = null;
    $form_attributes = [];

    if ($is_form_auth_exists)
      $form_attributes['auth'] = $this->data_adapter->getFormAuth();

    if ($is_form_params_exists)
      $form_attributes['form_params'] = $this->data_adapter->getFormParams();

    if ($is_header_params_exists)
      $form_attributes['headers'] = $this->data_adapter->getHeaderParams();

    $total_form_attribute = count(array_keys($form_attributes));

    $result = ($total_form_attribute > 0)
      ? $this->http_client->post($this->data_adapter->getEndpoint(), $form_attributes)
      : $this->http_client->post($this->data_adapter->getEndpoint());

    $response = $result->getBody()->getContents();
    $response_array = json_decode($response, true);

    return $response_array;
  }

  private function sendGet(): array {
    $is_form_auth_exists = empty($this->data_adapter->getFormAuth()) ? false : true;
    $is_header_params_exists = empty($this->data_adapter->getHeaderParams()) ? false : true;

    $result = null;
    $form_attributes = [];

    if ($is_form_auth_exists)
      $form_attributes['auth'] = $this->data_adapter->getFormAuth();

    if ($is_header_params_exists)
      $form_attributes['headers'] = $this->data_adapter->getHeaderParams();

    $total_form_attribute = count(array_keys($form_attributes));

    $result = ($total_form_attribute > 0)
      ? $this->http_client->get($this->data_adapter->getEndpoint(), $form_attributes)
      : $this->http_client->get($this->data_adapter->getEndpoint());

    $response = $result->getBody()->getContents();
    $response_array = json_decode($response, true);

    return $response_array;
  }

  private function isEndpointHasBeenSet() {
    if (empty($this->data_adapter->getEndpoint()))
      throw new \Exception('Endpoint is empty');
  }

  public function getDataAdapter(): DataAdapter  {
    return $this->data_adapter;
  }
}
