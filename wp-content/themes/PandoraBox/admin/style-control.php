<link rel="stylesheet" media="screen" type="text/css" href="<?php echo get_template_directory_uri() ?>/admin/colorpicker/css/colorpicker.css" />
<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/admin/colorpicker/js/colorpicker.js"></script>

<style type="text/css">
	.style-control{
		position: fixed;
		z-index: 2000;
		left: -400px;
		top: 30%;
		background: #f0f0f0;
		width: 400px;
		box-shadow: 0 2px 2px rgba(0,0,0,0.3);

		  -webkit-transition: all 0.5s ease;
		  -moz-transition: all 0.5s ease;
		  -ms-transition: all 0.5s ease;
		  -o-transition: all 0.5s ease;
		  transition: all 0.5s ease;
	}

	.style-control.active{
		left: 0;
	}

	.style-control .wrapper{
		padding: 20px;
	}

	.style-control h3{
		font-size: 22px;
		color: #777;
		margin: 5px;
		font-weight: 400;
	}

	.style-control p{
		margin: 20px 5px 10px 0;
		padding-left: 5px;
		color: #555;
		border-left: 3px solid #00a99d;
	}

	#toggle{
		position: absolute;
		right: -60px;
		top: 0;
		width: 55px;
		height: 55px;
		text-align: center;
		background: #f0f0f0;
		border-right: 4px solid #bbb;
		border-left: 1px solid #ddd;
		cursor: pointer;
		color: #777;
	}

	#toggle i{
		margin-top: 10px;
		display: inline-block;
	}

	#toggle:hover{
		color: #00a99d;
		border-right-color: #00a99d;
	}

	.style-control .style-select{
		width: 40px;
		height: 40px;
		border: 3px solid #ddd;
		margin: 0 20px 20px 0;
		float: left;
		cursor: pointer;
	}

	.style-control .style-select.active{
		border-color: #00a99d;
	}

	.style-control .style-select:hover{
		border-color: #aaa;
	}

	.style-control #style-color{
		background-color: #00a99d;
	}

	.style-control #style-pattern{
		background: url('<?php echo get_template_directory_uri() ?>/admin/colorpicker/img/th-pattern.png');
	}

	.style-control #style-photo{
		background: url('<?php echo get_template_directory_uri() ?>/admin/colorpicker/img/th-photo.png');
	}


	@media screen and (max-width: 540px) {
		.style-control{
			display: none;
		}
	}
</style>

<script type="text/javascript">
jQuery(document).ready(function($){
	$('#colorSelector').ColorPicker({
		flat: true,
		color: '#00a99d',
		onChange: function (hsb, hex, rgb) {
			outcss = [
				//Background-color
					"button,input.send,input[type='submit'] , .hexagon .inner , .colorblock , .storebutton:hover , .iconmenu, .mainmenu, .pb_slider .navigation:hover , .priceblock .block-container .pricetable .priceitem, .contactsblock .input-container input[type='submit'], .footerblock, .blogblock .navigation .page-numbers.current, .mainmenu .menuwrapper .menulist ul li ul li, .contactsblock .block-container .wrapper .mailbutton.active, .contactsblock .block-container .wrapper .contact-info a:hover { background-color: " + '#' + hex + ";}",

					"*::selection,input::selection,textarea::selection { background-color:" + '#' + hex + ";}",

					//Text color
					"a , .whiteblock a , .socialblock .socialicon i:hover , .skillsblock .skillcontainer:hover .skillogo .logocontainer , .skillsblock .skillcontainer:hover .title , .blogblock .block-container .post .post-body .title a:hover , .blogblock .block-container .post .meta .type i:hover { color: " + '#' + hex + ";}",

					//Border-color
					"input:focus,textarea:focus , .skillsblock .skillcontainer .wrapper .skillogo .logocontainer , .teamblock .block-container .teamcontainer .personal:hover .photo , .contactsblock .block-container .wrapper .mailbutton:hover, .contactsblock .block-container .wrapper .mailbutton.active { border-color: " + '#' + hex + ";}",

					".hexagon .inner:before {border-color: transparent transparent " + '#' + hex + " transparent; }",

					".hexagon .inner:after {border-color: " + '#' + hex + " transparent transparent transparent; }",

					".skillsblock .skillcontainer:hover .skillogo .logocontainer , .teamblock .block-container .teamcontainer .personal .photo { border-color: #c8c8c8; }", 

					".comments .commentlist .comment-article:hover .comment-block img { box-shadow: 0 0 0 3px #fff, 0 0 0 6px #" + hex + ";}"
					];

		$("#dynstyles").text(outcss.join('\n'));
		}

	});

	$('.style-control #toggle').on("click", function(){
		$('.style-control').toggleClass('active');
	});

	$('.style-control #style-color').click(function(){
		$('.style-select').removeClass('active');
		$(this).addClass('active');
		$('body link:last').remove();
		$('body style:last').remove();
		$('body').append('<link href="" type="text/css" rel="stylesheet" />');
	$('body').append('<style></style>');
	});

	$('.style-control #style-pattern').click(function(){
		$('.style-select').removeClass('active');
		$(this).addClass('active');
		$('body link:last').remove();
		$('body style:last').remove();
		$('body').append('<link href="<?php echo get_template_directory_uri() ?>/styles/background-pattern.css" type="text/css" rel="stylesheet" />');
		$('body').append('<style> .colorblock{background: url("<?php echo get_template_directory_uri() ?>/admin/colorpicker/img/pattern.png") repeat; } </style>');
	});

	$('.style-control #style-photo').click(function(){
		$('.style-select').removeClass('active');
		$(this).addClass('active');
		$('body link:last').remove();
		$('body style:last').remove();
		$('body').append('<link href="<?php echo get_template_directory_uri() ?>/styles/background-photo.css" type="text/css" rel="stylesheet" />');
		$('body').append('<style> .colorblock{background: url("<?php echo get_template_directory_uri() ?>/admin/colorpicker/img/photo.jpg") no-repeat; background-size: cover; } </style>');
	});

	$('body').append('<link href="" type="text/css" rel="stylesheet" />');
	$('body').append('<style></style>');
});
</script>

<div class="style-control">
	<div class="wrapper">
		<h3>PandoraBox Options</h3>

		<p>Basic color:</p>
		<div id="toggle"><i class="icon-cog icon-2x"></i></div>
		<div id="colorSelector"></div>

		<p>Select style:</p>
		<div class="style-select active" title="Color background" id="style-color"></div>
		<div class="style-select" title="Pattern background" id="style-pattern"></div>
		<div class="style-select" title="Photo background" id="style-photo"></div>
	</div>
</div>

