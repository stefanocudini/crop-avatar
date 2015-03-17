$(function(){

	var crop$,
		scale=100,
		imgfile = $('#imcrop').attr('src');

$.ajaxSetup({cache: false});
/*$('#thumbs')
.ajaxStart(function(e) {
	if($(e.target).is('#thumbs'))
		$(this).addClass('loading');
})
.ajaxStop(function() {
	if($(e.target).is('#thumbs'))
		$(this).removeClass('loading');
});//*/

	initCrop();//eseguito on document ready
	initRotate();
	listCrop();
	
	function initCrop() {
		if(crop$ && crop$.destroy)
			crop$.destroy();
		crop$ = $.Jcrop('#imcrop');
		setCrop();
		crop$.disable();
	}
	
	function initRotate() {
		var w = $('#imcrop').width();
		var h = $('#imcrop').height();
		$('#imcrop_wrap').css({position:'relative'}).width(w).height(h);
		$('#imcrop').css({position:'absolute'});
	}
	
	function setCrop() {  //croppa secondo il valore di scale
	
		var vh = parseInt($('#imcrop_wrap').height());
		var vw = parseInt($('#imcrop_wrap').width());
		var k = (100-scale)/2;
		crop$.setSelect( [Math.round((vw/100)*k),
						  Math.round((vh/100)*k),
						  vw-Math.round((vw/100)*k),
						  vh-Math.round((vh/100)*k) ] );
	}
	
	function listCrop() {
		$.get('./viewlist.php',{},function(data) {
				$('#list').html($(data));
			},'html');
	}
	
	$('#sldim').slider({
		min: 10,
		max: 100,
		step: 5,
		value:100,
		slide: function(event, ui) {
			scale = parseInt(ui.value);
			setCrop();
			$(this).next().html( scale +'%');
		}
	});
	
	$('#slrot').slider({
		min: -180,
		max: 180,
		step: 10,
		value: 0,
		slide: function(event, ui) {
			$('#imcrop').rotate( ui.value ,'abs');
			$(this).next().html( ui.value +'&deg;');
			$('#log').text( $('#imcrop').width() );

			$('#imcrop').position({		//centra la foto ruotata
			my:'center',
			at:'center',
			of: $('#imcrop_wrap')});
		},
		change: function(event, ui) {	//ruota
			var angle = ui.value;

			if(angle==0)
			{
				if(crop$ && crop$.destroy)
					crop$.destroy();
				$('#imcrop').attr('src',imgfile);
				initCrop();
			}
			else
			{
				$.get('./rotate.php',	//rotazione lato server in ajax
				{
					filename: imgfile,
					angle: angle
				},
				function(src) {	//ritorna url dell'immagine ruotata
					var w = $('#imcrop_wrap').width();
					var h = $('#imcrop_wrap').height();
					$('#imcrop').attr('src',src).width(w).height(h);
					initCrop();
				});
			}
		}//*/
	});
	
	$('#btsav').button().click(function() {
		var dim = crop$.tellSelect();
		dim.filename = $('#imcrop').attr('src');
		$('#thumbs').load('./crop.php?'+$.param(dim), listCrop );
		//$('#btres').button('enable');
	});

/*	$('#btres').button().button('disable').click(function() {
		$('#sldim').slider('value', 100);
		$('#slrot').slider('value', 0);
		scale = 100;
		$('#imcrop').attr('src',imgfile);//non funziona qui!!
		setCrop();
	});//*/

	$('#btlist').button().click(function() {
		listCrop();
	});
	
});
	
