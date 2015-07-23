<?php
// Developer Test Code
// Developer: David Lee
// Phone: 909-437-3556
// Date: 07/23/2015
// File: index.php

// Namespace that contains the layout coding
namespace theLayout{
	use theFields;
	
	// Writes the basic html headers and footer that is needed
	class drawHeaders{
		
		// Display the html header code
		static function doHeader() {
			?>
<html>
	<head>
		<title><?php echo theFields\savedInput::getProjectName();?> - <?php echo theFields\savedInput::getAuthor();?> <?php echo theFields\savedInput::getPhone();?> </title>
		<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
	</head>
	<body>
			<?php
		}
		
		// Display the html footer code
		static function doFooter() {
			?>
	</body>
	<footer>
	
	<script>

		var addressTable;
		// Make table pretty with dataTable
		$(document).ready(function() {
			addressTable = $('#addressTable').DataTable();	
		});	
				
		//submit the ajax form and get back information
		$("#addressForm").submit(function(e) {
			var postData = $(this).serializeArray();
			var formURL = $(this).attr("action");
			$.ajax({
				url : formURL,
				type: "POST",
				dataType: "json",
				data : postData,
				success:function(data) {
					if (data.status != "OK") {
						alert("No Results For Given Address");
					} else {
						addressTable.row.add( [
							data.validated_address1,
							data.validated_city,
							data.validated_state,
							data.validated_zip
						] ).draw();
					}
 					
					console.log(data);
				},
				error: function(error) {
					 console.log("Error:");
					 console.log(error);
				}
			});
			e.preventDefault(); 
		});		
	</script>
	
	</footer>
</html>
			<?php
		}
	}
	
	// Display the developer's information
	class drawIntro{
		static function doLayout() {
				echo theFields\savedInput::getProjectName()."<BR/>";
				echo "Name: ". theFields\savedInput::getAuthor()."<BR/>";
				echo "Phone: ". theFields\savedInput::getPhone()."<BR/>";
		}
	}
	
	// Display the address table
	class drawTable{
		static function doLayout() {
			?>
				<BR/>
				<table id="addressTable" class="display" cellspacing="0" width="100%">
					<thead>
						<tr><th><?php echo theFields\allHeaders::street(); ?></th><th><?php echo theFields\allHeaders::city(); ?></th><th><?php echo theFields\allHeaders::state(); ?></th><th><?php echo theFields\allHeaders::zip(); ?></th></tr>
					</thead>

					<tfoot>
						<tr><th><?php echo theFields\allHeaders::street(); ?></th><th><?php echo theFields\allHeaders::city(); ?></th><th><?php echo theFields\allHeaders::state(); ?></th><th><?php echo theFields\allHeaders::zip(); ?></th></tr>
					</tfoot>

					<tbody>
					</tbody>
				</table>
				
			<?php
		}
	}
	
	// Display the input form for data submission
	class drawForm{
		static function doLayout() {
		?>
		
		<form name="addressForm" id="addressForm" action="submitaddress.php" method="POST">
			<BR/><BR/>Address Lookup Form<BR/>
			<table>
			<tr><td><?php echo theFields\allHeaders::street(); ?>:</td><td><?php echo theFields\allHeadersFields::street(); ?></td></tr>
			<tr><td><?php echo theFields\allHeaders::city(); ?>:</td><td><?php echo theFields\allHeadersFields::city(); ?></td></tr>
			<tr><td><?php echo theFields\allHeaders::state(); ?>:</td><td><?php echo theFields\allHeadersFields::state(); ?></td></tr>
			</table>
			<input type="submit" value="Submit">
		</form>
		
		<?php
		}
	}
	
	// Draw everything needed for the html
	class drawLayout{
		function __construct() {
			drawHeaders::doHeader();
			drawIntro::doLayout();
			drawForm::doLayout();
			drawTable::doLayout();
			drawHeaders::doFooter();
		}
	}
	
}

namespace theFields{
	// Headers names for the table
	class allHeaders{
		static function street() {
			echo "Street";
		}
		static function city() {
			echo "City";
		}
		static function state() {
			echo "State";
		}
		static function zip() {
			echo "Zip";
		}
	}
	
	// Input fields that is needed
	class allHeadersFields{
		 static function street() {
			echo "<input name=\"street\" />";
		 }
		 static function city() {
			echo "<input name=\"city\" />";
		 }
		 static function state() {
			echo "<input name=\"state\" />";
		 }
	}
	
	// Stores the variable data that is used
	class savedInput{
		static function getAuthor() {
			return "David Lee";
		}
		static function getPhone() {
			return "(909) 437-3556";
		}
		static function getProjectName() {
			return "Developer Code Test";
		}
	}
}

namespace{
	// Calls the function that shows all the html
	new theLayout\drawLayout();
	
}

?>
