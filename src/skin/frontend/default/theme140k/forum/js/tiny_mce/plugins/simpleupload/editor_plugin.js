(function(){tinymce.PluginManager.requireLangPack('simpleupload');tinymce.create('tinymce.plugins.SimpleuploadPlugin',{init:function(ed,url){ed.addCommand('mceSimpleupload',function(){ed.windowManager.open({file:url+'/dialog.htm?'+new Date().getUTCSeconds(),width:320+parseInt(ed.getLang('simpleupload.delta_width',0)),height:100+parseInt(ed.getLang('simpleupload.delta_height',0)),inline:1},{plugin_url:url})});ed.addButton('simpleupload',{title:'simpleupload.desc',cmd:'mceSimpleupload',image:url+'/img/simpleupload.png'});ed.onNodeChange.add(function(ed,cm,n){cm.setActive('simpleupload',n.nodeName=='IMG')})},createControl:function(n,cm){return null},getInfo:function(){return{longname:'Simpleupload plugin',author:'Some author',authorurl:'http://webideaonline.com',version:"1.1"}}});tinymce.PluginManager.add('simpleupload',tinymce.plugins.SimpleuploadPlugin)})();