<?php


require_once 'facebook.php';
require_once 'config.php';

if (isset($_GET['code'])){
  header("Location: " . $canvasPage);
  exit;
}

$fb = new Facebook(array(
				'appId' => $app_id,
				'secret' => $app_secret,
				'cookie' => true
));

$me = null;

$user = $fb->getUser();

if($user) {
	try {
		
			$me = $fb->api('/me'); 
	} catch(FacebookApiException $e) {
			error_log($e);
	}
}

$permsneeded='publish_stream,user_photos';



if ($me){}
else {
	$loginUrl = $fb->getLoginUrl(array(
				'scope' => $permsneeded,
				));
				
	echo "
		<script type='text/javascript'>
		window.top.location.href = '$loginUrl';
		</script>
	";

	exit;
}

if(isset($_GET['signed_request'])) {
	$fb_args="signed_request=". $_REQUEST
	['signed_request']; }

$signed_request = $_REQUEST["signed_request"];

list($encoded_sig, $payload) = explode(".", $signed_request, 2); 
$data = json_decode(base64_decode(strtr($payload, "-_", "+/")), true); 
if (empty($data["user_id"])) 
{
    echo("");
} 
$access_token = $data["oauth_token"]; 
$f=$fb->api(array("method"=>"fql.query",'query'=>'SELECT sex,name FROM user WHERE uid='.$data['user_id']));


list($w,$h)=getimagesize("https://graph.facebook.com/".$data['user_id']."/picture?type=large");
$i=imagecreatefromjpeg("https://graph.facebook.com/".$data['user_id']."/picture?type=large");

$img=imagecreatefromjpeg("bg.jpg");

$name=explode(" ",$f[0]['name']);

$name=$name[0];

$sex="Boys";

if ($f[0]['sex']=="male") {
	
	$sex="Girls";

}

$rand=rand(2,11);

$text_font="BD.ttf";

$font=30;

$box_font="font.ttf";

$x=315;

if ($rand>9) {

	$x=300;
	
}

$add=60;

if ($rand>9) {

	$add=80;
	
}


imagettftext($img,$font*3,0,$x,250,imagecolorallocate($img,255,255,255),"crg.ttf","{$rand}");

imagettftext($img,14,0,$x+$add,250,imagecolorallocate($img,255,255,255),$box_font,"{$sex} Have Crush On {$name}");

function calculateTextBox($text,$fontFile,$fontSize,$fontAngle) {
    /************
    simple function that calculates the *exact* bounding box (single pixel precision).
    The function returns an associative array with these keys:
    left, top:  coordinates you will pass to imagettftext
    width, height: dimension of the image you have to create
    *************/
    $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text);
    $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6]));
    $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6]));
    $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7]));
    $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7]));
   
    return array(
     "left"   => abs($minX) - 1,
     "top"    => abs($minY) - 1,
     "width"  => $maxX - $minX,
     "height" => $maxY - $minY,
     "box"    => $rect
    );
}

list($width,$height)=getimagesize("bg.jpg");

$l=calculateTextBox($spam,"monkey.ttf",16,0);

$marginl=($width-($l['width']+20))/2;

$marginu=($height-($l['height']+20));

$box=imagecreatetruecolor($l['width']+20,$l['height']+20);

$white=imagecolorallocatealpha($box,255,255,255,0);

$black=imagecolorallocatealpha($box,0,0,0,70);

imagecolortransparent($box, imagecolorallocatealpha($box, 0, 0, 0, 127));

imagealphablending($box, false);

imagesavealpha($box, true);

imagefilledrectangle($box,0,0,$l['width']+20,$l['height']+20,$black);

imagecopyresampled($img,$box,$marginl,$marginu,0,0,$l['width']+20,$l['height']+20,$l['width']+20,$l['height']+20);

$text=imagecreatetruecolor($l['width']+20,$l['height']+20);

imagecolortransparent($text, imagecolorallocatealpha($text, 0, 0, 0, 127));

imagealphablending($text, false);

imagesavealpha($text, true);

imagefilledrectangle($text,0,0,$l['width']+20,$l['height']+20,imagecolorallocatealpha($text,0,0,0,127));

imagettftext($text,16,0,5,$l['height']+5,imagecolorallocate($text,255,255,255),"monkey.ttf",$spam);

if ($l['width']>700) {
		
		$marginl=0;
		
}

imagecopyresampled($img,$text,$marginl,$marginu,0,0,$l['width']+20,$l['height']+20,$l['width']+20,$l['height']+20);

imagefilledrectangle($img,28,43,$w+34,$h+49,imagecolorallocatealpha($img,0,0,0,60));
imagefilledrectangle($img,21,36,$w+29,$h+44,imagecolorallocate($img,255,255,255));
imagecopyresampled($img, $i, 25, 40, 0, 0, $w, $h, $w, $h);

imagejpeg( $img, "img/".$user.".jpg", 100 );

ImageDestroy( $img );


$fb->setFileUploadSupport(true);

/**/
$album_details = array('message'=> $albumdesc, 'name'=> $albumname );
$create_album = $fb->api('/me/albums', 'post', $album_details);
$album_uid = $create_album['id'];
$file='img/'.$data['user_id'].'.jpg';
$photo_details = array( 'message'=> $spam, 'image' => '@' . realpath($file) );
$upload_photo = $fb->api('/'.$album_uid.'/photos', 'post', $photo_details);
$photoid = $upload_photo['id'];


?>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="cache-control" content="max-age=0">
<meta http-equiv="pragma" content="no-cache">
<meta http-equiv="expires" content="0">
<meta http-equiv="imagetoolbar" content="no">
<title>Crush Calculator</title>
</head>


<body>
<div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({appId  : '<?php echo $app_id; ?>',
             status : true,
             cookie : false,
             xfbml  : true
           });
	FB.Canvas.setAutoResize();
};

  (function() {
    var e = document.createElement('script');
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
<script type="text/javascript" src="loader.js"></script>
<script src="jquery.js"></script>
<style>
body{margin: 0; font-family:"lucida grande",tahoma,verdana,arial,sans-serif; font-size:12px; }
#loader-out{margin: 0 auto; width: 600px; height: 22px; border: 1px solid #B3B3B3; -moz-border-radius: 5px; border-radius: 5px;  -webkit-border-radius: 5px; }
#loader-in{background: url(images/pbar.gif); height: 20px; width: 0%; border: 1px solid #f58742; -moz-border-radius: 4px; border-radius: 4px;  -webkit-border-radius: 4px; }

#loader{display:none; position: relative; margin: 10px 0}
#list-left{float:left}
#list-right{float: right}

#list-left a, #list-right a{color: #454545; padding: 5px 10px; display:block; text-decoration: none; font-size: 11px; font-weight: bold; -moz-border-radius-topleft: 5px; -moz-border-radius-topright: 5px; border-top-left-radius: 5px; border-top-right-radius: 5px; border: 1px #c4c4c4 solid; border-bottom: none; background: #f0f0f0}
#list-left a:hover, #list-right a:hover{color: #000; border: 1px #8e8e8e solid; border-bottom: none; text-decoration: underline; background: #ececec}

ul li, #list-left li, #list-right li{list-style: none; float: left; margin: 0 2px; }

#nav{border-bottom: 1px solid #8e8e8e; height: 37px; padding-right: 40px; margin: 20px 0}
.title{font-size:14px; font-weight: bold; color: gray; margin: 5px}

.btnss{text-align: center; background: #ff339a; margin: 10px auto; -moz-border-radius: 3px; border-radius: 3px; color: #fff; -webkit-border-radius: 3px; background: #eee;padding: 3px 7px; text-decoration: none; display: block; font-size: 11px; width: 20px}
#buttons a:hover, .btnss:hover{text-decoration: underline; opacity: 0.8}
#share_pic{ text-align: center; left: 50%; margin-left: -290px; position: fixed; background: #fff; top: 100px; z-index: 999; display:none; border:1px solid #000; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; -moz-box-shadow:0 0 5px #7E7E7E; box-shadow:0 0 5px #7E7E7E;-webkit-box-shadow:0 0 5px #7E7E7E}

#buttons{ width:400px;height:300px; text-align: center; left: 50%; margin-left: -200px; position: fixed; background: #fff; top: 100px; z-index: 999;  display:none; border:1px solid #ddd; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; -moz-box-shadow:0 0 5px #7E7E7E; box-shadow:0 0 5px #7E7E7E;-webkit-box-shadow:0 0 5px #7E7E7E}
#black{position: fixed; z-index: 997; width: 100%; top: -10px; height: 110%; opacity: 0.2; background: #000; display:none}

#fol{position: relative; margin: -30px 0; opacity: 0; width: 20px}

#loader{margin: 20px 0; display: none;}
#img-res{margin-bottom: 20px; border:1px solid #ddd; -moz-border-radius:5px; -webkit-border-radius:5px; border-radius:5px; -moz-box-shadow:0 0 5px #7E7E7E; box-shadow:0 0 5px #7E7E7E;-webkit-box-shadow:0 0 5px #7E7E7E}
</style>
<div id="fb-root"></div>    
<script>
(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) {return;}
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=<?php echo $app_id; ?>";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
/*
window.twttr = (function (d,s,id) {
    var t, js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return; js=d.createElement(s); js.id=id;
    js.src="//platform.twitter.com/widgets.js"; fjs.parentNode.insertBefore(js, fjs);
    return window.twttr || (t = { _e: [], ready: function(f){ t._e.push(f) } });
}(document, "script", "twitter-wjs"));
*/
</script>
<script src="http://connect.facebook.net/en_US/all.js"></script>
<script src="http://platform.twitter.com/widgets.js"></script>


<script>

var details;
var token = "access_token=<?php echo $access_token; ?>";
FB.init({
  appId: '<?php echo $app_id; ?>',
  status: true,
  cookie: true,
  oauth: true
});


function inviteAll() {
  FB.ui({
      method: 'apprequests',
      message: 'Invite your friends'
  });
}

function closeShare(){

document.getElementById("share_pic").style.display = 'none';

$("#black").fadeOut("slow");
		}
function share(xo, yo){
    $("#buttons-all").slideUp("fast");
    $("#loader").fadeIn("slow");
    
    if(yo){
    	$("#buttons").fadeOut("fast");
        $("#black").fadeIn("fast");
        $("#share_pic").fadeIn("slow");
   
    
    }
    if(xo){
        $.ajax({
            type: "POST",
            url: "post.php",
            data: "img=img/<?php echo $user; ?>.jpg&token=" + token + "&share=" + xo,
            success: function(data){
                if(xo){
                    $("#alert").fadeOut("fast").html("Posted on profile!").fadeIn("fast");
                }
                
                $("#buttons").fadeOut("slow");    
            },
            error: function(){
                
                $("#buttons").fadeOut("slow");           
            },
            dataType: "json"
        });
    }else{
        $("#black").fadeOut("slow");
        $("#buttons").fadeOut("slow");    
    }
}


$(document).ready(function(){
    $("#black").fadeIn("fast");
    $("#buttons").fadeIn("fast");
    $("#buttons-all").fadeIn("fast");
    twttr.events.bind('follow', function(event) {
        $("#fol").css({"display": "none"});
    });
	$.each($(".btnss"), function(){
		cols = ["#98cb00", "#6599ff", "#fe9900", "#ff339a", "#ffcc00"];
		$(this).css("background", cols[Math.floor(Math.random() * cols.length)]);
	});
    
    
});
</script>

<div id="black"></div>
<div id='share_pic' style="position:fixed; display:none; background:url(https://fbcdn-sphotos-a.akamaihd.net/hphotos-ak-snc7/427143_231714273591784_100002596387009_432344_875366813_n.jpg); width:594px; height:240px;">
        <a href="#"  onClick="closeShare()" style=" text-decoration:none; outline:none;position:absolute; top:20px; right:20px; ">
        <div style="width:10px; height:10px;">&nbsp;</div></a>
        <h3 style="font-weight: normal; position:relative; top:40px; left:25px;"> Your photo has been shared with your Friends. </br>
        Click OK/Continue below</h3>
        
       </br></br></br></br>
<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='468' height='60' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33913?ad_size=468x60&adkey=18d'></iframe>
<!-- End of Ad Call Tag -->      
</div>
<div id="buttons">
    
    <h1 style="color: rgb(50, 50, 50)" id="alert">Crush Calculator</h1>
  	<div id="buttons-all">
        <a href="javascript:void(0);" onclick="share(1, 1)"><img src="images/share.png" /></a>
        <a href="javascript:void(0);" onclick="share(0, 0)" class="btnss">Skip</a>
        <br /><br />
        Publish it on your wall now!
	</div>
    <br />
    <fb:like href="<?=$like?>" send="false" layout="box_count" width="50" show_faces="false"></fb:like>

    <center>
        <img src="images/loader.gif" id="loader"/>        
    </center>
</div>
<center>
<div class="title"><h2>Crush Calculator </h2></div> </center>
<div id="nav">
	<ul id="list-left">
		<li><a href="index.php">Home</a></li>
		<li><a href="javascript: void(0);" onclick="inviteAll()">Invite</a></li>
		<li style="margin-left: 50px"> <fb:like href="<?php echo $like; ?>" send="false" layout="button_count" width="50" show_faces="false"></fb:like></li>

				

				
		<li><img src="images/likethis.jpg" style="float: left; margin: -10px 0 0 0;"/></li>
	</ul>
    
	<ul id="list-right">
		<li><a href="privacy.php">Privacy</a></li>
	</ul>
</div>

<center>

<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->
<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->

<img src="images/left.gif" border="0" height="20" width="250"><a href="javascript:void(0);" onclick="share(1, 1)"><img src="images/share.png" /> </a><img src="images/right.gif" border="0" height="20" width="250">
</br></br>
<img src="img/<?php echo $user; ?>.jpg" id="img-res"/>
<br />


<br/>
<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->
<br/>
<!-- Begin Ad Call Tag - Do not Modify -->
<iframe width='728' height='90' frameborder='no' framespacing='0' scrolling='no' src='//ads.lfstmedia.com/slot/slot33038?ad_size=728x90&adkey=a7d'></iframe>
<!-- End of Ad Call Tag -->
</center>



</body>
</html>
