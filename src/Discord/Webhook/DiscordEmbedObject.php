<?php declare(strict_types = 1);

namespace Syams\PhpDiscordClient\Discord\Webhook;

class DiscordEmbedObject {
  # TODO: optimization in this class, in order to remove quadratic complexity of Big 0

  private $allowed_embed_object = [
    'title'       => ['type' => 'string'],
    'description' => ['type' => 'string'],
    'url'         => ['type' => 'string'],
    'color'       => ['type' => 'integer'],
    'footer'      => ['column' => ['text', 'icon_url']],
    'image'       => ['column' => ['url', 'height', 'width']],
    'thumbnail'   => ['column' => ['url', 'height', 'width']],
    'video'       => ['column' => ['url', 'height', 'width']],
    'provider'    => ['column' => ['url', 'height', 'width']],
    'author'      => ['column' => ['name', 'url', 'icon_url']],
    'fields'      => ['type' => 'array', 'column' => ['name', 'value', 'inline']]
  ], $embed_data = [];

  public function appendToEmbeds(array $embed_data_provider): void {
    # Validate when items is more than 10, than it's invalid
    if (count($embed_data_provider) > 10)
      throw new \Exception("Item should not be more than 10");

    $push_counter = 0;
    foreach ($embed_data_provider as $item) {
      # Validate valid embed field name
      $this->validateEmbedField($item);

      # Push to embeds object
      $this->pushToEmbedObject($item, $push_counter);

      $push_counter++;
    }
  }

  public function getEmbedData(): array {
    return $this->embed_data;
  }

  /**
   * Validate embed field
   *
   * @param array $embed_field
   * @return void
   */
  public function validateEmbedField(array $embed_field): void {
    foreach ($embed_field as $key => $value) {
      if (!in_array($key, array_keys($this->allowed_embed_object)))
        throw new \Exception("Field {$key} is not allowed");

      $type = $this->allowed_embed_object[$key]['type'] ?? null;
      if (is_null($type))
        continue;

      if ($type == 'string')
        if (!is_string($value))
          throw new \Exception("{$key} is not string");

      if ($type == 'integer')
        if (!is_int($value))
          throw new \Exception("{$key} is not integer");

      if ($type == 'array') {
        if (!is_array($value))
          throw new \Exception("{$key} is not array");

        if (count($value) > 10)
          throw new \Exception("Embed fields should not be more than 10");
      }
    }
  }

  /**
   * Parsing other embed field (excepts fields)
   *
   * @param array $embed_item
   * @param array $item_value
   * @param string $key
   * @param integer $push_counter
   * @return void
   */
  public function parseEmbedField(
    array $embed_item,
    array $item_value,
    string $key,
    int $push_counter
  ): void {
    foreach ($embed_item as $sub_key) {
      $this->embed_data[$push_counter][$key][$sub_key] = $item_value[$sub_key];
    }
  }

  /**
   * Parsing fields column
   *
   * @param array $item_value
   * @param string $key
   * @param integer $push_counter
   * @return void
   */
  public function parseFieldsColumn(
    array $item_value,
    string $key,
    int $push_counter
  ): void {
    $array_column_counter = 0;

    foreach ($item_value as $key_field) {
      $this->embed_data[$push_counter][$key][$array_column_counter]['name'] = $key_field['name'];
      $this->embed_data[$push_counter][$key][$array_column_counter]['value'] = $key_field['value'];

      $array_column_counter++;
    }
  }

  /**
   * Push to newly formatted embed object
   * Will override any key if there's same value
   * [title, title] => will only result 1 title with last value
   *
   * @param array $embed_item
   * @param integer $push_counter
   * @return void
   */
  public function pushToEmbedObject(array $embed_item, int $push_counter): void {
    foreach ($embed_item as $key => $value) {
      $allowed_column = $this->allowed_embed_object[$key]['column'] ?? null;

      if (!is_null($allowed_column)) {
        if ($key == 'fields') {
          $this->parseFieldsColumn($value, $key, $push_counter);
          continue;
        }

        # Get key from embed_item value
        $item_key = array_keys($value);

        # Filter allowed_column only from embed_item value
        $intersect_key = array_intersect($item_key, $allowed_column);

        # Parse other embed field
        $this->parseEmbedField($intersect_key, $value, $key, $push_counter);

        continue;
      }

      $this->embed_data[$push_counter][$key] = $value;
    }
  }
}
