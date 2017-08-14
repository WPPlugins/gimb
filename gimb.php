<?php
/*
Plugin Name: GIMB Google Images Media Button
Plugin URI: http://aguimaraes.org/gimb
Description: Add a media button to search an image with Google Search API
Version: 0.01
Author: Álvaro Guimarães
Author URI: http://aguimaraes.org/
*/

define('GIMB_VERSION', '001');

function gimb_add_media_upload_action() {
  
?>
<script src="http://www.google.com/jsapi?key=<?php echo get_option('gimb_google_key_' . GIMB_VERSION); ?>" type="text/javascript"></script>
    <script language="Javascript" type="text/javascript">
    //<![CDATA[
    
    // When click in 'include'
    function includeHandler(result) {
      var win = window.dialogArguments || opener || parent || top;
      win.send_to_editor('<img src="' + result.url + '" alt="" />');
    }

    google.load("search", "1");

    function OnLoad() {
    
      // Create a search control
      var searchControl = new google.search.SearchControl();
      
      // Create an Image Search Object
      var imageSearch = new google.search.ImageSearch();
      
      // Create an Options Object that will be given as argument
      // when adding a searcher to the searchControl
      var options = new google.search.SearcherOptions();
      
      // All results
      options.setExpandMode(google.search.SearchControl.EXPAND_MODE_OPEN);
      
      // Results in the searchresults div
      options.setRoot(document.getElementById("searchresults"));
      
      // safesearch on
      imageSearch.setRestriction(google.search.Search.RESTRICT_SAFESEARCH, google.search.Search.<?php echo get_option('gimb_restrict_' . GIMB_VERSION); ?>);
      
      searchControl.setResultSetSize(google.search.Search.LARGE_RESULTSET);
      
      searchControl.setOnKeepCallback(this, includeHandler, google.search.SearchControl.KEEP_LABEL_INCLUDE);
      
      //add the image searcher
      searchControl.addSearcher(imageSearch, options);
      
      // Tell the searcher to draw itself and tell it where to attach
      searchControl.draw(document.getElementById("searchcontrol"), options);
    }
    
    google.setOnLoadCallback(OnLoad);

    //]]>
    </script>
  <div style="width: 100%; float: left;" id="searchcontrol"></div>
  <div style="width: 100%; float: left;" id="searchresults"></div>
<?php
}

// Adiciona novo media button
function gimb_add_media_button($context) {
  
    $icon_url = WP_PLUGIN_URL . '/' . str_replace(basename( __FILE__),"",plugin_basename(__FILE__)) . 'googleicon.png';
    
    $result = '<a href="media-upload.php?type=gimb_image&amp;TB_iframe=1" class="thickbox" title="' . __('Add image from google') . '"><img src="'.$icon_url.'" alt="'. __('Add image from google') .'" title="'. __('Add image from google') .'" /></a>';
    
    if (FALSE == get_option('gimb_google_key_' . GIMB_VERSION) || FALSE == get_option('gimb_restrict_' . GIMB_VERSION)) {
      $result = ' <img src="' . str_replace('.png', 'bw.png', $icon_url) . '" alt="' . __('You need to configure GIMB first') . '" title="' . __('You need to configure GIMB first') . '" />';
    }
    
    
    return $context . $result;
}

function gimb_options_page() {
  ?>
  <div class="wrap">
    <h2>GIMB Options</h2>
    <form method="post" action="options.php">
      <?php wp_nonce_field('update-options'); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">Google API Key</th>
          <td><input type="text" name="gimb_google_key_<?php echo GIMB_VERSION; ?>" value="<?php echo get_option('gimb_google_key_' . GIMB_VERSION); ?>" size="60"/></td>
        </tr>
        <tr valign="top">
          <th scope="row">You can get it here:</th>
          <td><a href="code.google.com/apis/ajaxsearch/signup.html">http://code.google.com/apis/ajaxsearch/signup.html</a></td>
        </tr>
        <tr valign="top">
          <th scope="row">Safe Search:</th>
          <td>
          <select name="gimb_restrict_<?php echo GIMB_VERSION; ?>">
            <option value="SAFESEARCH_OFF" <?php if (get_option('gimb_restrict_' . GIMB_VERSION) == 'SAFESEARCH_OFF') echo 'selected="selected"'; ?>>Off</option>
            <option value="SAFESEARCH_MODERATE" <?php if (get_option('gimb_restrict_' . GIMB_VERSION) == 'SAFESEARCH_MODERATE') echo 'selected="selected"'; ?>>Moderate</option>
            <option value="SAFESEARCH_STRICT" <?php if (get_option('gimb_restrict_' . GIMB_VERSION) == 'SAFESEARCH_STRICT') echo 'selected="selected"'; ?>>Strict</option>
          </select>
        </tr>
      </table>
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="gimb_google_key_<?php echo GIMB_VERSION; ?>, gimb_restrict_<?php echo GIMB_VERSION; ?>" />
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>
    </form>
  </div>
  <?php
}

function gimb_menu_option() {
  add_options_page('GIMB Options', 'GIMB Options', 'manage_options', 'gimb', 'gimb_options_page');
}

add_action('admin_menu', 'gimb_menu_option');
add_filter('media_buttons_context', 'gimb_add_media_button');
add_filter('media_upload_gimb_image', 'gimb_add_media_upload_action');
?>
