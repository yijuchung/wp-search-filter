<?php
/*
Plugin Name: zoo-search-filter
Plugin URI: http://.../
Description: A simple search filter plugin for wordpress
Version: 1.0
Author: Yi-Ju, Chung (zoochung)
Author URI: http://..../
License: GPL
*/
set_time_limit(0);
ini_set("memory_limit","2048M");

register_activation_hook(__FILE__,'zoo_search_install'); 
register_deactivation_hook( __FILE__, 'zoo_search_remove' );

function zoo_search_install() {
add_option("zoo_search_data", 'Default', '', 'yes');
}

function zoo_search_remove() {
delete_option('zoo_search_data');
}

function objectToArray( $object )
{
    if( !is_object( $object ) && !is_array( $object ) )
    {
        return $object;
    }
    if( is_object( $object ) )
    {
         $object = get_object_vars( $object );
    }
    return array_map( 'objectToArray', $object );
}

if ( is_admin() ){

	add_action('admin_menu', 'zoo_admin_menu');

	function zoo_admin_menu() {
		add_menu_page('Zoo admin', 'Zoo admin', 'administrator','zoo-search-filter', 'zoo_html_page');
		add_submenu_page( 'zoo-search-filter', 'Config', "Config", 'administrator', 'zoo-search-config','zoo_config_page' );
		add_submenu_page( 'zoo-search-filter', 'Search', "Search", 'administrator', 'zoo-search','zoo_main_page' );
		add_submenu_page( 'zoo-search-filter', 'Deeper', "Deeper", 'administrator', 'zoo-deeper','zoo_deeper_page' );
	}
}

function zoo_html_page() {
	
	$data = array();
	
	$basedir = "../wp-content/plugins/zoo-search-filter/article/";
	$dir = opendir($basedir);
	$i = 0;
	while (false !== ($file = readdir($dir)))
	{
		if($file=="." || $file=="..")
			continue;
		$fp = fopen($basedir.$file,"r");
		$content = fread($fp,filesize($basedir.$file));
		fclose($fp);
		$ta = objectToArray(json_decode($content));
		
		$data[$ta['key']][$i] = $ta;
		$i++;
	}
	
?>
<div>
<h2>Configuration</h2>

<form name="data" method="post" action="../wp-content/plugins/zoo-search-filter/post.php">
<?php

$aa = 0;
$kt = 0;
foreach($data as $key => $var)
{
?>
	<b>Title</b>：<input name="search_title_<?php echo $kt; ?>" type="text" id="search_title_<?php echo $kt; ?>" value="<?php echo trim($key); ?>">
	<br>
<?php
	$ent_i = 1;
	foreach($var as $ent)
	{
		echo "<b>Resource [".$ent_i."] </b>";
		echo "<input type=checkbox name=\"alo_".$kt."_".$ent_i."\" onclick=\"allow(this,'s".$aa."','data');\">(admit)";
		echo "<input type=checkbox name=\"pub_".$kt."_".$ent_i."\" onclick=\"show(this,'s".$aa."','data');\">(show)";
		$cc = htmlspecialchars($ent['content']);
		$order   = array("\r\n", "\n", "\r", " ", "\"");
		$cc = str_replace($order, "", $cc);
		echo "<input type=\"button\" value=\"Original Html\" onclick=\"showori('".$cc."')\">";
		echo "<br>";
?>
		<b>Web Url</b>：<?php echo $ent['url']; ?>
		<br>
		<div id="s<?php echo $aa; ?>" style="display:none">
		<b>Web Url</b>：<br><textarea name="url_<?php echo $kt; ?>_<?php echo $ent_i; ?>" id="s<?php echo $aa; ?>" cols="100"  disabled="disabled"><?php echo $ent['url']; ?></textarea>
		<input type=checkbox name="alo_url_<?php echo $kt; ?>_<?php echo $ent_i; ?>" onclick="allow_kp(this,'url_<?php echo $kt; ?>_<?php echo $ent_i; ?>');" id="s<?php echo $aa; ?>">(admit)<br>
		<b>Web Title</b>：<br><textarea name="title_<?php echo $kt; ?>_<?php echo $ent_i; ?>" id="s<?php echo $aa; ?>" cols="100"  disabled="disabled"><?php echo $ent['title']; ?></textarea>
		<input type=checkbox name="alo_title_<?php echo $kt; ?>_<?php echo $ent_i; ?>" onclick="allow_kp(this,'title_<?php echo $kt; ?>_<?php echo $ent_i; ?>');" id="s<?php echo $aa; ?>">(admit)<br>
		<b>Search Summary</b>：<br><textarea name="sum_<?php echo $kt; ?>_<?php echo $ent_i; ?>" id="s<?php echo $aa; ?>" cols="100"  disabled="disabled"><?php echo $ent['summary']; ?></textarea>
		<input type=checkbox name="alo_sum_<?php echo $kt; ?>_<?php echo $ent_i; ?>" onclick="allow_kp(this,'sum_<?php echo $kt; ?>_<?php echo $ent_i; ?>');" id="s<?php echo $aa; ?>">(admit)<br>
<?php
		$ky = 0;
		foreach($ent['key_parse'] as $kp_key => $kp_cont)
		{
			if($kp_cont == null)
				continue;
			$tt = 0;
?>
			<b>Keywords(<?php echo $kp_key; ?>)</b>：<br>
			<input type="hidden" name="kn_<?php echo $kt; ?>_<?php echo $ent_i; ?>_<?php echo $ky; ?>" value="<?php echo $kp_key; ?>">
<?php
			foreach($kp_cont as $row)
			{
?>
				<textarea name="kp_<?php echo $kt; ?>_<?php echo $ent_i; ?>_<?php echo $ky; ?>_<?php echo $tt; ?>" id="s<?php echo $aa; ?>" cols="100"  disabled="disabled"><?php echo $row; ?></textarea>
<?php		
				echo "<input type=checkbox name=\"alo_kp_".$kt."_".$ent_i."_".$ky."_".$tt."\" onclick=\"allow_kp(this,'kp_".$kt."_".$ent_i."_".$ky."_".$tt."');\" id=\"s".$aa."\">(admit)";
				echo "<br>";
				$tt++;
			}
			$ky++;
		}
?>
	</div>
<?
		$aa++;
		$ent_i++;
	}
	$kt++;
}
?>
<br>
<input type="hidden" name="pub_num" value="0">
<input type="hidden" name="pub_way" value="0">
<input type='submit' value="w3c" style="display:none;"> 
<input type="button" name="draft" value="Draft" onclick='countf("data",this,1);'>
<input type="button" name="publish" value="Publish" onclick='countf("data",this,2);'>
</form>
</div>
<script type="text/javascript">
<!--
	function show(obj,id,f){
		document.getElementById(id).style.display = obj.checked?"block":"none";	
	}
	
	function allow(obj,id,f){
		var objForm = document.forms[f];
		var objLen = objForm.length;
			
		for (var iCount = 0; iCount < objLen; iCount++)
		{
			if (objForm.elements[iCount].id == id && objForm.elements[iCount].type == "checkbox")
				objForm.elements[iCount].checked = obj.checked;		
			if (objForm.elements[iCount].id == id)
				objForm.elements[iCount].disabled = !obj.checked;
		}
	}
	
	function allow_kp(obj,name){
		document.getElementsByName(name)[0].disabled = !obj.checked;
	}	
	
	function showori(obj){
		myWindow=window.open('','','width=300,height=300')
		myWindow.document.write(obj)
		myWindow.focus()
	}
	
function countf(d,f,k)
{
    var objForm = document.forms[d];
    var objLen = objForm.length;
	var chknum = 0;
    for (var iCount = 0; iCount < objLen; iCount++)
    {
        if (objForm.elements[iCount].type == "checkbox" && objForm.elements[iCount].checked)
			chknum++;
		else if( objForm.elements[iCount].name == "pub_num" )
		{
			var pub = iCount;
		}
		else if( objForm.elements[iCount].name == "pub_way" )
		{
			var method = iCount;
		}
    }
	
	objForm.elements(pub).value = chknum;
	objForm.elements(method).value = k;
	objForm.submit();
}
//-->
</script>
<?php
}

function zoo_config_page() {
$fz = 0;
echo "基礎設定"."<br>";

$arini = parse_ini_file("../wp-content/plugins/zoo-search-filter/config.ini");

?>

<form id="frmche" action="../wp-content/plugins/zoo-search-filter/clean.php" method = "GET" >
<input type="submit" name="submit" value="Clear Cache">
</form>

<Form name = "config" action = "../wp-content/plugins/zoo-search-filter/saveini.php" method = "GET"  >

Total Search:<input name="frm_itr" type="text" id="frm_itr" value="<?php echo $arini['total_search']; ?>">
<br>
Data per search(do no over 100):<input name="frm_num" type="text" id="frm_num" value="<?php echo $arini['num_search']; ?>">
<br>
Words forward count:<input name="frm_len_beg" type="text" id="frm_len_beg" value="<?php echo $arini['content_len_beg']; ?>">
<br>
Words backward count:<input name="frm_len_end" type="text" id="frm_len_end" value="<?php echo $arini['content_len_end']; ?>">
<br>
The limitation of keywords per single page:<input name="frm_key_num" type="text" id="frm_key_num" value="<?php echo $arini['key_num']; ?>">
<br>
Demontation:<br><textarea name="frm_confest" id="frm_confest"  cols="50" rows="3"><?php echo $arini['confest']; ?></textarea>
<br>
Wordpress resources per page(do not over Total search):<input name="frm_wp" type="text" id="frm_wp" value="<?php echo $arini['num_wp']; ?>">
<br>
Search engine:<select name="frm_sg" id="frm_sg">
<option selected value="<?php echo $arini['search_engine']; ?>" ><?php echo $arini['search_engine']; ?></option>
<option value="google" >Google</option>
<option value="bing" >Bing</option>
<option value="yahoo" >Yahoo</option>

</select>
<br>
<input type="submit" name="Submit" value="Submit">
<input type="reset" name="Submit" value="Reset">
</Form>

<?php

echo "Search Keywords"."<br>";
$s_fp = fopen("../wp-content/plugins/zoo-search-filter/search_keywords.txt", "r");
$fz = @filesize("../wp-content/plugins/zoo-search-filter/search_keywords.txt");
if($fz != 0 )
	$s_c = fread($s_fp,filesize("../wp-content/plugins/zoo-search-filter/search_keywords.txt"));
?>

<Form name = "keyword" action = "../wp-content/plugins/zoo-search-filter/savedata.php" method = "POST"  >

<textarea name="frm_key" id="frm_key" rows="10"><?php echo $s_c; ?></textarea>
<br>

<?php

echo "Excluded Url"."<br>";
$eu_fp = fopen("../wp-content/plugins/zoo-search-filter/ex_urls.txt", "r");
$fz = @filesize("../wp-content/plugins/zoo-search-filter/ex_urls.txt");
if($fz != 0 )
	$eu_c = fread($eu_fp,filesize("../wp-content/plugins/zoo-search-filter/ex_urls.txt"));
?>

<textarea name="frm_exurl" id="frm_exurl" cols="50" rows="10"><?php echo $eu_c; ?></textarea>
<br>

<?php

echo "Arguments Keywords"."<br>";
$a_fp = fopen("../wp-content/plugins/zoo-search-filter/article_keywords.txt", "r");
$fz = @filesize("../wp-content/plugins/zoo-search-filter/article_keywords.txt");
if($fz != 0 )
	$a_c = fread($a_fp,filesize("../wp-content/plugins/zoo-search-filter/article_keywords.txt"));
?>

<textarea name="frm_akey" id="frm_akey" rows="10"><?php echo $a_c; ?></textarea>
<br>

<?php

echo "Filter Keywords"."<br>";
$e_fp = fopen("../wp-content/plugins/zoo-search-filter/ex_article_keywords.txt", "r");
$fz = @filesize("../wp-content/plugins/zoo-search-filter/ex_article_keywords.txt");
if($fz != 0 )
	$e_c = fread($e_fp,filesize("../wp-content/plugins/zoo-search-filter/ex_article_keywords.txt"));
?>

<textarea name="frm_exkey" id="frm_exkey" rows="10"><?php echo $e_c; ?></textarea>
<br>
<input type="submit" name="Submit" value="Submit">
<input type="reset" name="Submit" value="Reset">
</Form>

<?php
}

function zoo_main_page() {

$arini = parse_ini_file("../wp-content/plugins/zoo-search-filter/config.ini");

$domain = $arini['search_engine'];

$key_sr = array();

$s_fp = fopen("../wp-content/plugins/zoo-search-filter/search_keywords.txt", "r");

$s_i = 0;
while (!feof($s_fp))
{
	$key_sr[$s_i] = trim(fgets($s_fp));
	$s_i++;
}

$t = 0;
echo "Start Searching !!"."<br>";
$ch = curl_init();
while( $t < $s_i )
{
	curl_setopt($ch,CURLOPT_URL,"http://140.109.22.252/wp/wp-content/plugins/zoo-search-filter/search?q=".urlencode($key_sr[$t]));
	echo "<pre>";
	$content = curl_exec($ch);
	echo "</pre>";
	$t++;
	echo "<br>";
	unset($content);
}

echo "Searching Completed ~";

}

function zoo_deeper_page() {

include("../wp-content/plugins/zoo-search-filter/simple_html_dom.php");

$arini = parse_ini_file("../wp-content/plugins/zoo-search-filter/config.ini");

echo "Deeper searching !!"."<br>";
echo "Loading config......";
$max_string_beg = $arini['content_len_beg'];
$max_string_end = $arini['content_len_end'];
$max_key = $arini['key_num'];
$max_string = $max_string_beg+$max_string_end;
$domain = $arini['search_engine'];
mb_internal_encoding("UTF-8");

$k_i = 0;
$key_ag = array();
$k_fp = fopen("../wp-content/plugins/zoo-search-filter/article_keywords.txt", "r");
while (!feof($k_fp))
{
	$key_ag[$k_i] = trim(fgets($k_fp));
	$k_i++;
}

function strtrim($string,$ag,$max_string_beg,$max_string,$max_key)
{
	$static_string = $string;
	foreach($ag as $var)
	{
		$string = $static_string;
		$ll = mb_strlen($var);

		do
		{
			$temp_pos = mb_strpos($string,$var);
			if($temp_pos == 0)
				break;
			$temp_string = mb_substr($string,$temp_pos-$max_string_beg,$max_string+$ll);			
			$string = mb_substr($string,$temp_pos+$ll+$max_string_end-1);	
			$order   = array("\r\n", "\n", "\r", " ");
			$temp_string = str_replace($order, "", $temp_string);
			$temp_string = trim($temp_string);
			
			if($temp_string != null && mb_strlen($temp_string) > $ll )
			{
				$temp[] = $temp_string;
			}
			unset($temp_string);
			if( count($temp) >= $max_key )
				break;
		}
		while(true);
		$temp2[$var] = $temp;
		unset($temp);
		
	}
	return $temp2;
}

$key_ea = array();
$e_fp = fopen("../wp-content/plugins/zoo-search-filter/ex_article_keywords.txt", "r");

$e_i = 0;
while (!feof($e_fp))
{
	$key_ea[$e_i] = trim(fgets($e_fp));
	$e_i++;
}

function filter($string,$ea)
{
	foreach($ea as $var)
	{
		$string = mb_ereg_replace($var,"***",$string);
	}
	return $string;
}

echo "finished !!"."<br>";
echo "Loading Cache...........";
$data = array();
$dir = opendir("../wp-content/plugins/zoo-search-filter/cache/".$domain."/");
$i = 0;
while (false !== ($file = readdir($dir)))
{
    if($file=="." || $file=="..")
		continue;
	$fp = fopen("../wp-content/plugins/zoo-search-filter/cache/".$domain."/".$file,"r");
	$data[$i]['key'] = fgets($fp);
	$data[$i]['result'] = objectToArray(json_decode(fread($fp,filesize("../wp-content/plugins/zoo-search-filter/cache/".$domain."/".$file))));
	fclose($fp);
	$i++;
}

$max_file = $i;
echo "finished !!"."<br>";
echo "Searching..............";
for($j = 0;$j<$max_file;$j++)
{
	if(!isset($data[$j]['result']))
	{
		continue;
	}
	foreach($data[$j]['result'] as $var)
	{
		echo "<br>URL:".$var['url'];
		
		$filename = "../wp-content/plugins/zoo-search-filter/article/".MD5($var['url'])."_".$domain.".txt";
		if(file_exists($filename))
		{
			echo "<br>Cache existed !!";
			continue;
		}
			
		$html = file_get_html($var['url']);
		$encode = mb_detect_encoding($html->plaintext,array("UTF-8","BIG5","GBK","GB2312"));
		if( $encode != "UTF-8" )
			$var['content'] = iconv($encode,"UTF-8//IGNORE",$html->plaintext);
		else
			$var['content'] = $html->plaintext;
			
		$var['content'] = filter($var['content'],$key_ea);
		$var['key_parse'] = array();
		$var['key_parse'] = strtrim($var['content'],$key_ag,$max_string_beg,$max_string,$max_key);
		
		$var['key'] = $data[$j]['key'];
		
		
		$fp = fopen($filename,"w");
		fwrite($fp,json_encode($var));
		fclose($fp);
		$html->clear();
	}
}
echo "<br>Ok !! Go to Zoo Admin to post。";
}
?>