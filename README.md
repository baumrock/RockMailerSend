# RockMailerSend

## Check if the API is working

```php
/** @var RockMailerSend $mailer */
$mailer = $modules->get('RockMailerSend');
$response = $mailer->api()->get('/domains');
if($response->hasStatus(200)) {
  foreach($response->result->data as $domain) echo "{$domain->name}<br>";
}
else {
  echo "Error getting domains from ".$mailer->api()->url()
}
```
