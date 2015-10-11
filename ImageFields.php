<?php
/*
Plugin Name: Image Fields
Description: Manage uploaded images metadata
Version: 0.1
Author: Dmitry Yakovlev
Author URI: http://dimayakovlev.ru/
*/

$DY_IMAGE_FIELDS = basename(__FILE__, ".php");

# register plugin
register_plugin(
  $DY_IMAGE_FIELDS,                                       # ID of plugin, should be filename minus php
  'Image Fields',                                         # Title of plugin
  '0.1',                                                  # Version of plugin
  'Dmitry Yakovlev',                                      # Author of plugin
  'http://dimayakovlev.ru/',                              # Author URL
  'Metadata for uploaded images',                         # Plugin Description
  '',                                                     # Page type of plugin
  ''                                                      # Function that displays content
);

add_action('image-extras', 'dyImageFieldsExtras');

function dyImageFieldsExtras() {
  global $DY_IMAGE_FIELDS;
  global $src_url;
  global $src;
  $image = $src_url . $src;  
  $xml_path = GSDATAOTHERPATH . 'images.xml';
  $image_title = $image_keywords = $image_description = null;
  
  if ($xml = getXML($xml_path))
    $current = $xml->xpath('./image[path = "' . $image . '"]');  
  
  if (isset($_POST['submitted'])) {
    
    $image_title = isset($_POST['image-title']) ? $_POST['image-title'] : '';
    $image_keywords = isset($_POST['image-keywords']) ? $_POST['image-keywords'] : '';
    $image_description = isset($_POST['image-description']) ? $_POST['image-description'] : '';
    
    if (!$xml)
      $xml = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8"?><images></images>');
    
    if (isset($current[0])) {
      
      $current[0]->title = safe_slash_html($image_title);
      $current[0]->keywords = safe_slash_html($image_keywords);
      $current[0]->description = safe_slash_html($image_description);
    
    } else {
      
      $item = $xml->addChild('image');
      $item->addChild('path', safe_slash_html($image));
      $item->addChild('title', safe_slash_html($image_title));
      $item->addChild('keywords', safe_slash_html($image_keywords));
      $item->addChild('description', safe_slash_html($image_description));
    }
    
    $result = XMLsave($xml, $xml_path);
    
  } else {
    
    if (isset($current[0])) {
      $image_title = (string) $current[0]->title;
      $image_keywords = (string) $current[0]->keywords;
      $image_description = (string) $current[0]->description;
    }
    
  }
?>
<div class="section">
  <form id="images-fields" method="post">
    <div class="leftsec">
      <p>
        <label for="image-title">Image Title:</label>
        <input type="text" class="text" name="image-title" value="<?php echo $image_title; ?>">
      </p>
      <p>
        <label for="image-keywords">Image Keywords:</label>
        <input type="text" class="text" name="image-keywords" value="<?php echo $image_keywords; ?>">
      </p>
    </div>
    <div class="rightsec">
      <p>
        <label for="image-description">Image Description:</label>
        <textarea class="text" name="image-description" style="height: 82px;"><?php echo $image_description; ?></textarea>
      </p>    
    </div>
    <div class="clear"></div>
    <p id="submit_line">
      <input type="submit" name="submitted" value="<?php i18n('SAVE') ?>" class="submit" />
    </p>
  </form>
</div>
<?php
}