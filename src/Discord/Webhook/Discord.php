<?php declare(strict_types = 1);

namespace Syams\PhpDiscordClient\Discord\Webhook;

use Syams\PhpDiscordClient\Discord\Webhook\DataAdapter;
use GuzzleHttp\Client as HttpClient;

class Discord {
  private $data_adapter, $discord_message;

  public function __construct(
    DataAdapter $data_adapter,
    DiscordMessage $discord_message
  ) {
    $this->data_adapter    = $data_adapter;
    $this->discord_message = $discord_message;
  }

  /**
   * Set bot properties
   *
   * @param string $webhook_url
   * @param string $bot_name
   * @param string $bot_image_url
   * @return self
   */
  public function setBotProps(
    string $webhook_url,
    string $bot_name = '',
    string $bot_image_url = ''
  ): self {
    $this->validateValue('webhook_url', $webhook_url);
    $this->data_adapter->setWebhookUrl($webhook_url);
    $this->data_adapter->setAvatarUrl($bot_image_url);
    $this->data_adapter->setBotName($bot_name);
    return $this;
  }

  /**
   * Set content message
   *
   * @param string $content
   * @return self
   */
  public function setContentMessage(string $content): self {
    /**
     * Why content does not need validation?
     * - As long as it's empty string (not null), it's safe
     *
     * Why need to subtract by 2 ($limit_char - 2)?
     * - Because we're counting the dot character (..)
     */
    $limit_char = 2000;
    $content    = strlen($content) > $limit_char
      ? substr($content, 0, $limit_char - 2) . '..'
      : $content;

    $this->data_adapter->setContent($content);
    return $this;
  }

  /**
   * Send webhook to discord
   *
   * @param HttpClient $http_client
   * @return void
   */
  public function sendToDiscord(HttpClient $http_client): void {
    try {
      # 0. Validate all config
      $this->isConfigValid();

      # 1. Prepare discord message
      $discord_message = $this->discord_message->buildDiscordMessage();

      # 2. Send post request
      $response = $http_client->post(
        $this->data_adapter->getWebhookUrl(),
        ['json' => $discord_message]
      );

      # 3. Validate http status code
      $allowed_response_code = [200, 204];
      if (!in_array($response->getStatusCode(), $allowed_response_code))
        throw new \Exception('Guzzle post request returning error - ' . $response->getReasonPhrase());

    } catch (\GuzzleHttp\Exception\ClientException $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    } catch (\GuzzleHttp\Exception\ServerException $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    }
  }

  public function isConfigValid(): void {
    $webhook_url = $this->data_adapter->getWebhookUrl() ?? null;
    $this->validateValue('webhook_url', $webhook_url);
  }

  public function validateValue(string $field, $value): void {
    if (empty($value))
      throw new \Exception("Field {$field} is required");
  }
}
