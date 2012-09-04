(function () {
    tinymce.create("tinymce.plugins.Preview", {init:function (a, b) {
        var d = this, c = tinymce.explode(a.settings.content_css);
        d.editor = a;
        tinymce.each(c, function (f, e) {
            c[e] = a.documentBaseURI.toAbsolute(f)
        });
        a.addCommand("mcePreview", function () {
            a.windowManager.open({file:a.getParam("plugin_preview_pageurl", b + "/preview.html"), width:parseInt(a.getParam("plugin_preview_width", "550")), height:parseInt(a.getParam("plugin_preview_height", "600")), resizable:"yes", scrollbars:"yes", popup_css:c ? c.join(",") : a.baseURI.toAbsolute("themes/" + a.settings.theme + "/skins/" + a.settings.skin + "/content.css"), inline:a.getParam("plugin_preview_inline", 1)}, {base:a.documentBaseURI.getURI()})
        });
        a.addButton("preview", {title:"preview.preview_desc", cmd:"mcePreview"})
    }, getInfo:function () {
        return{longname:"Preview", author:"Moxiecode Systems AB", authorurl:"http://tinymce.moxiecode.com", infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/preview", version:tinymce.majorVersion + "." + tinymce.minorVersion}
    }});
    tinymce.PluginManager.add("preview", tinymce.plugins.Preview)
})();
