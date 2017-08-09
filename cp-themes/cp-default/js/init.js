jQuery(function(){
	jQuery.noConflict();
	
	jQuery('ul.superfish').superfish({ 
		delay:       200,                            // one second delay on mouseout 
		animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 
		speed:       'fast',                          // faster animation speed 
		autoArrows:  true,                           // disable generation of arrow mark-up 
		dropShadows: false                            // disable drop shadows 
	});
	
});