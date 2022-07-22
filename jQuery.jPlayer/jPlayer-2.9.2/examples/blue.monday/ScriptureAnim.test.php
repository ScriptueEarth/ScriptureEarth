<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Scripture Animation Video tester</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="../../dist/skin/blue.monday/css/jplayer.blue.monday.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../../../_js/jquery-3.3.1.min.js"></script>
<script type="text/javascript" src="../../../../_js/jquery.jplayer-2.9.2.min.js"></script>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){

	$("#jquery_jplayer_1").jPlayer({
		ready: function () {
			$(this).jPlayer("setMedia", {
				title: "Scripture Animation Videos",
				m4v: "../../../../data/videos/amu Amuzgo de Guerrero Mark 15, 16 Jesus Crucifixion and Resurrection final video.mp4",
				//m4v: "http://www.jplayer.org/video/m4v/Big_Buck_Bunny_Trailer.m4v",
				//ogv: "http://www.jplayer.org/video/ogv/Big_Buck_Bunny_Trailer.ogv",
				//webmv: "http://www.jplayer.org/video/webm/Big_Buck_Bunny_Trailer.webm",
				poster: "../../../../data/~images/ScriptureAnim/ScriptureAnim.png"
			});
		},
		swfPath: "../../../../_js",
		supplied: "webmv, ogv, m4v",
		size: {
			width: "640px",
			height: "360px",
			cssClass: "jp-video-360p"
		},
		useStateClassSkin: true,
		autoBlur: false,
		smoothPlayBar: true,
		keyEnabled: true,
		remainingDuration: true,
		toggleDuration: true
	});

});
//]]>
</script>
</head>
<body>
<div id="jp_container_1" class="jp-video jp-video-360p" role="application" aria-label="media player">
	<div class="jp-type-single">
		<div id="jquery_jplayer_1" class="jp-jplayer"></div>
		<div class="jp-gui">
			<div class="jp-video-play">
				<button class="jp-video-play-icon" role="button" tabindex="0">play</button>
			</div>
			<div class="jp-interface">
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
				<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
				<div class="jp-controls-holder">
					<div class="jp-controls">
						<button class="jp-previous" role="button" tabindex="0">previous</button>
						<button class="jp-play" role="button" tabindex="0">play</button>
						<button class="jp-stop" role="button" tabindex="0">stop</button>
						<button class="jp-next" role="button" tabindex="0">next</button>
					</div>
					<div class="jp-volume-controls">
						<button class="jp-mute" role="button" tabindex="0">mute</button>
						<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
						<div class="jp-volume-bar">
							<div class="jp-volume-bar-value"></div>
						</div>
					</div>
					<div class="jp-toggles">
						<!--button class="jp-repeat" role="button" tabindex="0">repeat</button-->
						<button class="jp-full-screen" role="button" tabindex="0">full screen</button>
					</div>
				</div>
				<div class="jp-details">
					<div class="jp-title" aria-label="title">&nbsp;</div>
				</div>
			</div>
		</div>
		<div class="jp-no-solution">
			<span>Update Required</span>
			To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
		</div>
	</div>
</div>
</body>

</html>
