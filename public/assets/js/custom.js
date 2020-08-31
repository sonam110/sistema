(function($) {
	"use strict";
	$(".menu-c").on('click', function(event){
	    document.cookie = "navUrl="+$(this).attr('href');
	});
	function getCookie(navUrl) {
	  var name = navUrl + "=";
	  var ca = document.cookie.split(';');
	  for(var i = 0; i < ca.length; i++) {
	    var c = ca[i];
	    while (c.charAt(0) == ' ') {
	      c = c.substring(1);
	    }
	    if (c.indexOf(name) == 0) {
	      return c.substring(name.length, c.length);
	    }
	  }
	  return "";
	}
	var isAv = 0;
	$(".app-sidebar a").each(function() {
		var pageUrl = window.location.href.split(/[?#]/)[0];
		var setUrl = getCookie("navUrl");
		if(this.href == pageUrl)
		{
			$(this).addClass("active");
			$(this).parent().addClass("active"); // add active to li of the current link
			$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
			$(this).parent().parent().prev().click(); // click the item to make it drop
			isAv = 1;
		}
		else if (this.href == setUrl)
		{
			if(isAv==0)
			{
				$(this).addClass("active");
				$(this).parent().addClass("active"); // add active to li of the current link
				$(this).parent().parent().prev().addClass("active"); // add active class to an anchor
				$(this).parent().parent().prev().click(); // click the item to make it drop
				isAv = 1;
			}
		}
	});


	var sp = document.querySelector('.search-open');
	var searchbar = document.querySelector('.search-inline');
	var shclose = document.querySelector('.search-close');

	function changeClass() {
		searchbar.classList.add('search-visible');
	}

	/*function closesearch() {
		searchbar.classList.remove('search-visible');
	}
	sp.addEventListener('click', changeClass);
	shclose.addEventListener('click', closesearch);
	var searchField = $('.search');
	var searchInput = $("input[type='search']");
	var checkSearch = function() {
		var contents = searchInput.val();
		if (contents.length !== 0) {
			searchField.addClass('full');
		} else {
			searchField.removeClass('full');
		}
	};
	$("input[type='search']").focus(function() {
		searchField.addClass('isActive');
	}).blur(function() {
		searchField.removeClass('isActive');
		checkSearch();
	});*/
	$(".cover-image").each(function() {
		var attr = $(this).attr('data-image-src');
		if (typeof attr !== typeof undefined && attr !== false) {
			$(this).css('background', 'url(' + attr + ') center center');
		}
	});
	if ($('#ms-menu-trigger')[0]) {
		$('body').on('click', '#ms-menu-trigger', function() {
			$('.ms-menu').toggleClass('toggled');
		});
	}
	// ______________Full screen
	$(document).on("click", "#fullscreen-button", function toggleFullScreen() {
		if ((document.fullScreenElement !== undefined && document.fullScreenElement === null) || (document.msFullscreenElement !== undefined && document.msFullscreenElement === null) || (document.mozFullScreen !== undefined && !document.mozFullScreen) || (document.webkitIsFullScreen !== undefined && !document.webkitIsFullScreen)) {
			if (document.documentElement.requestFullScreen) {
				document.documentElement.requestFullScreen();
			} else if (document.documentElement.mozRequestFullScreen) {
				document.documentElement.mozRequestFullScreen();
			} else if (document.documentElement.webkitRequestFullScreen) {
				document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
			} else if (document.documentElement.msRequestFullscreen) {
				document.documentElement.msRequestFullscreen();
			}
		} else {
			if (document.cancelFullScreen) {
				document.cancelFullScreen();
			} else if (document.mozCancelFullScreen) {
				document.mozCancelFullScreen();
			} else if (document.webkitCancelFullScreen) {
				document.webkitCancelFullScreen();
			} else if (document.msExitFullscreen) {
				document.msExitFullscreen();
			}
		}
	})
	// ______________ PAGE LOADING
	$(window).on("load", function(e) {
		$("#global-loader").fadeOut("slow");
	})
	// ______________ BACK TO TOP BUTTON
	$(window).on("scroll", function(e) {
		if ($(this).scrollTop() > 0) {
			$('#back-to-top').fadeIn('slow');
		} else {
			$('#back-to-top').fadeOut('slow');
		}
	});
	$(document).on("click", "#back-to-top", function(e) {
		$("html, body").animate({
			scrollTop: 0
		}, 600);
		return false;
	});
	var ratingOptions = {
		selectors: {
			starsSelector: '.rating-stars',
			starSelector: '.rating-star',
			starActiveClass: 'is--active',
			starHoverClass: 'is--hover',
			starNoHoverClass: 'is--no-hover',
			targetFormElementSelector: '.rating-value'
		}
	};
	$(".rating-stars").ratingStars(ratingOptions);
	$(".vscroll").mCustomScrollbar();
	$(".imagescroll").mCustomScrollbar({
		axis: "x",
		theme: "dark-3",
		advanced: {
			autoExpandHorizontalScroll: true
		}
	});
	$(".app-sidebar").mCustomScrollbar({
		theme: "minimal",
		autoHideScrollbar: true,
		scrollbarPosition: "outside"
	});
	$(".scroll-1").mCustomScrollbar({
		theme: "dark"
	});
	if ($('.chart-circle').length) {
		$('.chart-circle').each(function() {
			let $this = $(this);
			$this.circleProgress({
				fill: {
					color: $this.attr('data-color')
				},
				size: $this.height(),
				startAngle: -Math.PI / 4 * 2,
				emptyFill: '#f5f5f5',
				lineCap: 'round'
			});
		});
	}
	/** Constant div card */
	const DIV_CARD = 'div.card';
	/** Initialize tooltips */
	$('[data-toggle="tooltip"]').tooltip();
	/** Initialize popovers */
	$('[data-toggle="popover"]').popover({
		html: true,
		trigger:"hover"
	});
	/** Function for remove card */
	$(document).on('click', '[data-toggle="card-remove"]', function(e) {
		let $card = $(this).closest(DIV_CARD);
		$card.remove();
		e.preventDefault();
		return false;
	});
	/** Function for collapse card */
	$(document).on('click', '[data-toggle="card-collapse"]', function(e) {
		let $card = $(this).closest(DIV_CARD);
		$card.toggleClass('card-collapsed');
		e.preventDefault();
		return false;
	});
	$(document).on('click', '[data-toggle="card-fullscreen"]', function(e) {
		let $card = $(this).closest(DIV_CARD);
		$card.toggleClass('card-fullscreen').removeClass('card-collapsed');
		e.preventDefault();
		return false;
	});
})(jQuery);

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#imageThumb').attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function checkAll(source) {
    checkboxes = document.getElementsByName('boxchecked[]');
    for(var i=0, n=checkboxes.length;i<n;i++) {
      checkboxes[i].checked = source.checked;
    }
  }
  function delrec(typ)
  {
    if(typ!="")
    {
      var prod;
      prod=false;
      prod=window.confirm("Está seguro que desea "+ typ +" registros elegidos?")
      if (prod==true)
      {
        var checkedCount = $("input[type=checkbox][name^=boxchecked]:checked").length;
        if (checkedCount == 0) {
          alert ("Tiene que elegir al menos una casilla!");
          return false;
        }
        return true;
        submitbutton('remove')
      }
      else
      {
        return false;
      }
    }
    else
    {
      return false;
    }
  }
