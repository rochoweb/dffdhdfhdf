/* 
	MonaMade.sk JS
*/

$(document).ready(function () {

	var url = [];
		url["switch"] = "http://" +  window.location.hostname + "/" + "temp/switch.php";
		url["action"] = "http://" +  window.location.hostname + "/" + "temp/action.php"
		
	var reset;
	
	/* CART PAGE */

	if ( $(".cart").length ) {
		var $sidebar   = $(".cart"), 
			$window    = $(window),
			offset     = $sidebar.offset(),
			topPadding = $("#fixedHeader-line").height() + 30; //120


		$window.ready(function() {
			cart_pos();
		});

		$window.scroll(function() {
			cart_pos();
		});
		
		

		function cart_pos() {		
			
			if (($window.scrollTop() + $("#fixedHeader-line").height() + 10 > offset.top ) && document.documentElement.clientWidth > 1024) {

				if ( ($window.scrollTop() + $(".cart").height()) <= $(".bd-left").height() ) {
					$sidebar.stop().css({
						marginTop: $window.scrollTop() - offset.top + topPadding
					});
				}
			} else {
				$sidebar.stop().css({
					marginTop: 0
				});
			}
		}

			
		$(window).resize(function (e) {
			$sidebar.stop().css({
				marginTop: 0
			});
		});
	}
	

	/* CART PAGE */

	function inputData2 (target) {
		var inputs = target.find("input");
		
		var obj = {};

		inputs.map( function () {
			obj[this.id] = this.value;
		});

		return obj;
	}

	$(".cart-continue").bind("click", function (e) {
		var DATA = inputData2( $("#basketform") ),
			type = $(this).attr("id"),
			load = $(".cart-load"),
			result = $(".result");

		load.show(0);
		result.hide(0);

		clearTimeout(reset);

		

		if ( $(this).hasClass('contactive') ) {
			$(this).removeClass('contactive');
			reset = setTimeout(function () {
				

				$.post(url["switch"], { lib: "basket", t: type, d: DATA }, function (data) {
					
					if ( data.errors ) {
						var err = 0;

						$.each(data.errors, function (e, type) {
							err += 1;

							var input = $("#" + e),
								error_icon = input.parent().find( $(".baStatusIcon") ),
								error_text = input.parent().find( $(".baStatus") );
							
							input.parent().attr("class", "inpbox inpBAD")
							error_icon.html( '<i class="fa fa-times baBAD" aria-hidden="true"></i>' );
							error_text.show().find("label").html( type );

							if ( err == 1 )
								input.focus();  
							
						});
					}

					if ( data.error ) {
						result.html( data.error ).show(0);

						if ( document.documentElement.clientWidth <= 1024)
							$(".mobile-statusbar").show().find( $(".mstatus") ).html( data.error );
					}

					if ( data.continue )
						window.location = data.continue;
					

					if ( data.scroll )
						scroll_to( $("#" + data.scroll) );
					

					if ( data.complete ) {
						$("#sendorder").remove();
						show_fcmessage( data.complete );
					}
					load.hide(0);

				}, "json");
			}, 500);
		}
		

		return false;
	});

	function show_fcmessage (mess) {
		var fc = $(".fullscreen_message"),
			content = fc.find( $(".fc-content") ),
			back = $(window).scrollTop();

		fc.show(0, function () {
			var height = $("#fixedHeader-line").height();

			$("body, html").scrollTop(0);
			$("#MonaMade").attr("class", back);

			content.css("display", "block").animate({ margin: height + "px 0 0 0" }, 400);

			setTimeout(function () {
				fc.find( $(".fcR-contentt") ).html( mess );
				fc.find( $(".fcR") ).css("display", "block");
				fc.find( $(".fcTemp") ).css("display", "none");
			}, 500);
			


			fc.css({ width: $("body").width(), height: $("body").height() });
		});
	}

	// TABS

	$(".tabmenu a").bind("click", function (e) {
		var data = $(this).attr("href").replace('#','');

		$(this).parent().parent().find( $("#tabA") ).removeAttr("id");
		$(this).attr("id", "tabA");


		$(this).parent().parent().parent().parent().find( $(".tab") ).hide(0);
		$("." + data).show(0);

		return false;
	});

	$(".tabSwitcher").bind("click", function (eve) {
		var data = $(this).attr("href").replace('#',''),
			menu = $(this).data("menu"),
			target = $(this).data("target"); 

		$("." + target).trigger("click");

		return false;
	});

	// TABS


	function scroll_to(id){
		
		var pos = id.offset().top,
			minusHead = $("#fixedHeader-line").height();

		$('html,body').animate({
			scrollTop: pos - minusHead - 67},
		250);
	}

});