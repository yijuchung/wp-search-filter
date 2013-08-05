<?php
header("Content-Type:text/html; charset=utf-8");
//require("../../../wp-includes/post.php");
//define('WP_USE_THEMES', true);
require_once('../../../wp-admin/admin.php');
$arini = parse_ini_file("config.ini");

$page = $arini['num_wp'];
$conf = $arini['confest'];
$k_i = 0;
$key_ag = array();
$k_fp = fopen("article_keywords.txt", "r");
while (!feof($k_fp))
{
	$key_ag[$k_i] = trim(fgets($k_fp));
	$k_i++;
}

$pub_num = $_POST['pub_num'];
unset($_POST['pub_num']);
$pub_way = $_POST['pub_way'];
unset($_POST['pub_way']);

//echo "<pre>";
//print_r($_POST);
//echo "</pre>";

  if( $pub_way == 2 )
	$way = "publish";
  else
	$way = "draft";

$itr = 1;
$page_itr = 1;

$first = true;
//$content = "<table>";
foreach( $_POST as $key => $val )
{
	$temp = explode("_", $key );
	
	switch( $temp[0] )
	{
		case  "search":
			if($first)
				$first = false;
			else
			{
				if( strlen($content) > strlen($header) )
				{
					//$content .= strlen($content);
					$content .= "</td></tr></tbody></table>";
					$my_post = array(
						'post_title' => "§ ".$title." Resource(".$page_itr.") §",
						'post_content' => $content,
						'post_status' => $way,
					);
  
					$pid = wp_insert_post( $my_post );
				}
			}
			unset($header);
			unset($content);
			unset($title);
			$page_itr = 1;
			$itr = 0;
			$header .= "<table style='width: 100%; border-collapse: collapse; border: 1px solid #000000;'><tbody><tr><td>";
			$header .= $conf;
			$header .= "</td></tr></tbody></table><div style='height: 8px;'></div><table style='width: 100%; border-collapse: collapse; border: 1px solid #000000;'><tbody>";
			$title = $val;
			$content .= $header;
			//echo $temp[0];
			break;
		case  "url":
			
			if( $itr%$page == 0 && $itr >= $page )
			{
				$content .= "</td></tr></tbody></table>";
				$my_post = array(
					'post_title' => "§ ".$title." Resource(".$page_itr.") §",
					'post_content' => $content,
					'post_status' => $way,
				);
  
				$pid = wp_insert_post( $my_post );
				$page_itr++;
				unset($content);
				$content .= $header;
				$content .= "<tr><td style='color: green; text-align: right;' colspan='2'>§ Data [".$itr."]--- §</td></tr>";
				$content .= "<tr><td style='width: 85px; text-align: center;'>From</td><td><a style='color: #0033cc;' href='".$val."' target='_blank'>Go to Original</a></td></tr>";
			}else{
				$content .= "<div style='height: 8px;'>　</div>";
				$content .= "<tr><td style='color: green; text-align: right;' colspan='2'>§ Data [".$itr."]--- §</td></tr>";
				$content .= "<tr><td style='width: 85px; text-align: center;'>From</td><td><a style='color: #0033cc;' href='".$val."' target='_blank'>Go to Original</a></td></tr>";
			}
			$itr++;
			//echo $temp[0];
			break;
		case  "title":
			$content .= "<tr><td style='text-align: center;'>Original Title</td><td>".$val."</td></tr>";
			//echo $temp[0];
			break;
		case  "sum":
			$content .= "<tr><td style='text-align: center;'>Summary</td><td style='color: #808080;'>".$val."</td></tr>";
			$content .= "<td style='text-align: center;' colspan='2'>";
			//echo $temp[0];
			break;
		case  "kn":
			$kn = $val;
			//echo $temp[0];
			break;			
		case  "kp":
			$content .= "[stextbox id='grey' caption='€ =&gt;「".$kn."」' color='0E1155' ccolor='ffffff' bcolor='000000' bgcolor='b1b4b1' cbgcolor='5a5358']".$val."[/stextbox]

";
			//echo $temp[0];
			break;
	}
	
}

if( strlen($content) > strlen($header) )
{
	$content .= "</td></tr></tbody></table>";
	$my_post = array(

	'post_title' => "§ ".$title." Resource(".$page_itr.") §",
	'post_content' => $content,
	'post_status' => $way,
	);
  
	$pid = wp_insert_post( $my_post );
}
  //get_header();
  /*
  $my_post = array(
     'post_title' => $title_all[0],
     'post_content' => $content,
     'post_status' => $way,
     //'post_author' => 1,
     //'post_category' => array(8,39)
  );
  
  $pid = wp_insert_post( $my_post );
  */

?>
<meta http-equiv=REFRESH CONTENT=1;url="../../../wp-admin/post.php?post=<?php echo $pid; ?>&action=edit">