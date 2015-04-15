(function($) {

	var infobox = function(selector) {

		var box = $(selector);

		var hide = function() { box.addClass("hidden"); };
		var show = function() { box.removeClass("hidden"); }

		var setTitle = function(title) {
			$("h6", box).text(title);
		};

		var setContent = function(content) {
			$("p.content", box).text(content);
		};

		box.on("click", ".close", hide);

		return {
			hide: hide,
			show: show,
			setTitle: setTitle,
			setContent: setContent
		}
	};

	$(".resume_subscription").on("click", function(e) {
		var data = $(this).data(),
			ajaxurl = data.ajaxurl,
			purchaseid = data.purchaseid,
			info = infobox("#subscription-infobox"),
			error = infobox("#subscription-errorbox"),
			spinner = $(this).parent().find("i.x-icon-spinner");

		spinner.removeClass("hidden");

		$.ajax({
			url: ajaxurl,
			method: "POST",
			data: {
				action: "podlove-digimember-resume-subscription",
				purchaseid: purchaseid
			}
		}).done(function(result) {
			info.setTitle(result.billing_status_msg);
			info.setContent(result.note);
			info.show();
		}).fail(function() {
			error.show();
		}).always(function() {
			spinner.addClass("hidden");
		})

		e.preventDefault();
	});

})(jQuery);