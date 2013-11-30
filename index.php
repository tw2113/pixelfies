<?php
	$babe = $_GET['babe'];
	$freshBabe = 'not-yet-a-babe-not-yet-a-woman';
	
	// thank you models for being so awesome and pixelated
	$selfies = array();
	foreach (glob('selfies/*.png') as $filename) { 
		$name = str_replace('selfies/', '', $filename);
		$name = str_replace('.png', '', $name);
		array_push($selfies, $name);
	}
	
	function getRGB( $color ) {
		$r = ($color >> 16) & 0xFF;
		$g = ($color >> 8) & 0xFF;
		$b = $color & 0xFF;
		return $r . ',' . $g . ',' . $b;
	}
	
	if ( $babe && in_array( $babe, $selfies ) ) {
		$babeClass = $babe;
	}
	else if ( $babe && !in_array( $babe, $selfies) ) {
		$babeClass = $freshBabe;
	}
?>

<!doctype html>
<html>
<head>
	<title>~*pixelfies*~ real selfies of real people with real pixels</title>
	<style type="text/css">
		body { position: relative; font-family: monospace; text-align: center; }
		a { color: blue; }
		#gallery { display: inline-block; width: auto; margin: 10px; position: relative; text-align: center; width: 800px; }	
		#about-this-thing { position: absolute; top: 20px; right: 0; text-align: right; z-index: 100000000; font-weight: bold; padding: 10px; background: rgba(255,255,255,.2); }
		#about-this-thing a { color: #000; }
		.pixel { width: 12px; height: 12px; float: left; }
		.break { width: 0; height: 0;  clear: both; }	
		.pixelfie { display: none; margin: 20px; position: relative; }
		.model { position: absolute; bottom: 12px; left: 12px; background: #fff; padding: 5px; font-size: 2em; text-transform: uppercase; }
	</style>
</head>

<body>

<?php if ( $babeClass == $freshBabe ) {
			echo '<div id="alert">The babe you entered is not actually a part of #pixelfie. If this babe is *you*, send your selfie to <em>jenn@pancaketheorem.com</em>. In the meantime, check out some other babes...</div>';
	  }
?>

<div id="gallery">


<div id="about-this-thing">
	<h1>#pixelfies</h1>
	<p>real people<br />
	real selfies<br />
	real pixels<br />
	real drama</p>
	<p>made by<br /><a href="http://twitter.com/jennschiffer">@jennschiffer</a></p>
	<div id="controls">
		<button id="random">random pixelfie</button>
		<p>#<span id="hex">ffffff</span></p>
	</div>
</div>

	<div id="pixelfies" <?php if ( $babeClass != $freshBabe ) { echo 'class="' . $babeClass . '"'; } ?>>
		<?php 
			foreach ( $selfies as $selfie ) {
				
				$filename = 'selfies/' . $selfie . '.png';
				$png = imagecreatefrompng($filename);
				$imageSize = getimagesize($filename);
				$imageWidth = $imageSize[0];
				$imageHeight = $imageSize[1];
				
				echo '<div id="' . $selfie . '" class="pixelfie">';
				
				for ( $row = 1; $row < $imageHeight; $row++ ) {
			
					for ( $column = 1; $column < $imageWidth; $column++ ) {
						echo '<div class="pixel" style="background:rgb(' . getRGB( imagecolorat($png, $column, $row) ) . ');"></div>';
					}
					echo '<div class="break"></div>';				
				}
				echo '<div class="model"><a href="/stuff/pixelfies/?babe=' . $selfie . '">' . $selfie . '</a></div>';
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
	var $pixelfies = $('.pixelfie').hide();
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
		$('.current').removeClass('.current').hide();
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
			$('.current').find('.pixel').bind('mouseover', function(e){
			loopOn = false;
			var newColor = $(this).css('background-color');
			$body.css('background-color', newColor );
			$hex.text( rgbToHex(newColor) );
		});
    }
        
	// randomize on click
	$buttonRandom.click(function(e){
		$('.current').find('.pixel').unbind('mouseover');
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