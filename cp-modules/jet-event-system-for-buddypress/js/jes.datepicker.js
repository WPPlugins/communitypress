// AJAX Functions
	var jqd = jQuery;
	jqd(document).ready(function() {
		jqd("#event-edtsd").datepicker(
			{
				changeMonth: true,
				changeYear: true,
				yearRange: '2010:2020',
				gotoCurrent: true
			});
		jqd("#event-edted").datepicker(
			{
				changeMonth: true,
				changeYear: true,
				yearRange: '2010:2020',
				gotoCurrent: true
			});		
	});