<?php declare(strict_types = 1);

namespace Syams\PhpDiscordClient\Discord\Webhook;

use Syams\PhpDiscordClient\Discord\Webhook\DataAdapter;
use Syams\PhpDiscordClient\Discord\Webhook\DiscordEmbedObject;

class DiscordMessage {
  private $data_adapter, $embed_object;

  public function __construct(DataAdapter $data_adapter, DiscordEmbedObject $embeds_object) {
    $this->data_adapter = $data_adapter;
    $this->embed_object = $embeds_object;
  }

  public function buildDiscordMessage(): array {
    $data = [
      'username'    => $this->data_adapter->getBotName(),
      'avatar_url'  => $this->data_adapter->getAvatarUrl(),
      'content'     => $this->data_adapter->getContent(),
      'tts'         => $this->data_adapter->getIsTtsEnabled()
    ];

    $data['embeds'] = $this->embed_object->getEmbedData();

    return $data;
  }

  /**
   * Parsing mentioned people or role from array to string
   *
   * @param array $mentioned_object
   * @return string
   */
  public function parseMentionedPeopleOrRole(array $mentioned_object): string {
    $user = $this->reformatMentionedObjectToString($mentioned_object['user'] ?? []);
    $role = $this->reformatMentionedObjectToString($mentioned_object['role'] ?? [], 'role');

    return "{$user} {$role}";
  }

  /**
   * Iteration to append prefix and suffix to mentioned people or role
   *
   * @param array $mentioned_object
   * @param string $type_object
   * @return string
   */
  public function reformatMentionedObjectToString(
    array $mentioned_object,
    string $type_object = 'user'
  ): string {
    $parsed_mentioned_object = '';
    $prefix = ['role' => '<@&', 'user' => '<@'];
    $suffix = '>';

    if (empty($mentioned_object))
      return '';

    if (!in_array($type_object, array_keys($prefix)))
      throw new \Exception('Type object is not listed in prefix');

    $total_object = count($mentioned_object);
    $counter = 1;
    foreach ($mentioned_object as $item) {
      $parsed_mentioned_object .= "{$prefix[$type_object]}{$item}{$suffix}";

      if ($counter != $total_object)
        $parsed_mentioned_object .= ' ';

      $counter++;
    }

    return $parsed_mentioned_object;
  }

  public function getOtherprops() {
    return $this->other_props;
  }
}
