initRTE = function(){


tinyMCE.init({
	mode : "exact",
	language: (SFdefault_language ? SFdefault_language : 'en'),
	theme : "advanced",
	elements : "Post, post",
	plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,simpleupload,",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_path_location : "bottom",
	extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name|style],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]",
	theme_advanced_resize_horizontal : "true",
	theme_advanced_resizing : "true",
	apply_source_formatting : "true",
	cleanup : true,
	advimagescale_max_width:  100,
	convert_urls : "false",
	theme_advanced_buttons1 : "bold,italic,underline,separator,formatselect,separator,forecolor,backcolor,separator,bullist,numlist,separator,outdent,indent,separator,undo,redo,separator,hr,removeformat,visualaid,simpleupload,image,media,link,separator,sub,sup,separator, charmap,emotions,removeformat,code",

	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",

	simpleupload_connector_path: '/var/media/connector/php/upload.php',
	simpleupload_images_path: '/var/media/media',

	inline_styles : true,
	force_br_newlines : "true",
	relative_urls: false,
	height:400,
	doctype : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
});

}
