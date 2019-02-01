# drupal-scripts

## Copy alt to captions
PHP script to copy content content from img alt attributes to data-caption attributes for use with D8 [Caption filter](https://www.drupal.org/project/caption_filter)

1. Download [copy_alt_to_captions.php](https://github.com/lizkrznarich/drupal-scripts/blob/master/copy_alt_to_captions.php) from this repository
2. Edit [copy_alt_to_captions.php lines 6-9](https://github.com/lizkrznarich/drupal-scripts/blob/master/copy_alt_to_captions.php#L6) to add your Drupal database connection info (as found in sites/default/settings.php)
3. To run a test that will show which nodes and images will be updated, set ```$really_update = FALSE``` on line 10
4. Upload copy_alt_to_captions.php to the web server that your Drupal site lives on, in a location that is publicly accessible on the Web (such as the root directory of your site).
5. Open a browser and navigate to the file you just uploaded, ex https://mysite.com/copy_alt_to_captions.php or https://mysite.com/somefoldername/copy_alt_to_captions.php . The script should begin running immediately and logging output to the browser window.
7. To make the database changes shown in the test, set ```$really_update = TRUE``` on line 10 and repeat steps 4-5
8. Log into your Drupal site, navigate to /admin/config/development/performance and click Clear all caches
