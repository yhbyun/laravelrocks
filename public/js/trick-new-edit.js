(function($){
    $("#tags").selectize({
        maxItems:5,
        persist: false,
        create: function(input) {
            return {
                value: input,
                text: input
            }
        }
    });
    $("#categories").selectize({
        maxItems:5,
        persist: false,
        create: false
    });

    var t = ace.edit("editor-content");
    var n = $("#code-editor");
    t.setTheme("ace/theme/github");
    t.getSession().setMode("ace/mode/php");
    t.getSession().setValue(n.val());
    n.closest("form").submit(function(){
        n.val(t.getSession().getValue())
    })
})(jQuery);
