<?php
header("Content-Type:text/html; charset=utf-8");

$fz = 0;
echo "基礎設定"."<br>";

$arini = parse_ini_file("config.ini");

?>

<form id="frmche" action="clean.php" method = "GET" >
<input type="submit" name="submit" value="清除快取">
</form>

<Form name = "config" action = "saveini.php" method = "GET"  >

搜尋總數:<input name="frm_itr" type="text" id="frm_itr" value="<?php echo $arini['total_search']; ?>">
<br>
單次搜尋筆數(請不要超過100):<input name="frm_num" type="text" id="frm_num" value="<?php echo $arini['num_search']; ?>">
<br>
文章關鍵字往前長度:<input name="frm_len_beg" type="text" id="frm_len_beg" value="<?php echo $arini['content_len_beg']; ?>">
<br>
文章關鍵字往後長度:<input name="frm_len_end" type="text" id="frm_len_end" value="<?php echo $arini['content_len_end']; ?>">
<br>
單篇文章關鍵句數限制:<input name="frm_key_num" type="text" id="frm_key_num" value="<?php echo $arini['key_num']; ?>">
<br>
Wordpress 單頁資料數(請不要超過搜尋總數):<input name="frm_wp" type="text" id="frm_wp" value="<?php echo $arini['num_wp']; ?>">
<br>
搜尋引擎:<select name="frm_sg" id="frm_sg">
<option selected value="<?php echo $arini['search_engine']; ?>" ><?php echo $arini['search_engine']; ?></option>
<option value="google" >Google</option>
<option value="bing" >Bing</option>
<option value="yahoo" >Yahoo</option>

</select>
<br>
<input type="submit" name="Submit" value="送出">
<input type="reset" name="Submit" value="重設">
</Form>

<?php

echo "搜尋關鍵字設定"."<br>";
echo "請務必按照預設格式設定(中間空白請填上+)"."<br>";
$s_fp = fopen("search_keywords.txt", "r");
$fz = @filesize("search_keywords.txt");
if($fz != 0 )
	$s_c = fread($s_fp,filesize("search_keywords.txt"));
?>

<Form name = "keyword" action = "savedata.php" method = "POST"  >

<textarea name="frm_key" id="frm_key" rows="10"><?php echo $s_c; ?></textarea>
<br>

<?php

echo "排除網址設定"."<br>";
echo "請務必按照預設格式設定"."<br>";
$eu_fp = fopen("ex_urls.txt", "r");
$fz = @filesize("ex_urls.txt");
if($fz != 0 )
	$eu_c = fread($eu_fp,filesize("ex_urls.txt"));
?>

<textarea name="frm_exurl" id="frm_exurl" cols="50" rows="10"><?php echo $eu_c; ?></textarea>
<br>

<?php

echo "文章關鍵字設定"."<br>";
echo "請務必按照預設格式設定"."<br>";
$a_fp = fopen("article_keywords.txt", "r");
$fz = @filesize("article_keywords.txt");
if($fz != 0 )
	$a_c = fread($a_fp,filesize("article_keywords.txt"));
?>

<textarea name="frm_akey" id="frm_akey" rows="10"><?php echo $a_c; ?></textarea>
<br>

<?php

echo "過濾關鍵字設定"."<br>";
echo "請務必按照預設格式設定"."<br>";
$e_fp = fopen("ex_article_keywords.txt", "r");
$fz = @filesize("ex_article_keywords.txt");
if($fz != 0 )
	$e_c = fread($e_fp,filesize("ex_article_keywords.txt"));
?>

<textarea name="frm_exkey" id="frm_exkey" rows="10"><?php echo $e_c; ?></textarea>
<br>
<input type="submit" name="Submit" value="送出">
<input type="reset" name="Submit" value="重設">
</Form>
