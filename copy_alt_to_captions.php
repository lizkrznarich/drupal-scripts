<?php 
/**
* PHP script to copy content content from img alt attributes to data-caption attributes for use with D8 Caption filter. Also resizes images that are currently 500px wide, scaling them to 700px wide.
*/
//Replace with your database connection info 
$server = "YOUR DB HOST, typically localhost";
$login = "YOUR DB USERNAME";
$password = "YOUR DB PASSWORD";
$database = "YOUR DB PASSWORD";
$prefix = '';
$really_update = FALSE; # Set this to TRUE to actually run this conversion
 
 
///////////////////////////////////////////////////////////////////////////////////////////////////
 
$link = mysql_connect($server, $login, $password) or die("Connect error : " . mysql_error());
echo "Connect OK\n";
 
$db_selected = mysql_select_db($database, $link);
if (!$db_selected)
    die ('Select DB error : ' . mysql_error());
 
$query = "SELECT * FROM ${prefix}node__body WHERE body_value like '%<img alt=%'";
$result = mysql_query($query);
if (!$result) {
    $message = 'Query error : ' . mysql_error() . "\n";
    $message .= 'Query : ' . $query;
    die($message);
}
 
$count=0;
while ($row = mysql_fetch_assoc($result))   {
    $time = time();
    $tmp = $row['body_value'];
    $count++;
    echo "<br>$count #######################################################################<br>";
    echo "Entity ID: ${row['entity_id']}<br>";
    $pattern = "/<img [^>]+\/>/";
    preg_match_all($pattern, $tmp, $matches, PREG_OFFSET_CAPTURE);
    foreach ($matches as $match) {
        $i = 0;
        foreach ($match as $image_marker)   {
            $alt="";
            $src="";
            $width="";
            $height="";
            $align="";
            $class="";

            $i++;
            list($img, $offset_in_tmp) = $image_marker;
            echo "Current img tag: <br>"
            echo "$img <br>";
            $img_stripped = preg_replace("/^<img (.*)\/>$/", '${1}', $img);
            $img_atts = explode('" ', $img_stripped);
            foreach($img_atts as $img_att){
                //echo "$img_att <br>";
                if(strpos($img_att, 'alt')!== false){
                    list($_, $alt) = explode('="', $img_att, 2);
                }
                if(strpos($img_att, 'src')!== false){
                    list($_, $src) = explode('="', $img_att, 2);
                }
                if(strpos($img_att, 'width')!== false){
                    list($_, $width) = explode('="', $img_att, 2);
                }
                if(strpos($img_att, 'height')!== false){
                    list($_, $height) = explode('="', $img_att, 2);
                }
                if(strpos($img_att, 'align')!== false){
                    list($_, $align) = explode('="', $img_att, 2);
                }
                if(strpos($img_att, 'class')!== false){
                    list($_, $class) = explode('="', $img_att, 2);
                }
            }
            if($width=="500"){
                    $width=intval($width*1.4);
                    $height=intval($height*1.4);
            }
            $image_tag = "<img alt=\"$alt\" data-caption=\"$alt\" src=\"$src\" width=\"$width\" height=\"$height\" align=\"$align\" class=\"$class\" />";
            echo "New img tag: <br>"
            echo "$image_tag <br>";
            echo "<br>##########################################################################<br>";
            $tmp = str_replace($img, $image_tag, $tmp);
        }
    }
    if ($really_update) {
        
        $update_query_1 = "UPDATE ${prefix}node__body SET body_value = '".addslashes($tmp)."' WHERE entity_id = ".$row['entity_id'];
        $res_1 = mysql_query($update_query_1);
        if (!$res_1) {
            $message = 'Query error : ' . mysql_error() . "\n";
            $message .= 'Query : ' . $update_query_1;
            die($message);
        }

        $update_query_2 = "UPDATE ${prefix}node_field_data SET changed = '".$time."' WHERE vid = ".$row['entity_id'];
        $res_2 = mysql_query($update_query_2);
        if (!$res_2) {
            $message = 'Query error : ' . mysql_error() . "\n";
            $message .= 'Query : ' . $update_query_2;
            die($message);
        }

        $update_query_3 = "UPDATE ${prefix}node_revision__body SET body_value = '".addslashes($tmp)."' WHERE entity_id = ".$row['entity_id']." and revision_id=".$row['revision_id'];
        $res_3 = mysql_query($update_query_3);
        if (!$res_3) {
            $message = 'Query error : ' . mysql_error() . "\n";
            $message .= 'Query : ' . $update_query_3;
            die($message);
        }

        $update_query_4 = "UPDATE ${prefix}node_revision SET revision_timestamp = '".$time."' WHERE nid = ".$row['entity_id']." and vid=".$row['revision_id'];
        $res_4 = mysql_query($update_query_4);
        if (!$res_4) {
            $message = 'Query error : ' . mysql_error() . "\n";
            $message .= 'Query : ' . $update_query_4;
            die($message);
        }

        $update_query_5 = "UPDATE ${prefix}node_field_revision SET changed = '".$time."' WHERE nid = ".$row['entity_id']." and vid=".$row['revision_id'];
        $res_5 = mysql_query($update_query_5);
        if (!$res_5) {
            $message = 'Query error : ' . mysql_error() . "\n";
            $message .= 'Query : ' . $update_query_5;
            die($message);
        }
        
    }
    //break; // Test
}   // End : while ($row = mysql_fetch_assoc($result))
mysql_free_result($result);
mysql_close($link);
echo "\nEnd ($count entities modified)\n\n";
?>