# uri

Ever wanted to get the right request URI/URL in PHP? This should do the trick

## API

```php

$ruri = $RequestURI::getInstance();
$ruri->getProtocol(); //returns http or https
$ruri->getHostname(); //returns the hostname
$ruri->getFilters(); //return the path as an array
$ruri->getGET(); //all GET parameters in an associative array
$ruri->getGivenFormat(); //when you add a .json to the end of the URI
$ruri->getRealWorldObjectURI(); //return URI without a format or without GET params
$ruri->getResourcePath(); //the path to the resource
$ruri->getURI(); //realworld object URI plus format and get params. The full URI.
```
