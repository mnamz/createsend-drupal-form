# Createsend Connector for Drupal form
Add subscribers to Campaign Monitor via Drupal form

## Usage
This module uses Guzzle to make API request to Campaign Monitor API. You can provide your API key to the configuration page `/admin/config/cs_form`

Create a form in any twig template. Use the normal form, add the action as follows.

```
<form method="get" action="/subscribe">
    <input type="text" name="name">
    <input type="email" name="email">
    <button type="submit">
</form>
```

Primarily this will collect the name and email from the input and send it to Createsend. More data can be modified as you wish in `Controller/CSFormController.php`

## Installation
- Download this repo
- Place this module in `modules/custom` folder
- Activate this module by installing it

If you wish to adjust the path for action page, you can edit the `cs_form.routing.yml` 

```
cs_form.subscribe:
  path: '/subscribe' # Handles form action
  defaults:
    _controller: '\Drupal\cs_form\Controller\CSFormController::customPage'
    _title: 'Callback'
  requirements:
    _permission: 'access content'
```

