# HoneysPlace
A Drupal 8/9 Module for connecting to Honey's Place API

# Install
- Install with composer

         composer require matt/honeys_place
         
- Enable the module

         drush pm-enable honeys_place
         
- Add the field_honey_order_created to your preferred Commerce Order Type bundle(s)
  
         Commerce > Configuration > Orders > Order Types > Default > Edit Fields > Add Field > field_honey_order_created

- Configure the API credentials on the configuration page
