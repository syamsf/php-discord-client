<?php declare(strict_types = 1);

namespace Syams\PhpDiscordClient\Discord\Webhook;

/**
 * Data Adapter class
 * - Serves as getter and setter for discord webhook
 */
class DataAdapter {
  private $bot_name = 'Nabs-Bot', $content = '',
    $avatar_url = 'https://cdn140.picsart.com/e101520b-d12a-4083-b64b-fc62dc7b3ed5/389377023006211.png',
    $webhook_url = '', $is_tts_enabled = false, $mentioned_people_or_role;

  public function getBotName(): string {
    return $this->bot_name;
  }

  public function setBotName(string $bot_name): self {
    if (!empty($bot_name))
      $this->bot_name = $bot_name;

    return $this;
  }

  public function getAvatarUrl(): string {
    return $this->avatar_url;
  }

  public function setAvatarUrl(string $avatar_url): self {
    if (!empty($avatar_url))
      $this->avatar_url = $avatar_url;

    return $this;
  }

  public function getContent(): string {
    return $this->content;
  }

  public function setContent(string $content): self {
    $this->content = $content;
    return $this;
  }

  public function getWebhookUrl(): string {
    return $this->webhook_url;
  }

  public function setWebhookUrl(string $webhook_url): self {
    $this->webhook_url = $webhook_url;
    return $this;
  }

  public function getIsTtsEnabled(): bool {
      return $this->is_tts_enabled;
  }

  public function setIsTtsEnabled(bool $is_tts_enabled): self {
    $this->is_tts_enabled = $is_tts_enabled;
    return $this;
  }

  public function getMentionedPeopleOrRole(): array {
    return $this->mentioned_people_or_role;
  }

  public function setMentionedPeopleOrRole(array $mentioned_people_or_role): self {
    $this->mentioned_people_or_role = $mentioned_people_or_role;
    return $this;
  }
}
