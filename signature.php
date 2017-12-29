<?php 
if(!isset($_GET["player"]) || !isset($_GET["type"]) || !isset($_GET["period"]) || !isset($_GET["game"])) {
	print "ERROR: not enough info. player, type, period and game required.";
	exit;
} else //prepare for image output
	header('Content-Type: image/jpeg');
	
//get info from url
$playerName = $_GET["player"];
$game = $_GET["game"]; //AOM_RC0; AOM_XPACK
$type = $_GET["type"]; //ZS_Supremacy; ZS_Conquest; ZS_Deathmatch; ZS_Lightning; ZS_Custom
$period = $_GET["period"]; //ZS_AllTime; ZS_Weekly; ZS_Monthly
if(isset($_GET["clan"])) {$clan = $_GET["clan"];} //optional

//Get ESO data
$parser = xml_parser_create();
xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1); 
xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0);
$queryeso = "http://72.3.239.130/$game/query/query.aspx?<clr><cmd%20v='query'/><co%20g='$game'%20s='100'%20z='1.0.3'%20t='time()'%20U='5'/><qest%20id='0'%20si='0'%20en='$playerName'%20et='ZS_Human'%20md='$type'%20tp='$period'/></clr>"; 
$data = implode("",file("$queryeso"));
xml_parse_into_struct($parser,$data,&$d_ar,&$i_ar);
xml_parser_free($parser);

//Get the info you need
$wineso = $d_ar['4']['attributes']['ZS_WinningPct'];
$TotalGames = $d_ar['4']['attributes']['ZS_Games'];
if($type != "ZS_Custom") //only for rated games
	$rate = round($d_ar['4']['attributes']['ZS_CombinedELORating']);

//Static assets for now
$image = imagecreatefromjpeg("assets/backgrounds/freak.jpg");
$flag = imagecreatefromjpeg("assets/countries/belgium.jpg");
$loki = imagecreatefromjpeg("assets/gods/lokismall.jpg");

//Add text
$width = imagesx($image);
$height = imagesy($image);
$size = 10;
$angle = 0;
$posx = 10;
$posy = 0;
$font = "./assets/fonts/framd.ttf";
$color = imagecolorallocate($image,153,199,255);

//Add Text
imagettftext($image,$size,$angle,$posx,$posy+12,$color,$font,$playerName);
imagettftext($image,$size,$angle,$posx,$posy+29,$color,$font,"Games Won: ".$wineso."%");
imagettftext($image,$size,$angle,$posx,$posy+46,$color,$font,"Games: ".$TotalGames);
if($type != "ZS_Custom") imagettftext($image,$size,$angle,$posx,$posy+97,$color,$font, $rate);
if(isset($clan)) imagettftext($image,$size,$angle,$posx,$posy+97,$color,$font,"Proud Member Of ".$clan);

//Add Misc
$image = image_overlap($image, $flag, 5, 5); //add flag
$image = image_overlap($image, $loki, 5, 20); //add loki
imagejpeg($image); //output image

function image_overlap($background, $foreground, $offsetx, $offsety){
   $insertWidth = imagesx($foreground);
   $insertHeight = imagesy($foreground);

   $imageWidth = imagesx($background);
   $imageHeight = imagesy($background);

   $overlapX = $imageWidth-$insertWidth-$offsetx;
   $overlapY = $imageHeight-$insertHeight-$offsety;
   imagecolortransparent($foreground,imagecolorat($foreground,0,0));
   imagecopymerge($background,$foreground,$overlapX,$overlapY,0,0,$insertWidth,$insertHeight,100);
   return $background;
}
?>