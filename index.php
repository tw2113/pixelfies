<?php

	$user = isset( $_GET['user'] ) ? $_GET['user'] : 'tw2113';
	$apikey = 'fcc6f589d15b8c8fdcbca5266d74620a';
	$lastfm = json_decode(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=$user&limit=100&&nowplaying=true&format=json&api_key=$apikey"));

	$selfies = array();
	foreach($lastfm->recenttracks->track as $cover) {
		if($cover->image[0]->{'#text'} && strpos($cover->image[0]->{'#text'}, 'png')) {
			$selfies[] = $cover->image[0]->{'#text'};
		}
	}
	$selfies = array_unique( $selfies );

	function getRGB( $color ) {
		$r = ($color >> 16) & 0xFF;
		$g = ($color >> 8) & 0xFF;
		$b = $color & 0xFF;
		return $r . ',' . $g . ',' . $b;
	}

?>

<!doctype html>
<html lang="en">
<meta charset="UTF-8">
	<title>~*pixelfies*~ Last.FM: real albums by real artists with real pixels</title>
	<link rel="icon" type="image/png" href="favicon.png">
	<style type="text/css">
		body { position: relative; font-family: monospace; text-align: center; }
		a { color: blue; }
		#gallery { display: inline-block; width: auto; margin: 10px; position: relative; text-align: center; width: 100%; max-width: 625px; }
		#about-this-thing { width: 175px; float: right; text-align: right; z-index: 100000000; font-weight: bold; padding: 10px; margin-top: 10px;  background: rgba(255,255,255,.2); }
		#about-this-thing a { color: #000; }
		#about-this-thing em { font-weight: normal; font-style: italic; }
		.pixel { width: 12px; height: 12px; float: left; }
		.break { width: 0; height: 0;  clear: both; }
		#pixelfies { width: 396px; height: 396px; padding: 0; margin: 10px 0; background: turquoise url('hourglass.gif') no-repeat center center; }
		.pixelfie { display:none; position: relative; float: left; }
		.model { position: absolute; bottom: 12px; left: 12px; background: #fff; padding: 5px; font-size: 2em; text-transform: uppercase; }

		@media (max-width: 800px) {
			#gallery { width: 588px; margin: 0 auto; display: block; }
			#about-this-thing { width: 100%; max-width: 568px; float: none; margin: 0 0 10px; display: block; text-align: center; }
			#pixelfies { float: none; margin: 0 auto; text-align: center; }
		}
	</style>
</head>

<body>

<div id="gallery">

<div id="about-this-thing">
	<h1>#pixelfies</h1>
	<p>Last.FM<br />
	real albums<br />
	real artists<br />
	real drama</p>
	<p>fork made made from<br /><a href="http://twitter.com/jennschiffer">@jennschiffer</a></p>
	<div id="controls">
		<button id="random">random pixelfieslastfm</button>
		<p>#<span id="hex">ffffff</span></p>
	</div>

	<p><em>show *your* true colors by listening to music.</em> Change user by setting /?user=USERNAME parameter</p>
</div>

	<div id="pixelfies">
		<?php
			foreach ( $selfies as $selfie ) {
				$png = imagecreatefrompng($selfie);
				$imageSize = getimagesize($selfie);
				$imageWidth = $imageSize[0];
				$imageHeight = $imageSize[1];

				echo '<div id="" class="pixelfie">';

				for ( $row = 1; $row < $imageHeight; $row++ ) {

					for ( $column = 1; $column < $imageWidth; $column++ ) {
						echo '<div class="pixel" style="background:rgb(' . getRGB( imagecolorat($png, $column, $row) ) . ');"></div>';
					}
					echo '<div class="break"></div>';
				}
				echo '</div>';
			}
		?>
	</div>

</div>


<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript">
$(function(){

	var $body = $('body');
	var galleryClass = $('#pixelfies').attr('class');
	var $pixelfies = $('.pixelfie');
	var $buttonParty = $('#party');
	var $buttonRandom = $('#random');
	var $alert = $('#alert');
	var $hex = $('#hex');
	var index = -1;
	var colorFlashMode;
	var loopOn = true;


	// get random index but never same two in a row
	var getRandomIndex = function() {
		var newIndex = Math.floor(Math.random() * ( $pixelfies.length - 1 ));
		if ( newIndex == index ) {
			newIndex--;
			if ( newIndex < 0 ) {
				newIndex = $pixelfies.length -1;
			}
		}
		return newIndex;
	};

	// get next pixelfie
	var nextPixelfiePlz = function() {
		index = getRandomIndex();
		var $currentPixelfie = $pixelfies.eq(index).show().addClass('current');
		$pixels = $currentPixelfie.find('.pixel');
		var $currentPixelfieId = $currentPixelfie.attr('id');
		var $currentPixelfiePixels = $currentPixelfie.find('.pixel');
		$body.removeAttr('class').addClass($currentPixelfieId);

		bindPixelMouseover();
	};

	// rgb to hex
	var rgbToHex = function( rgb ) {
        var rgbArray = rgb.substr(4, rgb.length - 5).split(',');
        var hex = "";
        for ( var i = 0; i <= 2; i++ ) {
            var hexUnit = parseInt(rgbArray[i]).toString(16);
            if ( hexUnit.length == 1 ) {
                hexUnit = '0' + hexUnit;
            }
            hex += hexUnit;
        }
        return hex;
    };

    var bindPixelMouseover = function(){
		// background change on hover
			$('.current').find('.pixel').on('mouseover', function(e){
			loopOn = false;
			var newColor = $(this).css('background-color');
			$body.css('background-color', newColor );
			$hex.text( rgbToHex(newColor) );
		});
    }

	// randomize on click
	$buttonRandom.click(function(e){
		var currentPixels = $('.current').find('.pixel');
		currentPixels.off();
		$('.current').removeClass('current').hide();
		nextPixelfiePlz();
	});

	// init if value given, else random
	if ( galleryClass ) {
		$('#' + galleryClass).show().addClass('current');
		bindPixelMouseover();
	}
	else {
		$buttonRandom.click();
	}
});
</script>

<!-- just some analytics, whatever -->
<script src="//pmetrics.performancing.com/js" type="text/javascript"></script>
<script type="text/javascript">try{ clicky.init(14721); }catch(e){}</script>
<noscript><p><img alt="Performancing Metrics" width="1" height="1" src="//pmetrics.performancing.com/14721ns.gif" /></p></noscript>
</body>
</html>
