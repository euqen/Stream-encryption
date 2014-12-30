$('input[type="radio"]').click(function(){
    if ($(this).attr('name') == 'LSFR1') {
        $('input[name="second1"]').attr("disabled", "disabled");
        $('input[name="third1"]').attr("disabled", "disabled");
        $('input[name="RC4KEY"]').attr("disabled", "disabled");
         $('input[name="fileName"]').attr("disabled", "disabled");
        $('input[name="first1"]').removeAttr("disabled");
        $('input[name="geffe"]').removeAttr("checked");
        $('input[name="RC4"]').removeAttr("checked");
        
    }
    else if($(this).attr('name') == 'geffe') {
        $('input[name="LSFR1"]').removeAttr("checked");
        $('input[name="RC4"]').removeAttr("checked");
        $('input[name="first1"]').removeAttr("disabled");
        $('input[name="second1"]').removeAttr("disabled");
        $('input[name="third1"]').removeAttr("disabled");
        $('input[name="RC4KEY"]').attr("disabled", "disabled");
        $('input[name="fileName"]').attr("disabled", "disabled");
    }
    else if($(this).attr('name') == 'RC4') {
        $('input[name="LSFR1"]').removeAttr("checked");
        $('input[name="geffe"]').removeAttr("checked");
        $('input[name="RC4KEY"]').removeAttr("disabled");
        $('input[name="fileName"]').removeAttr("disabled");
        $('input[name="first1"]').attr("disabled", "disabled");
        $('input[name="second1"]').attr("disabled", "disabled");
        $('input[name="third1"]').attr("disabled", "disabled");
    }
});