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

## Send Mail

```php
/** @var RockMailerSend $mailer */
$mailer = $modules->get('RockMailerSend');
$result = $mailer
  ->from('foo@bar.com', 'Foo Company')
  ->to('bar@foo.com')
  ->subject('Thank you for your order!')
  ->template('1234') // mailersend mail template
  ->vars([
    "foo" => "Foo Value",
    "bar" => "Bar Value",
  ])
  ->send();
```
