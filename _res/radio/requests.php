<?php

	if( !preg_match( "/index.php/i", $_SERVER['PHP_SELF'] ) ) { die(); }
	
	// Set the location of the requests.status file
	$requests_status = "../../_inc/requests.status";
	
	// First, we check the availiability of the status file
	if ( file_exists( $requests_status ) ) {
	
		$result = file_get_contents( $requests_status );
	
	}
	else {
		
		$result = "0";
	
	}
?>

<div class="box" align="right">

	<?php
		if( isset( $_GET['clear_requests'] ) ) {
		
			$db->query( "DELETE FROM requests WHERE `for` = '{$user->data['id']}'" );
	
			echo "<div class=\"square good\" align=\"left\">";
			echo "<strong>Success</strong>";
			echo "<br />";
			echo "Requests cleared.";
			echo "</div>";

		}
		elseif ( isset( $_GET['toggle_requests'] ) ) {
		
			// First, we find out the current state of requests
			if ( $result == "0" ) {
			
				// If it is already disabled, we enable it
				$handle = fopen( $requests_status, 'w' );
		
				// And write the data we downloaded to it
				fwrite( $handle, "1" );
				fclose( $handle );
				
			}
			else {
			
				// If it is already enabled, we disable it in one command, remove it!
				@unlink( $requests_status );
				
			}			
		}
		elseif( isset( $_GET['clear_all_requests'] ) and ( $user->hasGroup( '4' ) or $user->hasGroup( '5' ) ) ) {
			
			$db->query( "DELETE FROM requests" );
			
			echo "<div class=\"square good\" align=\"left\">";
			echo "<strong>Success</strong>";
			echo "<br />";
			echo "Requests cleared.";
			echo "</div>";
		
		}
	
		if( $user->hasGroup( '4' ) or $user->hasGroup( '5' ) ) {
	
	?>
	
	<form style="display: inline;" action="?clear_all_requests" method="post" onsubmit="if( !confirm('Are you sure you want to delete all requests?') ) { return false; }">
	
		<input type="submit" class="button red" value="Clear all requests" />
	
	</form>
	
	<?php
	
		}
	
	?>

	<form style="display: inline;" action="?clear_requests" method="post">

		<input type="submit" class="button red" value="Clear my requests" />

	</form>

	<form style="display: inline;" action="#" onsubmit="Radi.deleteCheckedRequests(); return false;" method="post">

		<input type="submit" class="button red" value="Clear selected requests" />

	</form>
	
	<form style="display: inline;" action="?status_toggle" method="post" onsubmit="if( !confirm('Are you sure you want to <?php if ( $result == "0" ) { echo "enable"; } else { echo "disable"; } ?> the request line?') ) { return false; }">
	
		<input type="submit" class="button <?php if ( $result == "0" ) { echo "green"; } else { echo "red"; } ?>" value="Toggle requests" />
	
	</form>

</div>

<div class="box">

	<strong>Filter by type:</strong>
	
	<br />
	
	<?php 
	
		$query = $db->query( "SELECT * FROM request_types" );
		
		echo "<a href=\"#\" onclick=\"Radi.requestsByType('*'); return false;\">All</a>&nbsp;&nbsp;";
		
		while( $array = $db->assoc( $query ) ) {
		
			echo "<a href=\"#\" onclick=\"Radi.requestsByType({$array['id']}); return false;\" style=\"color: #{$array['colour']};\">{$array['name']}s</a>&nbsp;&nbsp;";
		
		}
		
		if( $user->hasGroup( '4' ) or $user->hasGroup( '5' ) ) {

			echo "<a href=\"#\" onclick=\"Radi.requestsByType('all'); return false;\" style=\"color: #ff0099;\">All DJs</a>&nbsp;&nbsp;";

		}
		
	?>

</div>

<div id="requestlist"></div>

<script type="text/javascript">
	//<![CDATA[
		Radi.requestsByType('*');
	//]]>
</script>