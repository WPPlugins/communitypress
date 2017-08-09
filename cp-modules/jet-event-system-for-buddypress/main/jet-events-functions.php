<?php

function hidden_events()
	{
		global $bp;
		if ( ! (bp_is_page(JES_SLUG) ) )
			{
				return;
		}
			else
		{
			if ( ! is_user_logged_in())
				bp_core_redirect($bp->root_domain.'/'.BP_REGISTER_SLUG);
		}
} 
	$jes_adata = get_site_option('jes_events' );
	if ($jes_adata[ 'jes_events_addnavi_disable' ])
		{
			add_action('get_header','hidden_events');
	}


/* Date */

function jes_array_defdatemask($enterlocale='en_GB')
	{
	$jjqlang = Array ( 'af_AF' => 'dd/mm/yy', 'ar_AR' => 'dd/mm/yy', 'az_AZ' => 'dd.mm.yy', 'bg_BG' => 'dd.mm.yy', 'bs_BS' => 'dd.mm.yy', 'ca_CA' => 'dd/mm/yy', 'cs_CS' => 'dd.mm.yy', 'da_DA' => 'dd-mm-yy', 'de-CH' => 'dd.mm.yy', 'de_DE' => 'dd.mm.yy', 'el_EL' => 'dd/mm/yy', 'em-GB' => 'dd/mm/yy', 'eo_EO' => 'dd/mm/yy', 'es_ES' => 'dd/mm/yy', 'et_ET' => 'dd.mm.yy', 'eu_EU' => 'yy/mm/dd', 'fa_FA' => 'yy/mm/dd', 'fi_FI' => 'dd.mm.yy', 'fo_FO' => 'dd-mm-yy', 'fr_CH' => 'dd.mm.yy', 'fr_FR' => 'dd/mm/yy', 'he_HE' => 'dd/mm/yy', 'hr_HR' => 'dd.mm.yy.', 'hu_HU' => 'yy-mm-dd', 'hy_HY' => 'dd.mm.yy', 'id_ID' => 'dd/mm/yy', 'is_IS' => 'dd/mm/yy', 'it_IT' => 'dd/mm/yy', 'ja_JA' => 'yy/mm/dd', 'ko_KO' => 'yy-mm-dd', 'lt_LT' => 'yy-mm-dd', 'lv_LV' => 'dd-mm-yy', 'ms_MS' => 'dd/mm/yy', 'nl-BE' => 'dd/mm/yy', 'nl_NL' => 'dd-mm-yy', 'no_NO' => 'yy-mm-dd', 'pl_PL' => 'yy-mm-dd', 'pt_BR' => 'dd/mm/yy', 'ro_RO' => 'dd.mm.yy', 'ru_RU' => 'dd.mm.yy', 'sk_SK' => 'dd.mm.yy', 'sl_SL' => 'dd.mm.yy', 'sq_SQ' => 'dd.mm.yy', 'sr-SR' => 'dd/mm/yy', 'sr_SR' => 'dd/mm/yy', 'sv_SV' => 'yy-mm-dd', 'ta_TA' => 'dd/mm/yy', 'th_TH' => 'dd/mm/yy', 'tr_TR' => 'dd.mm.yy', 'uk_UK' => 'dd/mm/yy', 'vi_VI' => 'dd/mm/yy', 'zh-CN' => 'yy-mm-dd', 'zh-HK' => 'dd-mm-yy', 'zh-TW' => 'yy/mm/dd', 'en-US' => 'mm/dd/yy', 'sr-RS' => 'yy/mm/md', 'sr_RS' => 'yy/mm/dd');
	$result = $jjqlang[$enterlocale];
return $result;
}

function jes_defdatemask_jq()
	{
	$locale = apply_filters( 'wordpress_locale', get_locale() );
	$result = $jes_array_defdatemask($locale);
	if (!$result) { $result = 'dd.mm.yy'; }
return $result;
}

function jes_array_defdatemask_php($enterlocale='en_GB')
	{
	$jphplang = Array ( 'af_AF' => 'd/m/Y', 'ar_AR' => 'd/m/Y', 'az_AZ' => 'd.m.Y', 'bg_BG' => 'd.m.Y', 'bs_BS' => 'd.m.Y', 'ca_CA' => 'd/m/Y', 'cs_CS' => 'd.m.Y', 'da_DA' => 'd-m-Y', 'de-CH' => 'd.m.Y', 'de_DE' => 'd.m.Y', 'el_EL' => 'd/m/Y', 'em-GB' => 'd/m/Y', 'eo_EO' => 'd/m/Y', 'es_ES' => 'd/m/Y', 'et_ET' => 'd.m.Y', 'eu_EU' => 'Y/m/d', 'fa_FA' => 'Y/m/d', 'fi_FI' => 'd.m.Y', 'fo_FO' => 'd-m-Y', 'fr_CH' => 'd.m.Y', 'fr_FR' => 'd/m/Y', 'he_HE' => 'd/m/Y', 'hr_HR' => 'd.m.Y.', 'hu_HU' => 'Y-m-d', 'hy_HY' => 'd.m.Y', 'id_ID' => 'd/m/Y', 'is_IS' => 'd/m/Y', 'it_IT' => 'd/m/Y', 'ja_JA' => 'Y/m/d', 'ko_KO' => 'Y-m-d', 'lt_LT' => 'Y-m-d', 'lv_LV' => 'd-m-Y', 'ms_MS' => 'd/m/Y', 'nl-BE' => 'd/m/Y', 'nl_NL' => 'd-m-Y', 'no_NO' => 'Y-m-d', 'pl_PL' => 'Y-m-d', 'pt_BR' => 'd/m/Y', 'ro_RO' => 'd.m.Y', 'ru_RU' => 'd.m.Y', 'sk_SK' => 'd.m.Y', 'sl_SL' => 'd.m.Y', 'sq_SQ' => 'd.m.Y', 'sr-SR' => 'd/m/Y', 'sr_SR' => 'd/m/Y', 'sv_SV' => 'Y-m-d', 'ta_TA' => 'd/m/Y', 'th_TH' => 'd/m/Y', 'tr_TR' => 'd.m.Y', 'uk_UK' => 'd/m/Y', 'vi_VI' => 'd/m/Y', 'zh-CN' => 'Y-m-d', 'zh-HK' => 'd-m-Y', 'zh-TW' => 'Y/m/d', 'en-US' => 'm/d/Y', 'sr-RS' => 'Y/m/d', 'sr_RS' => 'Y/m/d' );	
	$result = $jphplang[$enterlocale];
return $result;
}

function jes_defdatemask_php()
	{
	$locale = apply_filters( 'wordpress_locale', get_locale() );
	$result = jes_array_defdatemask_php($locale);
	if (!$result) { $result = 'd.m.Y'; }
return $result;
}


function jes_datetounix($indate = '',$intimeh = '',$intimem = '') {
	$formatjes = jes_defdatemask_php();
if (($indate == null) || ($intimeh == null) || ($intimem == null))
	{
		$rezjes = date_i18n('d m Y H:i');
		$j_day = substr($rezjes,0,2);
		$j_month = substr($rezjes,3,2);
		$j_year = substr($rezjes,6,4);
		$j_h = substr($rezjes,11,2);
		$j_m = substr($rezjes,14,2);
		$rezjes2 = mktime((int)$j_h,(int)$j_m, 0, (int)$j_month, (int)$j_day, (int)$j_year);
	}
		else
	{
if (($formatjes == 'd-m-Y') || ($formatjes == 'd/m/Y') || ($formatjes == 'd.m.Y'))
	{
		$j_day = substr($indate,0,2);
		$j_month = substr($indate,3,2);
		$j_year = substr($indate,6,4);
	}
	else {
	if (($formatjes == 'm-d-Y') || ($formatjes == 'm/d/Y') || ($formatjes == 'm.d.Y'))
		{
			$j_day = substr($indate,3,2);
			$j_month = substr($indate,0,2);
			$j_year = substr($indate,6,4);
		}
		else {
	if (($formatjes == 'Y-m-d') || ($formatjes == 'Y/m/d') || ($formatjes == 'Y.m.D'))
		{
			$j_day = substr($indate,8,2);
			$j_month = substr($indate,5,2);
			$j_year = substr($indate,0,4);
		}		
		}
	}
                // $rezjes2 = date_i18n($formatjes.' H:i',mktime((int)$intimeh,(int)$intimem, 0, (int)$j_month, (int)$j_day, (int)$j_year));


		$rezjes = date_i18n($formatjes.' H:i',mktime((int)$intimeh,(int)$intimem, 0, (int)$j_month, (int)$j_day, (int)$j_year));

 if (($formatjes == 'd-m-Y') || ($formatjes == 'd/m/Y') || ($formatjes == 'd.m.Y'))
	{
		$j_day = substr($rezjes,0,2);
		$j_month = substr($rezjes,3,2);
		$j_year = substr($rezjes,6,4);
	}
	else {
	if (($formatjes == 'm-d-Y') || ($formatjes == 'm/d/Y') || ($formatjes == 'm.d.Y'))
		{
			$j_day = substr($rezjes,3,2);
			$j_month = substr($rezjes,0,2);
			$j_year = substr($rezjes,6,4);
		}
		else {
	if (($formatjes == 'Y-m-d') || ($formatjes == 'Y/m/d') || ($formatjes == 'Y.m.D'))
		{
			$j_day = substr($rezjes,8,2);
			$j_month = substr($rezjes,5,2);
			$j_year = substr($rezjes,0,4);
		}		
		}
	}
	$j_h = substr($rezjes,11,2);
	$j_m = substr($rezjes,14,2);
		$rezjes2 = mktime((int)$j_h,(int)$j_m, 0, (int)$j_month, (int)$j_day, (int)$j_year); 
	}
	return $rezjes2;
}

function unixtodate($inputunix) {
	$ddateformat = jes_defdatemask_php();
	$ev_dres = date($ddateformat, $inputunix);
	return $ev_dres;
}

function unixtotime($inputunix) {
	$ev_dres = time('H:i',$inputunix);
	return $ev_dres;
}

function eventstatus($inarg1 = '', $inarg2 = '', $inarg3 = '', $inarg4 = '', $inarg5 = '', $inarg6 = '') {
	$jes_adata = get_site_option('jes_events' );
	$mainarg = jes_datetounix();
	$iinarg1 = jes_datetounix($inarg1,$inarg2,$inarg3);
	$iinarg2 = jes_datetounix($inarg4,$inarg5,$inarg6);

if ( ( $iinarg1 < $mainarg ) && ( $iinarg2 > $mainarg ) )
	{
		$rezstat = '<span style="color:#'.$jes_adata[ 'jes_events_color_current' ].'">'.__('Current event','jet-event-system').'</span>';
	}
		else
			{
				if ( ( $iinarg2 < $mainarg ) )
					{
						$rezstat = '<span style="color:#'.$jes_adata[ 'jes_events_color_past' ].'">'.__('Past event','jet-event-system').'</span>';
					}
						else
							{
								$rezstat = '<span style="color:#'.$jes_adata[ 'jes_events_color_active' ].'">'.__('Active event','jet-event-system').'</span>';
							}
			}
		
return $rezstat;
}
function jes_offset()
	{
		$offset1 = mktime(date_i18n('H'),date_i18n('i'),date_i18n('s'),date_i18n('m'),date_i18n('d'),date_i18n('Y'));
		$offset2 = time();
		$offset = $offset1 - $offset2;
		return $offset;
}
?>