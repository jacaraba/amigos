<?php
	$rdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $rdata)));
	$jdata = array_map('to_utf8', array_map('safe_html', array_map('html_attr_tags_ok', $jdata)));
?>
<script>
	$j(function() {
		var tn = 'lideres';

		/* data for selected record, or defaults if none is selected */
		var data = {
			PUESTO: <?php echo json_encode(['id' => $rdata['PUESTO'], 'value' => $rdata['PUESTO'], 'text' => $jdata['PUESTO']]); ?>
		};

		/* initialize or continue using AppGini.cache for the current table */
		AppGini.cache = AppGini.cache || {};
		AppGini.cache[tn] = AppGini.cache[tn] || AppGini.ajaxCache();
		var cache = AppGini.cache[tn];

		/* saved value for PUESTO */
		cache.addCheck(function(u, d) {
			if(u != 'ajax_combo.php') return false;
			if(d.t == tn && d.f == 'PUESTO' && d.id == data.PUESTO.id)
				return { results: [ data.PUESTO ], more: false, elapsed: 0.01 };
			return false;
		});

		cache.start();
	});
</script>

