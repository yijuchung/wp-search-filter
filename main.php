<?php
header("Content-Type:text/html; charset=utf-8");
set_time_limit(0);
ini_set("memory_limit","2048M");

$arini = parse_ini_file("config.ini");

$domain = $arini['search_engine'];

$key_sr = array();

$s_fp = fopen("search_keywords.txt", "r");

$s_i = 0;
while (!feof($s_fp))
{
	$key_sr[$s_i] = trim(fgets($s_fp));
	$s_i++;
}

//print_r($key_sr);
$t = 0;

$ch = curl_init();
//ob_start();
while( $t < $s_i )
{
	//echo $key_sr[$t];
	curl_setopt($ch,CURLOPT_URL,"http://140.109.22.252/wp/wp-content/plugins/zoo-search-filter/search?q=".urlencode($key_sr[$t]));
	echo "<pre>";
	$content = curl_exec($ch);
	echo "</pre>";
	//echo $content;
	//$content = ob_get_contents();
	//ob_clean();
	//curl_close($ch);
	$t++;
	echo "<br>";
	unset($content);
	//echo $content;
}

echo "搜尋完成，1秒後回前頁。";

?>
<meta http-equiv="refresh" content="1; URL=javascript:history.back();">