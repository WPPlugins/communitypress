<?php do_action( 'bp_before_event_googlemap' ); ?>

<?php $jes_adata = get_site_option('jes_events' ); ?>

<table valign="top" width="100%">
    <tr>
	<td width="50%" style="vertical-align:top">
<span><strong><?php _e('The event will take place at:','jet-event-system'); ?></strong>
		<?php if ( jes_bp_event_is_visible() ) { ?>
					<p><?php jes_bp_event_placedaddress() ?></p>
		<?php } else {?>	
					<p>join this event to see its location...</p>
		<?php } ?>		
	</td>
	<td width="50%">
<?php if ( ( jes_bp_get_event_placedgooglemap() == null ) || ( $jes_adata['jes_events_googlemapopt_type'] == 'google' ) ) { ?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript">
  var geocoder;
  var map;
function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
      zoom: 15,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }

  function codeAddress() {
    initialize();
    var address = "<?php if ( jes_bp_event_is_visible() ) { echo jes_bp_get_event_placedaddress(); } else { echo 'USA'; } ?>";
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map, 
            position: results[0].geometry.location
        });
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }
window.onload = codeAddress;
</script>

<div id="map_canvas" style="width: 400px; height: 350px;"></div>
<?php } else { ?>
<img src="<?php jes_bp_event_placedgooglemap() ?>">
<?php } ?>
	</td>
    </tr>
</table>
<?php do_action( 'bp_after_event_googlemap' ); ?>