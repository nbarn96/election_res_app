// AJAX-based retrieval function
$(document).ready(function() {
	$("#date").change(function() {
		var value = this.value;
		if (value == "") {
			$(".search-result").hide();
		} else {
			$.ajax({
				type: 'POST',
				url: 'assets/scripts-home.php',
				data: { 'query': value },
				success: function(html) {
					$(".search-result").show();
					$("#results-box").html(html).show();
				}
			});
		}
	});
});