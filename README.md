# v3s3-symfony3 (Using the Framework Specific Layer (FSL))
A simple storage system RESTful API written in Symfony 3<br />
<br />
This is part of a collection of repositories which provides an implementation of a Very Very Very Simple Storage System (V3s3) RESTful API using different PHP Frameworks. The project aims to directly compare the differences in setup and syntax between each of the represented frameworks.<br />
<br />
See the Wiki page for a hyperlinked list of all the repositories in the collection.

<hr />

# INSTALLATION AND IMPLEMENTATION SPECIFICS
1. Installing Symfony 3:<br />
```
cd /path/to/htdocs
mkdir symfony3-installer
php -r "file_put_contents('symfony-installer/symfony', file_get_contents('https://symfony.com/installer'));"
php ./symfony3-installer/symfony new v3s3-symfony3
```
This downloads the Symfony 3 installer (a PHP script) and saves it in the `/path/to/htdocs/symfony3-installer` directory. Then we run the installer using the PHP binary to create a new project in the `/path/to/htdocs/v3s3-symfony3` directory.<br />
2. Testing installation on Apache:<br />
If you prefer to avoid the overhead caused by Apache when loading and parsing `.htaccess` files you can use the provided `example_apache_virtualhost.conf` configuration to setup the necessary `mod_rewrite` directives. Once the server configuration is loaded we can open the Symfony index page by following the `localhost[:port]` URL.<br />
3. Installing the V3s3 bundle (module):<br />
Make sure that the V3s3 bundle is present in the `/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle` directory<br />
4. Configuration and V3s3 bundle (module) integration:<br />
The `composer.json` file needs to be modified to **psr-4** autoload all bundles within the `/path/to/htdocs/v3s3-symfony3/src` directory. Then we need to regenerate the autoload files by running `composer dump-autoload` from the command line.

<hr />

# NOTABLE NEW/MODIFIED PROJECT-SPECIFIC FILES:
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/V3s3Bundle.php** (create)<br />
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/Controller/DefaultController.php** (create)<br />
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/Entity/V3s3.php** (create)<br />
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/Exception/V3s3InputValidationException.php** (create)<br />
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/Helper/V3s3Html.php** (create)<br />
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/Helper/V3s3Xml.php** (create)<br />
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/Repository/V3s3Repository.php** (create)<br />
**/path/to/htdocs/v3s3-symfony3/src/V3s3Bundle/Resources/translations/V3s3.en.php** (create)<br />
<br />
**/path/to/htdocs/v3s3-symfony3/app/config/config.yml** (uncomment line 13)<br />
**/path/to/htdocs/v3s3-symfony3/app/config/parameters.yml** (create using the provided `.dist` file and modifying lines 5, 7, 8, 9)<br />
**/path/to/htdocs/v3s3-symfony3/app/config/routing.yml** (add lines 5-10)<br />
**/path/to/htdocs/v3s3-symfony3/app/AppKernel.php** (add line 19)<br />
<br />
**/path/to/htdocs/v3s3-symfony3/composer.json** (replace line 7 then run `composer dump-autoload` from the command line)