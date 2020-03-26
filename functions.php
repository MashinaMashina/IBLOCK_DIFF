<?php

function get_bitrix_scheme($bitrix_path)
{
	if (preg_match('#^http(s|)://#i', $bitrix_path))
		return get_bitrix_remote_scheme($bitrix_path);
	else
		return get_bitrix_local_scheme($bitrix_path);
}

function bitrix_admin_auth()
{
	global $USER;
	$USER->Authorize(1);
}

function get_bitrix_local_scheme($path)
{
	include_once $path . '/bitrix/modules/main/include/prolog_before.php';
	
	bitrix_admin_auth();
	
	$iblock_list = CIBlock::GetList();
	
	$PROPS = get_bitrix_iblock_props();
	
	$iblocks = [];
	while($IBLOCK = $iblock_list->Fetch())
	{
		filter_bitrix_array($IBLOCK);
		
		$iblock_code = ! empty($IBLOCK['CODE']) ? $IBLOCK['CODE'] : $IBLOCK['ID'];
		
		$iblocks[$IBLOCK['IBLOCK_TYPE_ID']][$iblock_code] = $IBLOCK;
		$iblocks[$IBLOCK['IBLOCK_TYPE_ID']][$iblock_code]['PROPERTIES'] = $PROPS[$IBLOCK['ID']];
	}
	
	return $iblocks;
}

function get_bitrix_iblock_props()
{
	$properties = CIBlockProperty::GetList();
	$result = [];
	while ($PROP = $properties->GetNext())
	{
		filter_bitrix_array($PROP);
		
		$prop_code = ! empty($PROP['CODE']) ? $PROP['CODE'] : $PROP['ID'];
		$result[$PROP['IBLOCK_ID']][$prop_code] = $PROP;
	}
	return $result;
}

function filter_bitrix_array(&$array)
{
	foreach($array as $k => $v)
	{
		if (substr($k, 0, 1) === '~')
			unset($array[$k]);
	}
}

function get_bitrix_remote_scheme($path)
{
	$scheme = file_get_contents($path . '/IBLOCK_DIFF/get_scheme.php');
	
	return json_decode($scheme, true);
}

function compile_bitrix_diff($array1, $array2, $name1, $name2)
{
	$diff = bitrix_diff_arrays($array1, $array2);
	
	$result  = '<table style="width:100%">';
	$result .= empty($diff) ? 'Same structure' : bitrix_table_tr($name1, $name2) . $diff;
	$result .= '</table>';
	
	return $result;
}

function bitrix_table_tr($td1, $td2)
{
	return '<tr><td style="border-bottom:1px solid #c2c2c2">' . $td1 . '</td><td style="border-bottom:1px solid #c2c2c2">' . $td2 . '</td></tr>';
}

function bitrix_diff_arrays($array1, $array2, $prefix = '')
{
	global $skip_keys;
	
	$result = '';
	$only_arr2 = array_diff_key($array2, $array1);
	
	foreach ($only_arr2 as $k => $v)
	{
		$result .= bitrix_table_tr('<i>not found</i>', $prefix . '.' . $k);
	}
	
	foreach ($array1 as $k => $v)
	{
		if (in_array($k, $skip_keys))
			continue;
		
		if (is_null($v))
			continue;
		
		if ( ! isset($array2[$k]))
		{
			$result .= bitrix_table_tr($prefix . $k, '<i>not found</i>');
			continue;
		}
		
		if (is_array($v))
		{
			$result .= bitrix_diff_arrays($array1[$k], $array2[$k], $prefix . $k . '.');
			continue;
		}
		elseif ($array1[$k] !== $array2[$k])
		{
			$result .= bitrix_table_tr($prefix . $k . ' = ' . bitrix_print_prop($array1[$k]), $prefix . $k . ' = ' . bitrix_print_prop($array2[$k]));
		}
		else
		{
			// $result .= bitrix_table_tr($prefix . $k . print_r($array1[$k], true), ' === ' . $prefix . $k . print_r($array2[$k], true));
		}
	}
	
	return $result;
}

function bitrix_print_prop($prop)
{
	$need_pre = is_array($prop) or is_object($prop);
	
	$prop = print_r($prop, true);
	$prop = htmlspecialchars($prop);
	
	if ($need_pre)
		$prop = "<pre>{$prop}</pre>";
	
	return $prop;
}