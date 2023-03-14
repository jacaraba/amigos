<?php

// Data functions (insert, update, delete, form) for table amigos

// This script and data application were generated by AppGini 22.14
// Download AppGini for free from https://bigprof.com/appgini/download/

function amigos_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('amigos');
	if(!$arrPerm['insert']) return false;

	$data = [
		'LIDER' => Request::val('LIDER', '1111111111'),
		'CEDULA' => Request::val('CEDULA', ''),
		'NOMBRE' => Request::val('NOMBRE', ''),
		'PUESTO' => Request::val('PUESTO', ''),
		'NOMPUESTO' => Request::val('NOMPUESTO', ''),
		'MESA' => Request::val('MESA', ''),
		'CELULAR' => Request::val('CELULAR', ''),
		'DIRECCION' => Request::val('DIRECCION', ''),
	];


	// hook: amigos_before_insert
	if(function_exists('amigos_before_insert')) {
		$args = [];
		if(!amigos_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('amigos', backtick_keys_once($data), $error);
	if($error) {
		$error_message = $error;
		return false;
	}

	$recID = db_insert_id(db_link());

	update_calc_fields('amigos', $recID, calculated_fields()['amigos']);

	// hook: amigos_after_insert
	if(function_exists('amigos_after_insert')) {
		$res = sql("SELECT * FROM `amigos` WHERE `LLAVE`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args = [];
		if(!amigos_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	set_record_owner('amigos', $recID, getLoggedMemberID());

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID'))) amigos_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function amigos_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function amigos_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('amigos', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: amigos_before_delete
	if(function_exists('amigos_before_delete')) {
		$args = [];
		if(!amigos_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	sql("DELETE FROM `amigos` WHERE `LLAVE`='{$selected_id}'", $eo);

	// hook: amigos_after_delete
	if(function_exists('amigos_after_delete')) {
		$args = [];
		amigos_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='amigos' AND `pkValue`='{$selected_id}'", $eo);
}

function amigos_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('amigos', $selected_id, 'edit')) return false;

	$data = [
		'LIDER' => Request::val('LIDER', ''),
		'CEDULA' => Request::val('CEDULA', ''),
		'NOMBRE' => Request::val('NOMBRE', ''),
		'PUESTO' => Request::val('PUESTO', ''),
		'NOMPUESTO' => Request::val('NOMPUESTO', ''),
		'MESA' => Request::val('MESA', ''),
		'CELULAR' => Request::val('CELULAR', ''),
		'DIRECCION' => Request::val('DIRECCION', ''),
	];

	// get existing values
	$old_data = getRecord('amigos', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: amigos_before_update
	if(function_exists('amigos_before_update')) {
		$args = ['old_data' => $old_data];
		if(!amigos_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'amigos', 
		backtick_keys_once($set), 
		['`LLAVE`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="amigos_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('amigos', $data['selectedID'], calculated_fields()['amigos']);

	// hook: amigos_after_update
	if(function_exists('amigos_after_update')) {
		$res = sql("SELECT * FROM `amigos` WHERE `LLAVE`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['LLAVE'];
		$args = ['old_data' => $old_data];
		if(!amigos_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update ownership data
	sql("UPDATE `membership_userrecords` SET `dateUpdated`='" . time() . "' WHERE `tableName`='amigos' AND `pkValue`='" . makeSafe($selected_id) . "'", $eo);
}

function amigos_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $separateDV = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = null;
	$row = $urow = $jsReadOnly = $jsEditable = $lookups = null;

	$noSaveAsCopy = false;

	// mm: get table permissions
	$arrPerm = getTablePermissions('amigos');
	if(!$arrPerm['insert'] && $selected_id == '')
		// no insert permission and no record selected
		// so show access denied error unless TVDV
		return $separateDV ? $Translation['tableAccessDenied'] : '';
	$AllowInsert = ($arrPerm['insert'] ? true : false);
	// print preview?
	$dvprint = false;
	if(strlen($selected_id) && Request::val('dvprint_x') != '') {
		$dvprint = true;
	}


	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: ESLIDER
	$combo_ESLIDER = new Combo;
	$combo_ESLIDER->ListType = 0;
	$combo_ESLIDER->MultipleSeparator = ', ';
	$combo_ESLIDER->ListBoxHeight = 10;
	$combo_ESLIDER->RadiosPerLine = 1;
	if(is_file(__DIR__ . '/hooks/amigos.ESLIDER.csv')) {
		$ESLIDER_data = addslashes(implode('', @file(__DIR__ . '/hooks/amigos.ESLIDER.csv')));
		$combo_ESLIDER->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions($ESLIDER_data))));
		$combo_ESLIDER->ListData = $combo_ESLIDER->ListItem;
	} else {
		$combo_ESLIDER->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions("LIDER;;VOTANTE"))));
		$combo_ESLIDER->ListData = $combo_ESLIDER->ListItem;
	}
	$combo_ESLIDER->SelectName = 'ESLIDER';
	// combobox: ESTADO
	$combo_ESTADO = new Combo;
	$combo_ESTADO->ListType = 0;
	$combo_ESTADO->MultipleSeparator = ', ';
	$combo_ESTADO->ListBoxHeight = 10;
	$combo_ESTADO->RadiosPerLine = 1;
	if(is_file(__DIR__ . '/hooks/amigos.ESTADO.csv')) {
		$ESTADO_data = addslashes(implode('', @file(__DIR__ . '/hooks/amigos.ESTADO.csv')));
		$combo_ESTADO->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions($ESTADO_data))));
		$combo_ESTADO->ListData = $combo_ESTADO->ListItem;
	} else {
		$combo_ESTADO->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions("INGRESADO;;VERIFICADO;;CONFIRMADO"))));
		$combo_ESTADO->ListData = $combo_ESTADO->ListItem;
	}
	$combo_ESTADO->SelectName = 'ESTADO';

	if($selected_id) {
		// mm: check member permissions
		if(!$arrPerm['view']) return $Translation['tableAccessDenied'];

		// mm: who is the owner?
		$ownerGroupID = sqlValue("SELECT `groupID` FROM `membership_userrecords` WHERE `tableName`='amigos' AND `pkValue`='" . makeSafe($selected_id) . "'");
		$ownerMemberID = sqlValue("SELECT LCASE(`memberID`) FROM `membership_userrecords` WHERE `tableName`='amigos' AND `pkValue`='" . makeSafe($selected_id) . "'");

		if($arrPerm['view'] == 1 && getLoggedMemberID() != $ownerMemberID) return $Translation['tableAccessDenied'];
		if($arrPerm['view'] == 2 && getLoggedGroupID() != $ownerGroupID) return $Translation['tableAccessDenied'];

		// can edit?
		$AllowUpdate = 0;
		if(($arrPerm['edit'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['edit'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['edit'] == 3) {
			$AllowUpdate = 1;
		}

		$res = sql("SELECT * FROM `amigos` WHERE `LLAVE`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'amigos_view.php', false);
		}
		$combo_ESLIDER->SelectedData = $row['ESLIDER'];
		$combo_ESTADO->SelectedData = $row['ESTADO'];
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
		$combo_ESLIDER->SelectedText = (isset($filterField[1]) && $filterField[1] == '2' && $filterOperator[1] == '<=>' ? $filterValue[1] : 'VOTANTE');
		$combo_ESTADO->SelectedText = (isset($filterField[1]) && $filterField[1] == '13' && $filterOperator[1] == '<=>' ? $filterValue[1] : 'INGRESADO');
	}
	$combo_ESLIDER->Render();
	$combo_ESTADO->Render();

	ob_start();
	?>

	<script>
		// initial lookup values

		jQuery(function() {
			setTimeout(function() {
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_clean());


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/amigos_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/amigos_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'DATOS BASICOS DEL AMIGO', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert) {
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return amigos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return amigos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if(Request::val('Embedded')) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id) {
		if(!Request::val('Embedded')) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate) {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return amigos_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		}
		if(($arrPerm['delete'] == 1 && $ownerMemberID == getLoggedMemberID()) || ($arrPerm['delete'] == 2 && $ownerGroupID == getLoggedGroupID()) || $arrPerm['delete'] == 3) { // allow delete?
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		} else {
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);
		}
		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);

		// if not in embedded mode and user has insert only but no view/update/delete,
		// remove 'back' button
		if(
			$arrPerm['insert']
			&& !$arrPerm['update'] && !$arrPerm['delete'] && !$arrPerm['view']
			&& !Request::val('Embedded')
		)
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
		elseif($separateDV)
			$templateCode = str_replace(
				'<%%DESELECT_BUTTON%%>', 
				'<button
					type="submit" 
					class="btn btn-default" 
					id="deselect" 
					name="deselect_x" 
					value="1" 
					onclick="' . $backAction . '" 
					title="' . html_attr($Translation['Back']) . '">
						<i class="glyphicon glyphicon-chevron-left"></i> ' .
						$Translation['Back'] .
				'</button>',
				$templateCode
			);
		else
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly = '';
		$jsReadOnly .= "\tjQuery('#LIDER').replaceWith('<div class=\"form-control-static\" id=\"LIDER\">' + (jQuery('#LIDER').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#CEDULA').replaceWith('<div class=\"form-control-static\" id=\"CEDULA\">' + (jQuery('#CEDULA').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#NOMBRE').replaceWith('<div class=\"form-control-static\" id=\"NOMBRE\">' + (jQuery('#NOMBRE').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#PUESTO').replaceWith('<div class=\"form-control-static\" id=\"PUESTO\">' + (jQuery('#PUESTO').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#NOMPUESTO').replaceWith('<div class=\"form-control-static\" id=\"NOMPUESTO\">' + (jQuery('#NOMPUESTO').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#MESA').replaceWith('<div class=\"form-control-static\" id=\"MESA\">' + (jQuery('#MESA').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#CELULAR').replaceWith('<div class=\"form-control-static\" id=\"CELULAR\">' + (jQuery('#CELULAR').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#DIRECCION').replaceWith('<div class=\"form-control-static\" id=\"DIRECCION\">' + (jQuery('#DIRECCION').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif($AllowInsert) {
		$jsEditable = "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(ESLIDER)%%>', $combo_ESLIDER->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(ESLIDER)%%>', $combo_ESLIDER->SelectedData, $templateCode);
	$templateCode = str_replace('<%%COMBO(ESTADO)%%>', $combo_ESTADO->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(ESTADO)%%>', $combo_ESTADO->SelectedData, $templateCode);

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = [];
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] /* && !Request::val('Embedded')*/) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-default add_new_parent" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus text-success"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(LLAVE)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(ESLIDER)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(LIDER)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(CEDULA)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(NOMBRE)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(PUESTO)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(NOMPUESTO)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(MESA)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(CELULAR)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(DIRECCION)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(CORREO)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(OBSERVACIONES)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(ESTADO)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(LLAVE)%%>', safe_html($urow['LLAVE']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(LLAVE)%%>', html_attr($row['LLAVE']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(LLAVE)%%>', urlencode($urow['LLAVE']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ESLIDER)%%>', safe_html($urow['ESLIDER']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ESLIDER)%%>', html_attr($row['ESLIDER']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ESLIDER)%%>', urlencode($urow['ESLIDER']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(LIDER)%%>', safe_html($urow['LIDER']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(LIDER)%%>', html_attr($row['LIDER']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(LIDER)%%>', urlencode($urow['LIDER']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(CEDULA)%%>', safe_html($urow['CEDULA']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(CEDULA)%%>', html_attr($row['CEDULA']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CEDULA)%%>', urlencode($urow['CEDULA']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(NOMBRE)%%>', safe_html($urow['NOMBRE']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(NOMBRE)%%>', html_attr($row['NOMBRE']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(NOMBRE)%%>', urlencode($urow['NOMBRE']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(PUESTO)%%>', safe_html($urow['PUESTO']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(PUESTO)%%>', html_attr($row['PUESTO']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(PUESTO)%%>', urlencode($urow['PUESTO']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(NOMPUESTO)%%>', safe_html($urow['NOMPUESTO']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(NOMPUESTO)%%>', html_attr($row['NOMPUESTO']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(NOMPUESTO)%%>', urlencode($urow['NOMPUESTO']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(MESA)%%>', safe_html($urow['MESA']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(MESA)%%>', html_attr($row['MESA']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(MESA)%%>', urlencode($urow['MESA']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(CELULAR)%%>', safe_html($urow['CELULAR']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(CELULAR)%%>', html_attr($row['CELULAR']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CELULAR)%%>', urlencode($urow['CELULAR']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(DIRECCION)%%>', safe_html($urow['DIRECCION']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(DIRECCION)%%>', html_attr($row['DIRECCION']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(DIRECCION)%%>', urlencode($urow['DIRECCION']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(CORREO)%%>', safe_html($urow['CORREO']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(CORREO)%%>', html_attr($row['CORREO']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CORREO)%%>', urlencode($urow['CORREO']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(OBSERVACIONES)%%>', safe_html($urow['OBSERVACIONES']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(OBSERVACIONES)%%>', html_attr($row['OBSERVACIONES']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(OBSERVACIONES)%%>', urlencode($urow['OBSERVACIONES']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(ESTADO)%%>', safe_html($urow['ESTADO']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(ESTADO)%%>', html_attr($row['ESTADO']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ESTADO)%%>', urlencode($urow['ESTADO']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(LLAVE)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(LLAVE)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(ESLIDER)%%>', 'VOTANTE', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ESLIDER)%%>', urlencode('VOTANTE'), $templateCode);
		$templateCode = str_replace('<%%VALUE(LIDER)%%>', '1111111111', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(LIDER)%%>', urlencode('1111111111'), $templateCode);
		$templateCode = str_replace('<%%VALUE(CEDULA)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CEDULA)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(NOMBRE)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(NOMBRE)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(PUESTO)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(PUESTO)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(NOMPUESTO)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(NOMPUESTO)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(MESA)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(MESA)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(CELULAR)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CELULAR)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(DIRECCION)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(DIRECCION)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(CORREO)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(CORREO)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(OBSERVACIONES)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(OBSERVACIONES)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(ESTADO)%%>', 'INGRESADO', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(ESTADO)%%>', urlencode('INGRESADO'), $templateCode);
	}

	// process translations
	$templateCode = parseTemplate($templateCode);

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if(Request::val('dvprint_x') == '') {
		$templateCode .= "\n\n<script>\$j(function() {\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption) {
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id) {
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields
	$filterField = Request::val('FilterField');
	$filterOperator = Request::val('FilterOperator');
	$filterValue = Request::val('FilterValue');

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('amigos');
	if($selected_id) {
		$jdata = get_joined_record('amigos', $selected_id);
		if($jdata === false) $jdata = get_defaults('amigos');
		$rdata = $row;
	}
	$templateCode .= loadView('amigos-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: amigos_dv
	if(function_exists('amigos_dv')) {
		$args = [];
		amigos_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}