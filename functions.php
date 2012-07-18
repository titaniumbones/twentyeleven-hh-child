<?php 
/* folloiwing guides here:
http://lisles.net/header-image/
http://op111.net/53
http://codex.wordpress.org/Child_Themes
*/

// change the horribly huge header height
add_filter('twentyten_header_image_height','my_header_height');
add_filter('twentyten_header_image_width','my_header_width');
function my_header_height($size){
  return 150;
}
function my_header_width($size){
  return 940;
}


/// / adding the possibility to get a network instead
function delicious_network($username = '', $num = 5, $list = true, $update = true, $tags = false, $filtertag = '', $displaydesc = false, $nodisplaytag = '', $globaltag = false, $encode_utf8 = false ) {
	
	global $delicious_options;
	include_once(ABSPATH . WPINC . '/rss.php');
	
	$url_extras = "network/".$username;
	if($filtertag != '') { $url_extras .= '/'.$filtertag; }
	//$rss = $delicious_options['rss_url'].'network/'.$username;
	$rss = $delicious_options['rss_url'].$url_extras;
	$html = "http://www.delicious.com/".$url_extras;

	$bookmarks = fetch_rss($rss);
	
	echo "<h3> <a href=".$html."> Related Bookmarks on Delicious </a> </h3>";

	if ($list) echo '<ul class="delicious">';
	
	if ($username == '') {
		if ($list) echo '<li>';
		echo 'Username not configured';
		if ($list) echo '</li>';
	} else {
		if ( empty($bookmarks->items) ) {
			if ($list) echo '<li>';
			echo 'No bookmarks avaliable.';
			if ($list) echo '</li>';
		} else {
			foreach ( $bookmarks->items as $bookmark ) {
				$msg = $bookmark['title'];
				if($encode_utf8) utf8_encode($msg);					
				$link = $bookmark['link'];
				$desc = $bookmark['description'];
			
				if ($list) echo '<li class="delicious-item">'; elseif ($num != 1) echo '<p class="delicious">';
        		echo '<a href="'.$link.'" class="delicious-link">'.$msg.'</a>'; // Puts a link to the... link.

        if($update) {				
          $time = strtotime($bookmark['pubdate']);
          
          if ( ( abs( time() - $time) ) < 86400 )
            $h_time = sprintf( __('%s ago'), human_time_diff( $time ) );
          else
            $h_time = date(__('Y/m/d'), $time);

          echo sprintf( '%s',' <span class="delicious-timestamp"><abbr title="' . date(__('Y/m/d H:i:s'), $time) . '">' . $h_time . '</abbr></span>' );
         }      
				
				if ($displaydesc && $desc != '') {
        			echo '<br />';
        			echo '<span class="delicious-desc">'.$desc.'</span>';
				}
				
				if ($tags) {
					echo '<br />';
					echo '<div class="delicious-tags">';
					$tagged = explode(' ', $bookmark['dc']['subject']);
					$ndtags = explode('+', $nodisplaytag);
					if ($globaltag) { $gttemp = 'tag'; } else { $gttemp = $username; }
					foreach ($tagged as $tag) {
					  if (!in_array($tag,$ndtags)) {
       			  echo '<a href="http://del.icio.us/'.$gttemp.'/'.$tag.'" class="delicious-link-tag">'.$tag.'</a> '; // Puts a link to the tag.              
            }
					}
					echo '</div>';
				}
					
				if ($list) echo '</li>'; elseif ($num != 1) echo '</p>';
			
				$i++;
				if ( $i >= $num ) break;
			}
		}	
  }
	if ($list) echo '</ul>';  
}


// //added by matt sept 10 2010 
function delicious_network_with_page_tags () {
  /* This function simply lets you put delicious results in a page but 
   filtered by the page's tags */
    // define a string that conforms to the plugins search syntax
  // only works for me right now...  
  $tags = get_the_tags();   
  $tag_string = "hackinghistory";
  foreach ($tags as $tag){
    $tag_string .= "+{$tag->name}";
  }
  // $tag_string = substr($tag_str,1);
  //  echo "<p> ".$tag_string."</p";
  delicious_network('hackinghistory', 20, true, false, true, $tag_string, false);
  // delicious_network('hackinghistory', 20, true, false, true, 'hackinghistory+hh', false); 

}
?>