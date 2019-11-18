Project Title:

CrossLend Bid Test

Getting Started:

These instruction will help install and run the CrossLend Bid Test on your machine.



Prerequisites
 - have composer installed.
 - have apache2/nginx installed.
 - have PHP >= 7.1



Installing

 - Extract the project inside your webserver folder
 - Add your local ip to array inside app_dev.php line 16
 - Execute command (sudo composer install) to install vendors.
 - Access the project using your configured vhost or configure new one (https://symfony.com/doc/3.4/setup/web_server_configuration.html)


Problem occured:

 - the boundaries may sometimes be lower than system value (we may have boundary min 34->58, and the minimum system value is always 64 for number 0, solution SUM at this point is 0)
 - Requesting parity and boundary may sometimes raise an error which i handled using try catch and request again the same number to not loose system values.

 
