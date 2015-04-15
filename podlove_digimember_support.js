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

	$(".change_subscription").on("click", function(e) {
		var data = $(this).data(),
			ajaxurl = data.ajaxurl,
			purchaseid = data.purchaseid,
			action = data.action,
			info = infobox("#subscription-infobox"),
			error = infobox("#subscription-errorbox"),
			status_row = $(this).closest("tr")
			spinner = status_row.find("i.x-icon-spinner");

		e.preventDefault();
		spinner.removeClass("hidden");

		$.ajax({
			url: ajaxurl,
			method: "POST",
			data: {
				action: "podlove-digimember-" + action + "-subscription",
				purchaseid: purchaseid
			}
		}).done(function(result) {
			info.setTitle(result.billing_status_msg);
			info.setContent(result.note);
			info.show();

			if (result.modified.toUpperCase() === 'Y') {
				status_row.find(".billing_status").text(result.billing_status_msg);
				status_row.find(".billing_modify").addClass('hidden');
			}

		}).fail(function() {
			error.show();
		}).always(function() {
			spinner.addClass("hidden");
		})

	});

})(jQuery);