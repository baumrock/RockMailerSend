<?php

namespace ProcessWire;

/**
 * @author Bernhard Baumrock, 07.12.2022
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockMailerSend extends WireData implements Module, ConfigurableModule
{
  public $api;
  public $apitoken;
  private $from;
  private $subject = 'RockMailerSend';
  private $to = [];
  private $tpl;
  private $vars = [];

  public static function getModuleInfo()
  {
    return [
      'title' => 'RockMailerSend',
      'version' => '0.0.1',
      'summary' => 'MailerSend API Integration for ProcessWire',
      'autoload' => false,
      'singular' => true,
      'icon' => 'code',
      'requires' => [
        'RockApi',
        'PHP>=8.1',
      ],
    ];
  }

  public function init()
  {
  }

  public function api(): RockApi
  {
    if ($this->api) return $this->api;
    /** @var RockApi $api */
    $api = $this->wire->modules->get('RockApi');
    $api->url = $this->apiurl;
    $api->http()
      ->setHeader('Authorization', 'Bearer ' . $this->apitoken);
    return $this->api = $api;
  }

  private function buildReplacementsArray(): array
  {
    $vars = [];
    foreach ($this->to as $mailto) {
      $subs = [];
      foreach ($this->vars as $k => $v) {
        $subs[] = (object)[
          'var' => $k,
          'value' => $v,
        ];
      }
      $vars[] = (object)[
        'email' => $mailto->email,
        'substitutions' => $subs,
      ];
    }
    return $vars;
  }

  /**
   * Send request to MailerSend API
   */
  public function send()
  {
    $data = [
      'to' => $this->to,
      'from' => $this->from,
      'subject' => $this->subject,
    ];

    // use a template?
    if ($this->tpl) $data['template_id'] = $this->tpl;

    // set variables (replacements)?
    if (count($this->vars)) {
      $data['variables'] = $this->buildReplacementsArray();
    }

    // send mail
    // bdb($data, 'send mail');
    return $this->api()->post("/email", $data);
  }

  /** Self-Returning Mailer API */

  public function from($mail, $name = null): self
  {
    $this->from = (object)[
      'email' => $mail,
      'name' => $name,
    ];
    return $this;
  }

  /**
   * Set mail subject
   */
  public function subject($subject): self
  {
    $this->subject = $subject;
    return $this;
  }

  /**
   * Set template to use
   */
  public function template($id): self
  {
    $this->tpl = $id;
    return $this;
  }

  /**
   * Add a recipient
   */
  public function to($email, $name = null): self
  {
    $this->to[] = (object)[
      'email' => $email,
      'name' => $name
    ];
    return $this;
  }

  /**
   * Add variables to populate
   * Usage:
   * $mailer->vars([
   *   'foo' => 'my foo tag value',
   * ]);
   */
  public function vars(array $vars): self
  {
    $this->vars = array_merge($this->vars, $vars);
    return $this;
  }

  /** END Self-Returning Mailer API */

  /**
   * Config inputfields
   * @param InputfieldWrapper $inputfields
   */
  public function getModuleConfigInputfields($inputfields)
  {
    $inputfields->add([
      'type' => 'URL',
      'name' => 'apiurl',
      'value' => $this->apiurl,
      'label' => 'API Endpoint URL',
    ]);
    $inputfields->add([
      'type' => 'text',
      'name' => 'apitoken',
      'value' => $this->apitoken,
      'label' => 'API Bearer Token',
    ]);
    return $inputfields;
  }

  public function __debugInfo()
  {
    return [
      'api' => $this->api(),
      'to' => $this->to,
      'template' => $this->tpl,
      'vars' => $this->vars,
    ];
  }
}
