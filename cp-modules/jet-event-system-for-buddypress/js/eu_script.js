/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery(document).ready(function(){
    var j=jQuery;
//append a loading box
j("input#event-name").wrap("<div id='eventname_checker'></div> ");
j("#eventname_checker").append("<span class='loading' style='display:none'></span>")
j("#eventname_checker").append("<span id='name-info'></span> ");
    j("input#event-name").bind("blur",function(){
		j("#eventname_checker #name-info").empty();//hhide the message
		//show loading icon
		j("#eventname_checker .loading").css({display:'block'});
		
        var event_name=j("input#event-name").val();
        j.post( ajaxurl, {
			action: 'check_eventname',
			'cookie': encodeURIComponent(document.cookie),
			'event_name':event_name
			},
		function(response){
                    var resp=JSON.parse(response);
						show_message(resp.message,resp.code);
				}
     
    );
});
function show_message(msg,respcode)
    {//hide ajax loader
	j("#eventname_checker #name-info").removeClass();
	j("#eventname_checker .loading").css({display:'none'});
     j("#eventname_checker #name-info").empty().html(msg);
       j("#eventname_checker #name-info").addClass(respcode);

    }
});

