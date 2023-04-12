<?php
namespace Drupal\cs_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use \Symfony\Component\HttpFoundation\RedirectResponse;
use GuzzleHttp\Client;

class CSFormController extends ControllerBase {
  public function customPage() {
    $request = \Drupal::request();
    $name = $request->query->get('name');
    $email = $request->query->get('email');
    $phone = $request->query->get('phone');
    $description = $request->query->get('description');
    $orgid = $request->query->get('orgid');
    $retURL = $request->query->get('retURL');
    $type = $request->query->get('type');
    $external = $request->query->get('external');
    $sf_endpoint = $request->query->get('sf_endpoint');
    $api_key = \Drupal::config('cs_form.settings')->get('api_key');
    $list_id = \Drupal::config('cs_form.settings')->get('list_id');
    $success_page = \Drupal::config('cs_form.settings')->get('success_page');

    if ($name && $email && $description && $orgid && $retURL && $type && $external && $sf_endpoint) {
      $this->cm_subscribe($name, $email, $api_key, $list_id);
      $client = new Client();

      $data = [
        'orgid' => $orgid,
        'retURL' => $retURL,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'type' => $type,
        'description' => $description,
      ];

      $response = $client->post($sf_endpoint, [
        'form_params' => $data,
      ]);

      $body = (string) $response->getBody();
      \Drupal::logger('cs_form')->info('Salesforce Response: @response', ['@response' => $body]);

    } else if ($name && $email) {
      $this->cm_subscribe($name, $email, $api_key, $list_id);
    }

    $redirect_url = $success_page;
    $response = new RedirectResponse($redirect_url, 302, ['Refresh' => '3']);
    
    return $response;
  }

  private function cm_subscribe($name, $email, $api_key, $list_id) {
    $client = new Client([
      'base_uri' => 'https://api.createsend.com/api/v3.2/',
      'headers' => [
        'Authorization' => 'Basic ' . base64_encode($api_key . ':'),
        'Content-Type' => 'application/json',
      ],
    ]);
    $subscriber_data = [
      'EmailAddress' => $email,
      'Name' => $name,
      'Resubscribe' => true,
      'RestartSubscriptionBasedAutoresponders' => true,
      "ConsentToTrack" => "Yes",
    ];

    $json_data = json_encode($subscriber_data);

    $response = $client->post("subscribers/$list_id.json", [
      'body' => $json_data,
    ]);

    $body = (string) $response->getBody();

    if ($response->getStatusCode() == 201){
      \Drupal::logger('cs_form')->info('@email subscribe data sent to Campaign Monitor', ['@email' => $email]);
    } else {
      \Drupal::logger('cs_form')->info('Request to Campaign Monitor failed - @response', ['@response' => $body]);
    }

  }

}
