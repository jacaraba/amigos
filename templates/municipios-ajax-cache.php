<?php
	$rdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'municipios';

		/* data for selected record, or defaults if none is selected */
		var data = {
			dd: <?php echo json_encode(['id' => $rdata['dd'], 'value' => $rdata['dd'], 'text' => $jdata['dd']]); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for dd */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'dd' && d.id == data.dd.id)
				return { results: [ data.dd ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

