(function(e) {
	"use strict";
	function t() {
		var t = e(window).height() - e("body > .header").height()
	}
	function n() {
		e(".sidebar").slimscroll({
			color : "rgba(0,0,0,0.5)",
			height : e(window).height() - e("header").height() + "px"
		})
	}
	e(".sidebar-toggle").click(function() {
		e("body").toggleClass("sidebar-sm");
		e(".leftside, header .logo").toggleClass("display-inline");
		return false
	});
	e(".dropdown").on("show.bs.dropdown", function() {
		e(this).find(".dropdown-menu").addClass("animated flipInY")
	});
	e(".dropdown").on("hide.bs.dropdown", function() {
		e(this).find(".dropdown-menu").removeClass("animated flipInY")
	});
	e("[data-toggle='tooltip']").tooltip();
	e(".navbar .dropdown-menu ul").slimscroll({
		alwaysVisible : false,
		size : "3px",
		height : "210px"
	}).css("width", "100%");
	e(".collapse-box").click(function(t) {
		t.preventDefault();
		var n = e(this).parent().parent().next(".box-body");
		if (n.is(":visible")) {
			e(this).children("i").removeClass("fa-chevron-up");
			e(this).children("i").addClass("fa-chevron-down")
		} else {
			e(this).children("i").removeClass("fa-chevron-down");
			e(this).children("i").addClass("fa-chevron-up")
		}
		n.slideToggle("slow")
	});
	e(".remove-box").click(function() {
		var t = e(this).parents(".box").first();
		t.slideUp()
	});
	e(".wrapper").resize(function() {
		t();
		n()
	});
	e.fn.sub = function() {
		return this.each(function() {
			var t = e(this).children("a").first();
			var n = e(this).children(".sub-menu").first();
			var r = e(this).hasClass("active");
			if (r) {
				n.show();
				t.children(".fa-angle-right").first().removeClass(
						"fa-angle-right").addClass("fa-angle-down")
			}
			t.click(function(e) {
				e.preventDefault();
				if (r) {
					n.slideUp(200);
					r = false;
					t.children(".fa-angle-down").first().removeClass(
							"fa-angle-down").addClass("fa-angle-right");
					t.parent("li").removeClass("active")
				} else {
					n.slideDown(200);
					r = true;
					t.children(".fa-angle-right").first().removeClass(
							"fa-angle-right").addClass("fa-angle-down");
					t.parent("li").addClass("active")
				}
			})
		})
	}
})(jQuery);

$(".sidebar .sub-nav").sub()