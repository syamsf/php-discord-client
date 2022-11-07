<?php declare(strict_types = 1);

namespace Syams\PhpDiscordClient\HttpClient;

class DataAdapter {
  private $endpoint, $form_params, $form_auth = [], $header_params = [];

  public function getEndpoint(): string {
    return $this->endpoint;
  }

  public function setEndpoint(string $endpoint): self {
    $this->endpoint = $endpoint;
    return $this;
  }

  public function getFormParams(): array {
    return $this->form_params;
  }

  public function setFormParams(array $form_params): self {
    $this->form_params = $form_params;
    return $this;
  }

  public function setFormAuth(array $form_auth): self {
    $this->form_auth = $form_auth;
    return $this;
  }

  public function getFormAuth(): array {
    return $this->form_auth;
  }

  public function setHeaderParams(array $header_params): self {
    $this->header_params = $header_params;
    return $this;
  }

  public function getHeaderParams(): array {
    return $this->header_params;
  }
}
