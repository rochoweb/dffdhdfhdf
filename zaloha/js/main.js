/* 
	MonaMade.sk JS
*/

$(document).ready(function () {

	var LANG = $("#MonaMade").attr("lang"),
		UON = $("body").attr("id");


	var url = [];
		url["switch"] = "http://" +  window.location.hostname + "/" + "temp/switch.php";
		url["action"] = "http://" +  window.location.hostname + "/" + "temp/action.php";
	
	var onblur, update;

	$(window).bind("blur focus click", function () {
		
		clearTimeout(onblur);

		onblur = setTimeout(function() {

			if ( $("body").attr("id") ) {
				activity_update();
			}

		}, 10000);
	});

	$(window).bind("dragover dragleave drop", function () {
		return false;
	});
	
	var reset;


	$(window).scroll(function () {
		var pos = $(window).scrollTop(),
			w = responsive();

		if ( pos > 0 ) {

			var actPos = $(".lines").css("padding-top").substr(0, $(".lines").css("padding-top").length-2),
				actPosAfix = Number(actPos) - $(".category-content").height()

			$("#fixedHeader-line").css("box-shadow", "0 2px 2px -1px #d0d0d0;");
		} else {
			$("#fixedHeader-line").css("box-shadow", "0");
		}

		if ( $(".fim-menu").css("display") == "block" ) {
			$(".fim-button").show(0);
			$(".fim-menu").hide(0);

		}

		if ( PID == 16 ) {
			if ( w == 'mobilel' || w == 'mobilem' || w == 'mobiles' ) {
				var images = $('.ipl-content').height(),
					head = $('.head-content');

				if ( pos > images - (head.height() / 2) )
					head.css('background', 'rgba(255, 255, 255, .99)');
				else
					head.css('background', 'rgba(255, 255, 255, .6)');
			}
		}
			
	});



	/* MENU */


	var $nav = $('.navimenu');
	var $btn = $('.navimenu button');
	var $vlinks = $('.navimenu .visible-links');
	var $hlinks = $('.navimenu .hidden-links');

	var numOfItems = 0;
	var totalSpace = 0;
	var breakWidths = [];

	// Get initial state
	$vlinks.children(".nm-def").outerWidth(function(i, w) {
		//console.log( this );
		//if ( !$(this).hasClass("link-menu") ) {
			totalSpace += w;
			numOfItems += 1;
			breakWidths.push(totalSpace);
		///}
	});

	var availableSpace, numOfVisibleItems, requiredSpace;


	function check() {

	// Get instant state
		availableSpace = $vlinks.width() - 100;
		numOfVisibleItems = $vlinks.children(".nm-def").length;
		requiredSpace = breakWidths[numOfVisibleItems - 1];

		// There is not enought space
		if (requiredSpace > availableSpace) {
		  $vlinks.children(".nm-def").last().prependTo($hlinks);
		  numOfVisibleItems -= 1;
		  check();
		  // There is more than enough space
		} else if ( availableSpace > breakWidths[numOfVisibleItems] ) {
		  $hlinks.children().first().insertAfter( $vlinks.children(".nm-def").last() );
		  numOfVisibleItems += 1;
		}
		// Update the button accordingly
		$btn.attr("count", numOfItems - numOfVisibleItems);
		
		if (numOfVisibleItems === numOfItems) {
	 	 	$btn.addClass('hidden').hide(0);
		} else {
			$btn.removeClass('hidden').show(0);
		}
	}

	$btn.on('click', function() {
		$hlinks.toggleClass('hidden');
	});

	$(window).resize(function() {
		var w = responsive(0);

		for (var i = 0; i < numOfItems; i++) {
			check();
		}
		//check();
		responsive();

		slider_buttons();

		if ( $(".navimenu2 .visible-links").length || $(".scrollv").length ) {
			scrolleft_buttons( $(".navimenu2 .visible-links") );
		}

		$(".fullscreen, .fullscreen_message, #imgzoom").css({ width: $("body").width(), height: $("body").height() });

		if ( w != 'tablet' && w != 'mobilel' && w != 'mobilem' && w != 'mobiles') {
			if ( $("#gmap").length )
				initialize();
		}
	});

	$(window).scroll(function() {
		check();
	});

	check();

	/*MENU*/

	$(document).on("contextmenu", "img", function (e) {
		return false;
	});

	/* ITEM HISTORY SLIDER */

	$(".slider").on("swipeleft swiperight", function (e) {
		
		var buttonLeft = $(this).find( $(".posLeft") ),
			buttonRight = $(this).find( $(".posRight") );

		switch(e.type) {
			case "swipeleft":

				if ( buttonLeft.css("display") != "none" )
					buttonLeft.trigger("click");
			break;

			case "swiperight":

				if ( buttonRight.css("display") != "none" )
					buttonRight.trigger("click");
			break;
		}
	});

	$(".slider").on("swipeleft swiperight", function (e) {
		
		var buttonLeft = $(this).find( $(".posLeft") ),
			buttonRight = $(this).find( $(".posRight") );

		switch(e.type) {
			case "swipeleft":
				if ( buttonLeft.css("display") != "none" ) buttonLeft.trigger("click");
			break;

			case "swiperight":
				if ( buttonRight.css("display") != "none" ) buttonRight.trigger("click");
			break;
		}
	});

	$(".sLR").on("click", function (e) {

		var slider = $(this).parent().find( $(".sliderItems") ),
			divs = slider.children(),
			divW = slider.children(":first-child").width();

		if ( slider.is(":animated") ) return false;

		if ( $(this).hasClass("posLeft") ) {
			var tmp = slider.children(":last-child").detach();

			slider.prepend(tmp).css({ left: slider.position().left - divW });

			slider.animate({ left: slider.position().left + divW }, 200);
		} else if ( $(this).hasClass("posRight") ) {
			slider.animate({ left: slider.position().left - divW }, 200, function () {
				var tmp = slider.children(":first-child").detach();

				slider.append(tmp).css({ left: slider.position().left + divW });
			});
		}

		return false;
	});


	
	slider_buttons();

	function slider_buttons() {
		var w = responsive();

		$(".slider").each(function(i, e) {
			
			var c = $(e).find( $(".diy") ).length,
				buttons = $(e).find( $(".sLR ") );
				n = "";

			switch (w) {
				case "laptopl":
					n = 5;
				break;
				
				case "laptop":
					n = 3;
				break;
				
				case "tablet":
					n = 2;
				break;
				
				case "mobilel":
				case "mobilem":
				case "mobiles":
					n = 1;
				break;

				default:
					n = 6;
			}

			if ( c > n ) {
				buttons.show(0);
			}
			else
				buttons.hide(0);
		});
	};
	
	function responsive() {
		var w = document.documentElement.clientWidth,
			h = document.documentElement.clientHeight,
			r = "";

		if ( w <= 1440 )
			r = "laptopl";
		
		if ( w <= 1024 )
			r = "laptop";
		
		if ( w <= 768 )
			r = "tablet";

		if ( w <= 425 )
			r = "mobilel";
		
		if ( w <= 375 )
			r = "mobilem";

		if ( w <= 320 )
			r = "mobiles";

		$("#MonaMade").attr("data-window", w);

		return r;
	}
	/* ITEM HISTORY SLIDER */




	switch(PID) {
		case 1:
		case 4:
		case 5:
		case 8:
		case 16:
		case 17:
			var timer;

			$(".diyContent").bind("mouseenter focusin mouseleave focusout click", function (e) {
				var parent = $(this).parent(),
					top = $(this).find( $(".diyDescTop") ),
					tags = $(this).find( $(".diyDesc-footer") ),
					diff = $(this).find( $(".diyDesc-diff") );

				switch(e.type) {
					case "mouseenter":
					//case "focusin":
						parent.attr("id", "diyA");

						show( [top] );

						clearTimeout(timer);

						timer = setTimeout(function () {
							diff.css("display", "none");
							diff.fadeIn(200);
						}, 1000);
						break;

					case "mouseleave":
					//case "focusout":
						parent.removeAttr("id");

						
						hide( [top, diff, $(".tagContainer") ] );
						break;
				}

			});

			$(".diyStat, .diffs").bind("mouseenter focusin mouseleave focusout click", function (e) {
				var explain = $(this).find( $(".diyStat-explain") );

				switch(e.type) {
					case "mouseenter":
					case "focusin":

						explain.css("display", "block");
						break;

					case "mouseleave":
					case "focusout":

						explain.css("display", "none");
						break;
					case "click":
						return false;
						break;
				}
			});

			

			$(".si").bind("focusin focusout", function (e) {
				var parent = $(this).parent();

				switch(e.type) {
					case "focusin":

						parent.attr("id", "siA");
						break;

					case "focusout":

						parent.removeAttr("id");
						break;
				}
			});


			$(".moreTags").bind("click mouseenter", function (e) {
				var tags = $(this).parent().find( $(".tagContainer") );

				tags.css("display", "block");
			});
		break;

		case 4:
			var search = $("#p-global-search");
		
			search.focus();
		break;
	}	

	function hide (data) {
		$.each(data, function () {
			this.css("display", "none");
		});
	}

	function show (data) {
		$.each(data, function () {
			this.css("display", "block");
		});
	}

	$("#up").bind("click", function () {
		$("html, body").animate({ scrollTop: 0 }, 250);
	});



	$(".noSlider").bind("click", function () {
		return false;
	});


	var timer;

	$(".search-result, .fcR").delegate("div", "mouseenter mouseleave", function (eve) {
		
		var tags = $(this).find( $(".diyDesc-footer") );

		if ( $(this).hasClass("diyContent") ) {
			var parent = $(this).parent();

			switch(eve.type) {
				case "mouseenter":
					parent.attr("id", "diyA");
					show( [tags] );

					break;

				case "mouseleave":
					parent.removeAttr("id");

					hide( [tags, $(".tagContainer")] );
					break;
			}
		}

	});


	var time;

	$(".dH").bind("mouseenter mouseleave focusin", function (e) {
		var hide = false,
			focus = $(this).find( $("#sA") )
			thiss = $(this);

		if ( !focus.length  ) {
			clearTimeout(time);

			switch(e.type) {
				case "mouseleave":
				case "focusin":
				hide = true; break;
			}

			if ( hide == true ) {
				time = setTimeout(function () {

					thiss.hide(0);
					$(".AA").removeClass("AA");
				}, 1000);
			}
		}
		

	});

	$("input, textarea").bind("focusin focusout keyup change blur", function (eve) {
		var keyup;

		acc_input( $(this), eve.type);
	});


	$(".user-forms input").bind("focusin focusout keyup change blur", function (eve) {
		var keyup;

		acc_input( $(this), eve.type);
	});

	$(".fcR").delegate("input", "focusin focusout keyup blur change", function (eve) {
		var keyup;

		acc_input( $(this), eve.type);
	});

	
	var keyup;

	function acc_input (thiss, eve) {
		
		var type = $(thiss).attr("class"),
			label = $(thiss).parent().find( $("label") ),
			reset = $(thiss).parent().find( $(".input-reset") ),
			ID = $(thiss).attr("id");

		var sett = thiss.parent().parent().hasClass("settings-ui"),
			minCH;
		
		if ( sett )
			minCH = 0;
		else
			minCH = 2;


		if ( thiss.attr("class") )
			var type = thiss.attr("class").split(" ");
		

		if ( type && !$(thiss).hasClass("finddata-select") ) {
			$.each(type, function (e, type) {
				switch(type) {
					case "ss-text":
					case "adi-text":
						if ( type == "adi-text" )
							var parent = $(thiss).parent().parent();
						else
							var parent = $(thiss).parent();

						switch(eve) {
							case "focusin":
								parent.attr("id", "siA");
								break;

							case "keyup":
							case "change":

								if ( reset )
									inputLabel($(thiss), label, reset);
								else
									inputLabel($(thiss), label, "");

								if ( !thiss.hasClass("adi-text") && !thiss.hasClass("global-search") && !thiss.hasClass("global-search-top") ) {
									var id = thiss.attr("id");

									clearTimeout(keyup);

									if ( id != "login-username" && id != "plogin-username" && id != "login-password" && id != "plogin-password" ) {
										var resultBar = thiss.parent().find( $(".resultbar") ),
											icon = thiss.parent().find( $(".ss-icon") );

										resultBar.css("display", "none");
										icon.css("display", "block");

										keyup = setTimeout(function () {

											if ( thiss.val().length >= minCH ) {
												$.post(url["switch"], { lib: "verify",  l: LANG, p: PID, t: thiss.attr("id"), d: thiss.val() }, function (data) {
													
													if ( data.r ) {
														resultBar.html( data.r );
														resultBar.css("display", "block");
														icon.css("display", "none");
													}

												}, "json");
											}
											else {
												icon.css("display", "block");
												resultBar.css("display", "none");
											}
										}, 1500);
									}
								}
								break;

							case "focusout":
								parent.removeAttr("id");
								break;
								
						}
					break;

					case "show-select":
						$(".select-menu").hide(0);

						var menu = thiss.parent().find( $(".select-menu") );

						menu.show(0);

						thiss.parent().find( $(".ir-text") ).hide(0);
					break;

					case "global-search":

						switch(eve) {
							case "keyup":

							if ( thiss.attr("id") == "p-global-search" ) {
								var result = $(".search-result");

								clearTimeout(keyup);

								keyup = setTimeout(function () {
									$.post(url["switch"], { lib: "search",  l: LANG, p: PID, t: thiss.attr("id"), d: thiss.val() }, function (data) {
										
										if ( data.r ) {
											result.show(0);
											result.html( data.r );
											history.pushState( {}, data.url, data.url);
										}

										if ( data.head )
											document.title = data.head;

									}, "json");
								}, 500);
							}

							break;
						}
						
					break;
				}
			});
		}
	}

	function update_searchURL (url, query) {
		var newURL;

		if ( LANG == "sk" )
			newURL = "/hladat/" + query;
		else
			newURL = "/find/" + query;

		history.pushState( {}, newURL, newURL);

		
		return false;
	}

	

	function activity_update () {
		$.post(url["switch"], { lib: "account", l: LANG, p: PID, t: "refresh" }, function(data) {
			if ( data.offline ) {
				show_fatalError( data.offline );
				updateUI();
				$("body").removeAttr("id");
			}
		}, "json");
	}

	function updateUI () {
		$.post(url["switch"], { lib: "account", l: LANG, p: PID, t: "ui" }, function(data) {
			if ( data ) {
				if ( data.menu1 ) $(".user-login-pos").html( data.menu1 );
				if ( data.menu2 ) $(".user-menu-mini").html( data.menu2 );
			}
		}, "json");
	}

	
	$(".user-forms a").bind("click", function (eve) {

		acc_a( $(this), eve );

	});

	$(".fcR").on("click", "a", function (eve) {
		acc_a( $(this), eve );

		if ( !$(this).hasClass("jsl") )
			return false;

		if ( $(this).hasClass("hib") )
			$(".fc-hide").trigger("click");
	});

	$(".right-content").on("click", "a", function (eve) {
		acc_a( $(this), eve );

		if ( $(this).hasClass("jsl") )
			return false;
	});



	if ( $(".navimenu2 .visible-links").length || $(".scrollv").length ) {

		if ( $(".navimenu2 .visible-links").length )
			scrolleft_buttons( $(".navimenu2 .visible-links") );

		if ( $(".scrollv").length )
			scrolleft_buttons( $(".scrollv") );
		
		$(".navimenu2 .visible-links, .scrollv").on("scroll", function (e) {
			
			scrolleft_buttons( $(this) );
		});

	}
	
	function scrolleft_buttons(target) {
		var pos = target.scrollLeft(),
			width = target.width(),
			max = target.get(0).scrollWidth,
			left = target.parent().parent().parent().parent().find( $(".bfmleft") ),
			right =  target.parent().parent().parent().parent().find( $(".bfmright") );

		if ( max > width ) {
			if ( pos <= 4 ) {
				left.hide(0);
			} else
				left.show(0);

			if ( pos === max - width ) {
				right.hide(0);
			} else
				right.show(0);

		} else {
			left.hide(0);
			right.hide(0);
		}
	}





	
	$(".fcR, .user-forms").delegate("form", "submit", function (eve) {
		submit_form( $(this) );
			
		return false;
	});

	$(".search-submit").bind("submit", function (eve) {
		submit_form( $(this) );
			
		return false;
	});

	function submit_form (thiss) {
		if ( LANG == "sk" )
			var page = "hladat";
		else
			var page = "find";


		if ( page ) {
			var url;

			if ( thiss.find("input").val().length > 0 )
				url = "http://" +  window.location.hostname + "/" + page + "/" + thiss.find("input").val() + "/";
			else
				url = "http://" +  window.location.hostname + "/" + page + "/";

			window.location = url;
		}
	}

	
	$(".makediy").on("click", "a", function (eve) {

		if ( !$(this).hasClass("defE") ) {
			acc_a( $(this), eve);
		
			return false;
		}
		
	});

	$(".lines").on("click", "a", function (eve) {
		acc_a( $(this), eve);

		if ( $(this).hasClass("show-filter") )
			return false;
	});

	function acc_a (thiss, eve) {

		if ( thiss.attr("class") )
			var type = thiss.attr("class").split(" ");
		
		
		if ( type ) {
			$.each(type, function (e, type) {
				switch(type) {
					case "fc-show":
						actionEvent( thiss );
						return false;
					break;

					case "show-menu":
						var menu = $( thiss ).parent().find( $(".user-interface") );

						menu.toggle(0);
						return false;
					break;

					case "menu-action":
						if ( eve.type == "click" ) {
							var type = $( thiss ).attr("name");

							$.post(url["switch"], { lib: "account", t: type }, function(data) {
							
								$("body").removeAttr("id");

								updateUI();

								if ( data.mess ) {

									$(".fc-message-content").html( data.mess );
									$(".fc-message").css("display", "block");

									hide_message();
								}

							}, "json");
						}

						return false;
					break;

					case "edit-input":
					case "edit-input-hide":
						$(".select-menu").css("display", "none");

						var inpu = thiss.data("inputtarget");

						$("#" + inpu).attr( "value", thiss.attr("name") ).attr("name", thiss.data("datainfo"));
						$("#" + inpu).trigger("keyup");
						
						$(".select-menu").css("display", "none");
					break;

					case "show-filter":

						$(".filter-menu").css("display", "none");

						var menu = $( thiss ).parent().find( $(".filter-menu") );

						menu.toggle(0);
					break;

					default:
						return true;
					break;

				}
			});
		}
	}

	$(".user-forms button").bind("click mouseenter focusin mouseleave focusout", function (eve) {
		acc_button( $(this), eve, $("#main-loading"));
	});

	$(".user-forms").delegate("button", "click mouseenter focusin mouseleave focusout", function (eve) {
		acc_button( $(this), eve, $("#main-loading"));
	});


	$(".fcR").delegate("button", "click mouseenter focusin mouseleave focusout", function (eve) {
		acc_button( $(this), eve, $("#fc-loading") );
	});

	$(".search-result").delegate("button", "click mouseenter", function (eve) {
		acc_button( $(this), eve, "");
	});

	function acc_button (thiss, eve, loading) {
		var type = thiss.attr("class").split(" "),
			target = thiss.parent().parent().parent();
		

		$.each(type, function (e, type) {
			switch(type) {
				case "ss-send":

					if ( eve.type == "click" ) {
						var data = inputData( target );

						clearTimeout(reset);

						reset = setTimeout(function () {

							$.post(url["switch"], { lib: "verify", l: LANG, p: PID, t: thiss.attr("id"), d: data}, function (data) {
								
								loading.css("display", "block");

								setTimeout(function () {
									loading.css("display", "none");

									if ( data.r )
										title_update( data.r );
									
									if ( data.mess ) {
										if ( data.reg != 1 ) {
											$(".fc-message-content").html( data.mess );
											$(".fc-message").css("display", "block");

											hide_message();
										}
									}
										
									if ( data.reg == 1 ) {
										$(".signup-sucess").html( data.mess ).show(0);
									}

									if ( data.on == 1 || data.reg == 1 )
										inputReset( target );

									if ( data.on == 1 ) {
										updateUI();
										online();
										hide_message();
										
										switch(PID) {
											case 9: window.location.href = window.location.pathname; break;
											case 3: window.location.href = "/"; break;
										}
									}

									if ( data.tn )
										$("body").attr("id", data.tn);

								}, 1000);

							}, "json");

						}, 300);
					}
					
					return false;
				break;

				case "input-reset":
					if ( eve.type == "click" )
						input_reset(thiss);
				break;

				case "input-result":
					var result = thiss.parent().find( $(".ir-text") );

					if ( result.length ) {

						switch(eve.type) {
							case "mouseenter":
							case "focusin":
								result.css("display", "block");
								break;

							case "mouseleave":
							case "focusout":
								result.css("display", "none");
								break;
						}
					}
				break;

				case "moreTags":
					
					if ( eve.type == "click" || eve.type == "mouseenter" ) {
						var tags = thiss.parent().find( $(".tagContainer") );

						tags.css("display", "block");
					}
				break;

				case "closeb":

					if ( eve.type == "click" )
						hide_something( thiss.attr("name") );
				break;
			}
		});
	}

	$(".fim-button").bind("click", function (e) {
		var target = $(this).data("target");

		$( target ).toggle(0);

		return false;
	});


	$(".input-reset").bind("click", function (argument) {
		input_reset( $(this) );
	});

	function input_reset (thiss) {
		var input = thiss.parent().find("input");

		input.attr("value", "").trigger("keyup");
		input.parent().find("label").css({"display" : "block", "top" : 0, "left": 0}).attr("class", "labelD");

		thiss.css("display", "none");
	}

	function inputData (target) {
		var inputs = target.find("input");
		
		var obj = {};

		inputs.map( function () {
			obj[this.name] = this.value;
		});

		return obj;
	}

	function inputReset (target) {
		var inputs = target.find("input");
		
		inputs.each( function () {
			$(this).val("");
			$(this).parent().find( $(".ss-icon") ).css("display", "block");
			$(this).parent().find("label").css("display", "block");
			$(this).parent().find( $(".resultbar") ).css("display", "none");
		});
	}

	function check_inputs (target) {
		var inputs = target.find("input");

		inputs.each( function () {
			inputLabel($(this), $(this).parent().find("label"), "");
		});
	}

	function hide_message () {
		setTimeout(function  () {
			$(".fc-message").hide(0);
		}, 8000);
	}

	function online () {
		
		$(".fc-hide").trigger("click");
	}












	$(".fcR").delegate("div", "mouseenter focusin mouseleave focusout mousemove", function (eve) {
		acc_div( $(this), eve );
	});

	$(".user-menu, .user-menu-mini, .search-result").delegate("div", "mouseenter focusin mouseleave focusout", function (eve) {
		var time;

		acc_div( $(this), eve );

	});


	function acc_div (thiss, eve) {
		
		if ( thiss.attr("class") ) {
			var type = thiss.attr("class").split(" "),
				target = thiss.parent().parent().parent();
		}
		
		if ( thiss.attr("class") ) {
			$.each(type, function (e, type) {
				switch(type) {
					case "dH":
						switch(eve.type) {
							case "mouseenter":
							case "mouseleave":
							case "focusin":

								var hide = false,
									focus = thiss.find( $("#sA") );

								if ( !focus.length ) {
									clearTimeout(time);

									switch(eve.type) {
										case "mouseleave":
										case "focusin":
										hide = true; break;
									}

									if ( hide == true ) {
										time = setTimeout(function () {

											thiss.hide(0);

										}, 1000);
									}
								}
								break;
						}
					break;

					case "found-search-result":

						if ( eve.type == "mousemove" ) {
							var pos = thiss.scrollTop();

							if ( pos > 0 )
								thiss.css("box-shadow", "-4px 0 10px -4px #838B93 inset");
							else
								thiss.css("box-shadow", "0 0 0");
						}
						
					break;
				}
			});
		}
		
	}






	var showTimer;

	$(".fc-show").bind("click", function (e) {

		actionEvent( $(this) );

		return false;
	});

	function actionEvent (thiss) {
		var te = $(thiss).attr("name"),
			back = $(window).scrollTop();

		clearTimeout(showTimer);

		showTimer = setTimeout(function () {
			$(".fullscreen").show(0, function () {
				var height = $(".head-content").height();

				$("body, html").scrollTop(0);
				$("#MonaMade").attr("class", back);

				$(".fc-content").css("display", "block").animate({ margin: height + "px 0 0 0" }, 400);
				
			
				

				
				$.post(url["action"], { l: LANG, p: PID, t: te }, function (data) {
					
					setTimeout(function () {
						if ( data.r ) {
							$(".fcR-content").html( data.r );

							$(".fcTemp").css("display", "none");

							$(".fcR").removeClass("dSize").attr("id", "fc-" + data.ws).css("display", "block");


							var firstField = $(".fcR").find("input").first();

							if ( firstField.length > 0 )
								firstField.focus();
							else
								$(".fcR").find( $(".fc-hide") ).focus();

							inputFix();
						}
					}, 400);

				}, "json");


				$(".fullscreen, .fullscreen_message").css({ width: $("body").width(), height: $("body").height() });
			});
		}, 250);
	}

	function inputFix () {
		var inputs = $(".fcR").find("input");

		inputs.each(function (e, d) {

			var label = $(this).parent().find("label");
		});
	}

	function inputLabel (data, label, reset) {
		
		var text = label.text(),
			resultBar = data.parent().find( $(".resultbar") ),
			sett = data.parent().parent().hasClass("settings-ui");

		if ( sett == false ) {
			if ( data.val().length > 0 ) {

				if ( label.hasClass("stay") )
					label.css({ display: "none" });
				else
					label.css({"top": "-45px", "left": "-20px" });

				data.attr("title", text);

				if ( reset.length == 1 ) {
					if ( reset.length == 1 )
						reset.css("display", "block");
					else
						reset.css("display", "none");
				}

				label.addClass("labelV").removeClass("labelD");
			}
			else {

				if ( label.hasClass("stay") )
					label.css({ "display": "block" });
				else
					label.css({"top": "0", "left": "0" });

				if ( reset.length == 1 )
					reset.css("display", "none");

				data.attr("title", "");

				resultBar.css("display", "none");
				data.parent().find( $(".ss-icon") ).css("display", "block");

				label.addClass("labelD").removeClass("labelV");
			}
		}
	}

	$("body").on("click", ".fc-hide, .hT", function () {
		var back = $("#MonaMade").attr("class");

		$(".fullscreen, .fullscreen_message").hide(0);
		$("body, html").scrollTop(back);

		fc_reset();
	});

	$(".fullscreen, .fullscreen_message").bind("click", function (e) {
		var target = e.target;

		if ( $(target).hasClass("fullscreen") ||  $(target).hasClass("fullscreen_message") || $(target).hasClass("fc-content") )
			$(this).find( $(".fc-hide") ).trigger("click");
	});

	function fc_reset () {
		$(".fc-content").css("marginTop", "20px");

		$(".fcTemp").css("display", "block");
		$(".fcR").addClass("dSize").removeAttr("id").css("display", "none");

		$(".utools-loading").css("display", "none");
	}

	

	$(".hideThing, .closeb").bind("click", function (argument) {
		hide_something( $(this).attr("name") );
	});

	function hide_something (target) {
		$(target).hide(0);
	}

	// update each date

	var updatetime;
	var updateEvery = 60000; //every 1 minute (60000) ms


	// IP PAGE

	$(".ip-imgList .ipImg a").bind("click", function () {
		var img = $(this).find("img"),
			target = $(".ip-introImg img"),
			all = $(this).parent().find( $(".ipImg") ),
			slider = $(".introImgSlider"),
			last = all.length - 1;

		$("#imgA").attr("id", "");

		$(this).parent().attr("id", "imgA");

		target.attr("src", img.attr("src"));
		target.attr("class", img.attr("class"));

		return false;
	});


	$(".ImgSlider").bind("click", function () {
		var actual = $("#imgA").index(),
			all = $(".ip-imgList .ipImg").length - 1;

		$.each( $(this).attr("class").split(" "), function (e, type) {

			switch( type ) {
				case "is-left":
					var nextt = ( all < actual - 1 ) ? all : actual - 1,
						next = $(".ip-imgList .ipImg").get( nextt ),
						nextImg = $(next).find("img");
					break;

				case "is-right":
					var nextt = ( all < actual + 1 ) ? 0 : actual + 1,
						next = $(".ip-imgList .ipImg").get( nextt ),
						nextImg = $(next).find("img");
					break;
			}

			if ( nextImg ) {
				$(".ip-introImg img").attr("src", nextImg.attr("src"));
				$("#imgA").attr("id", "");
				$(next).attr("id", "imgA");
			}
		});

		return false;
	});


	$(".ipTabMenu a, .tabs a").bind("click", function () {
		var target = $(this).data("target");

		$("#ipTabA, #ipA").attr("id", "");

		$(this).attr("id", "ipTabA");
		$("." + target).attr("id", "ipA");


		return false;
	});

	$(".filters button").bind("click", function () {
		var target = $(this).parent().find( $(".filter-content") ),
			parent = $(this).parent();

		target.toggle(0);

		if ( target.css("display") == "none")
			parent.attr("class", "filter fU");
		else if ( target.css("display") == "block")
			parent.attr("class", "filter fD");
		return false;
	});


	$(".data-basket, .basket-data").on("click", ".qChange, .deleteItem", function (eve) {
		
		if ( $(this).hasClass("qChange") ) {
			
			var target = $(this).data("target"),
				max = $(this).parent().data("maxquantity"),
				actualVal = $("#" + target).attr("value") <= max ? $("#" + target).attr("value") : max,
				newValue = 0;

			$("#" + target).attr("value", actualVal ).val( actualVal );
			
			$.each( $(this).attr("class").split(" "), function (e, type) {

				switch( type ) {
					case "qUp":
						if ( actualVal >= max )
							newValue = Number(max);
						else
							newValue = Number(actualVal) + 1;
						break;

					case "qDown":
						if ( actualVal <= 1 )
							newValue = 1;
						else
							newValue = Number(actualVal) - 1;
						break;
				}
			});
			
			$("#" + target).attr("value", newValue ).val( newValue );

			if ( $(this).hasClass("qty") ) {
				
				$("#" + target).trigger("change");

				var up = $(this).parent().find( $(".qUp") ),
					down = $(this).parent().find( $(".qDown") ),
					actV = $("#" + target).attr("value");

				$(this).removeClass("bIn");

				if ( $(this).hasClass("qUp")) {
					if ( actV == max ) {
						$(this).addClass("bIn");
					}
					if ( actV > 1 )
						down.removeClass("bIn");
				}
				
				if ( $(this).hasClass("qDown")) {
					if ( actV == 1 )
						$(this).addClass("bIn");
					
					if ( actV < max )
						up.removeClass("bIn");
				}
			}

			$("#" + target).trigger("change");
			
		}

		if ( $(this).hasClass("deleteItem") ) {
			var targ = $(this).data("target");

			$("#" + targ).val("0").trigger("change");
		}

		return false;
	});
	
	$(".basket-data").delegate("input", "change", function (eve) {
		
		$.each( $(this).attr("class").split(" "), function (e, type) {

			switch( type ) {
				case "ba-qty":
					var data = inputData( $(eve.target).parent() );

						just_do_it( $(eve.target), eve, data );
					break;
			}
		});

		return false;
	});

	$(document).on("focusin focusout keyup input", ".inpbox input", function (eve) {
		
		$.each( $(this).attr("class").split(" "), function (e, type) {

			switch( type ) {
				case "defInp":
					var	thiss = $(eve.target),
						pa = thiss.parent();

					switch(eve.type) {
						case "focusin":
							pa.attr("id", "inpAA");
							break;

						case "focusout":
							pa.removeAttr("id");
							break;
						
					}

					break;
			}
		});
		
		//return false;
	});

	$(".basket-data input").on("focusin focusout keyup input", function (eve) {
		
		$.each( $(this).attr("class").split(" "), function (e, type) {

			switch( type ) {
				case "baInp":
					var	thiss = $(eve.target),
						pa = thiss.parent(),
						val = thiss.val();

					switch(eve.type) {

						case "keyup":
						case "input":
							var DATA = inputData( thiss.parent() ),
								type = thiss.attr("id"),
								error_icon = thiss.parent().find( $(".baStatusIcon") ),
								error_text = thiss.parent().find( $(".baStatus") );

							//console.log( $(tab).find( $(".baOK") ) );

							if ( val.length >= 1 ) {

								clearTimeout(reset);

								reset = setTimeout(function () {
									$.post(url["switch"], { lib: "basket", t: type, d: DATA }, function (data) {
										
										if ( data.icon )
											error_icon.html( data.icon );

										if ( data.error ) {
											
											if ( data.error == "false" ) {
												thiss.parent().removeClass("inpBAD");
												error_text.hide().find("label").html("");
											} else {
												thiss.parent().attr("class", "inpbox inpBAD");
												title_update( data.error );
												error_text.show().find("label").html( data.error );
											}
											
										}

										var tab = thiss.closest("form").get(),
											checktab = $(tab).find( $(".baOK") );

										if ( checktab.length > 0 ) {
											$("." + $(tab).attr("name") + "tm").html('<i class="ii iCheck"></i>');
										} else {
											$("." + $(tab).attr("name") + "tm").html('<i class="ii iUncheck"></i>');
										}

									}, "json");
								}, 200);
							} else {

								error_icon.html("");
								error_text.hide().find("label").html("");
								thiss.parent().removeClass("inpBAD");
							}


							break;
					}

					var tab = thiss.closest("form").get(),
						checktab = $(tab).find( $(".baOK") );

					if ( checktab.length > 0 ) {
						$("." + $(tab).attr("name") + "tm").html('<i class="ii iCheck"></i>');
					} else {
						$("." + $(tab).attr("name") + "tm").html('<i class="ii iUncheck"></i>');
					}
					break;
			}
		});

		return false;
	});




	if ( $(".about-left").length ) {
		var $sidebar   = $(".about-left"), 
			$window    = $(window),
			offset     = $sidebar.offset(),
			topPadding = 150; //120


		$window.ready(function() {
			about_pos();
		});

		$window.scroll(function() {
			about_pos();
		});
		
		

		function about_pos() {		
			
			if (($window.scrollTop() + $("#fixedHeader-line").height() + 10 > offset.top ) && document.documentElement.clientWidth >= 768) {

				if ( ( $window.scrollTop() + $(".about-left").height() ) <= $(".about-right").height() ) {
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
	


	$('body').on('click', '.select-switcher', function () {
		var target = $(this).data("target");

		if ( $(this).hasClass("showw") )
			$(".selectdata").hide(0);

		$( target ).toggle(0);

		return false;
	});

	$("body").on('click', '.option', function () {
		var parent = $(this).parent(),
			target = parent.data("target");

		parent.hide(0);
		$( target ).val( $(this).html() );
		$( target ).trigger("change");

		return false;
	});

	$("body").on('click', function () {
		if ( $(".selectdata") )
			$(".selectdata").hide(0);
	});

	// IP PAGE
	
	// UI ORDERS PAGE

		$(".order-info .o-action").on("click", function (e) {
			var parent = $(this).parent().parent(),
				target = parent.find( $(".order-details") );

			target.toggle(0, function (e) {
				if ( target.css("display") == "block" ) {
					parent.attr("class", "order sw_d");
				}
				else
					parent.attr("class", "order");
			});

			return false;
		});


		$(".toggleButton").bind("click", function (e) {
			var target = $(this).data("target");

			

			if ( $(this).hasClass("show") ) {
				$(".toggleButton.show").hide(0);
				$(target).toggle(0);
			}
			else if ( $(this).hasClass("hide") ) {
				var toggle = $(this).data("toggle");
				
				$(target).hide()
				$(toggle).show(0);
			}

			return false;
		});

		$(".uiCircle").bind("click", function (e) {
			var target = $(this).data("target");

			$(".uicA").removeClass("uicA");

			$(this).addClass("uicA")
			$(target).addClass("uicA");

			return false;
		});
		 
	// UI ORDERS PAGE



	$('input[type="number"]').bind("change keyup", function () {
		var min = $(this).attr("min"),
			max = $(this).attr("max"),
			val = $(this).val(),
			final = 0;

		if ( Number(val) >= max ) final = max; else final = val;

		$(this).attr("value", final ).val( final );
	});

	

	$(".sublevel1 li").bind("mouseenter click mouseleave", function (eve) {
		
		switch(eve.type) {
			case "mouseenter":

				$(this).attr("class", " AAsub");
			break;

			case "mouseleave":
				$(this).removeClass("AAsub");
			break;
		}
	});
	

	$(".st").bind("mouseover focusin click mouseout", function (eve) {
		var target = $(this).data("target"),
			sub = $(this).data("sub"),
			show = "",
			clas = $(this).attr("class");

		

			switch(eve.type) {
				case "mouseout":
					$(".AA").removeClass("AA");

					clearTimeout(reset);
					break;

				case "mouseover":
				case "click":
				case "focusin":
					

					reset = setTimeout(function () {
						$.each( $(".subMenu"), function (e) {
							$(this).find("li:first").attr("class", "AAsub");
						});

						if ( sub )
							show = target + ", "+sub;
						else
							show = target;

						var notEmpty = $( sub ).find("li").length;

						$( ".subMenu" ).css("display", "none");

						if ( notEmpty != false ) {
							$( show ).css("display", "block");
							$(this).attr("class", clas + " AA");

						} else {
							$( show ).css("display", "none");
						}
					}, 500);
				break;


				break;
			}
		
	});

	/* ABOUT */

		if ( $("#aboutAA").length > 0 ) {
			$(".scrollv").scrollLeft( $("#aboutAA").offset().left - 15 );
		}
		

		$(".about-tab").bind("click", function () {
			var target = $(this).parent().parent().find( $(".aaco-body") ),
				parent = $(this).parent().parent(),
				icon_d = $(this).find( $(".f-down") ),
				icon_u = $(this).find( $(".f-up") );

			target.toggle(0);

			if ( target.css("display") == "none") {
				parent.attr("class", "aaco aaN");
				icon_d.hide(0);
				icon_u.show(0);
			}
			else if ( target.css("display") == "block") {
				parent.attr("class", "aaco aaB");
				icon_d.show(0);
				icon_u.hide(0);
			}
			return false;
		});

	/* ABOUT */

	function show_fatalError (text) {
		$(".fullscreen_message").css({ width: $("body").width(), height: $("body").height() });

		$(".ferr-message").html( text );
		$("#fullscreen-fatalerror").fadeIn(400);
	}




	var reset;

	
	$(document).on("click", "button.delivery-choice", function (eve) {

		just_do_it( $(this), eve, "" );

		var type = $(this).parent().parent().data("type");

		$("#" + type).removeAttr("id");

		$(this).parent().attr("id", type);


		if ( type == "delivery-type" ) {
			var payment = $(this).data("payment");

			update_payments( payment );

			$(".secondChoice").children().on('click');
			$(".choiceDis").removeClass("choiceDis");
			$(".secondChoice").find("button").removeAttr("disabled");
		}


		return false;
	});

	function update_payments(data) {

		$("#delivery-payment").removeAttr("id");

		if ( data + ':contains("#")')
			var d = data.split("#");
		else
			var d = data;

		
		$(".pays").hide(0);

		$.each( d, function( key, value ) {
			$(".payment-" + value).show(0);
		});
	}



	$(".jdi").on("click", function (eve) {
		just_do_it( $(this), eve, "" );

		return false;
	});

	$(".addi-button").bind("click", function (eve) {
		var data = inputData( $(".addItem") );

		if ( $(this).hasClass("addi-button") ) load_icon(".addi-button");
		
		just_do_it( $(this), eve, data );

		return false;
	});

	$(".data-basket").delegate("a", "click", function (eve) {
		
		if ( $(this).hasClass("addi-button") ) {
			var data = inputData( $(".addItem") );

			load_icon(".addi-button");

			just_do_it( $(this), eve, data );
		}

		return false;
	});




	//IMG ZOOM

	
	$(document).on("click", ".jdi2", function (e) {
		var target = $( this ).data("target")
			cont = $(target).find( $(".imgzcont") );

		cont.css( "margin-top", 0 );

		$(target).show(0, function(e) {
			cont.animate({ marginTop: $('.head-content').height() + "px" }, 200);
		});

		switch(target) {
			case '#imgzoom':
				

				$("#imgzoom").css({ width: $("body").width(), height: $("body").height() });

				var galery = $( $(this).data("info") ).find("img");

				$(".imgz-big").html("");
				$(".imgz-list").html("");
				
				galery.each(function (i, ee) {
					var parent = $( ee ).parent().parent().attr("id");

					var rawimg = '<img src="' + $(ee).attr("src") + '" class="' + $(ee).attr("class") + '">';
					var rawimg2 = '<img src="' + $(ee).attr("src") + '">';

					if ( parent == 'imgA' )
						var img = '<a href="#" id="imggA">' + rawimg + '</a>';
					else
						var img = '<a href="#">' + rawimg + '</a>';

					if ( i != 0 ) {
						if ( parent == 'imgA' ) {
							$('.imgz-big').append( rawimg2 );

							if ( $(ee).hasClass("imgvertical") ) {
								$(".imgz-big").attr("class", "imgz-big imgv");
							}
							else {
								$(".imgz-big").attr("class", "imgz-big imgh");
							}
						}

						$('.imgz-list').append( '<div class="imgz">' + img + '</div>' );
					}
					
				});

				break;
		}

		return false;
	});

	$(document).on("click touchend", ".aoe", function (e) {

		if ( $(e.target).hasClass("aoe") || $(e.target).hasClass("imgz-big") )
			$(this).hide(0);
	});

	$(document).on("click touch", ".imgz-list .zoompls", function (e) {
		var rawimg = '<img src="' + $(this).attr("src") + '">';
		var parent = $("#imgzoom");

		parent.find( $("#imggA") ).removeAttr("id");

		$(this).parent().attr("id", "imggA");

		$(".imgz-big").html( rawimg );

		if ( $(this).hasClass("imgvertical") )
			$(".imgz-big").attr("class", "imgz-big imgv");
		else
			$(".imgz-big").attr("class", "imgz-big imgh");

		return false;
	});

 	$(document).on("mouseenter mouseleave mousemove mousedown mouseup touchstart touchmove touchend", "div.imgz-big", function (e) {

 		var w = responsive();

		var zoom = $( this );
		var smallImg = $(".imgz-big img");

		var zoomin = $(".zoomed");
		var bgImg = "url(" + smallImg.attr("src") + ")";


		var leftInitial = smallImg.offset().left - zoom.offset().left - zoomin.width() / 2;
		var topInitial = smallImg.offset().top - zoom.offset().top - zoomin.height() / 2;

		var imgWW = smallImg.width();
		var imgHH = smallImg.height();

		zoomin.css({ 
			backgroundImage: bgImg,
			backgroundRepeat: "no-repeat",
			backgroundPosition: "0px 0px",
			left: leftInitial,
			top: topInitial
		});

		if ( !$(this).find( $(".zoomed") ).length ) zoom.append("<div class='zoomed'></div>");

		switch(e.type) {
			
			case 'touchstart':
			case 'touchmove':
				var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];

				var	Pleft  	= touch.pageX - zoom.offset().left-zoomin.width()/2;
				var	Ptop   	= touch.pageY - zoom.offset().top-zoomin.height()/2;
				var	left   	= touch.pageX - smallImg.offset().left;
				var	top    	= touch.pageY - smallImg.offset().top;
				break;

			case 'mousemove':
			case 'mousedown':
				var   Pleft  = e.pageX - zoom.offset().left-zoomin.width()/2;
				var   Ptop   = e.pageY - zoom.offset().top-zoomin.height()/2;
				var   left   = e.pageX - smallImg.offset().left;
				var   top    = e.pageY - smallImg.offset().top;
				break;

			/*case 'mouseup':*/
			case 'touchend':
			case 'mouseleave':
			//case 'mouseout':
				zoomin.hide();
				break;
		}

		if ( e.type == 'touchstart' ||  e.type == 'touchmove' ||  e.type == 'mousemove' ||  e.type == 'mousedown' ) {
			var newImage = new Image();
				newImage.src = smallImg.attr("src");
				
			var Wrate	= (newImage.width - zoomin.width()) / smallImg.width();
			var Hrate	= (newImage.height - zoomin.height()) / smallImg.height();

			var	bgLeft 	= left * Wrate;
			var	bgTop  	= top * Hrate;

			var	imgW   	= smallImg.width(); 
			var	imgH   	= smallImg.height();

			if ( left >= 0 & left <= imgW & top >= 0 & top <= imgH ) {
				zoomin.show().css({
					left: Pleft,
					top: e.type == 'touchstart' ||  e.type == 'touchmove' ? Ptop - zoomin.height() - 20 : Ptop,
					backgroundPosition: "-" + bgLeft + "px -" + bgTop +"px",
				});
			} else {
				zoomin.hide();
			}
		}

		if ( left <= 0 || left >= imgW || top <= 0 || top >= imgH ){
			zoomin.hide();
		}
	});

	//IMG ZOOM


	function just_do_it (thiss, eve, data) {
		var lib =  thiss.data("lib"),
			type = thiss.data("event"),
			DATA = data.length != 0 ? data : thiss.data("data");


		clearTimeout(reset);

		reset = setTimeout(function () {
			$.post(url["switch"], { lib: lib, t: type, d: DATA }, function (data) {
				
				if ( data.reset ) {
					$(".itemHistory").fadeOut(400);
				}

				if ( data.basket )
					$(".user-cart").html( data.basket );

				if ( data.addbutton )
					$(".data-basket").html( data.addbutton );



				if ( data.cartsummary )
					$(".cart-summ").html( data.cartsummary );

				if ( data.cartlist )
					$("#basketItems").html( data.cartlist );

			}, "json");
		}, 250);
	}

	function load_icon(target) {
		$( target ).html('<i class="fa fa-cog fa-spin fa-fw"></i>');
	}

	var default_title = document.title;

	function title_update (title) {

		document.title = "▶ " + title;

		setTimeout(function () {
			document.title = default_title;
		}, 5000);
	}

});