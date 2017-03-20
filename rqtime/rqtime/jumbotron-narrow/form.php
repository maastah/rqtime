<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>RT Lab</title>

    <!-- Bootstrap core CSS -->
    <link href="../../dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../../assets/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron-narrow.css" rel="stylesheet">
	<link href="custom.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

<body>

    <div class="container">
      <div class="header clearfix">
        <h3 class="text-muted">Request time lab</h3>
		<div id="chart_div"></div>
      </div>

      <div class="jumbotron">
        <h1 style="font-size:35px;">Type your URL here: <span style="font-size:0.4em;font-weight:400;">("http://example.com/" syntax)</span></h1>
        <form method="GET">
			<input type="Text" value="" name="user_url_form">
			<input type="Submit" name="Submit-btn" value="Check it!">
		</form>
      </div>
		<?php
if(!isset($_GET['Submit-btn']))
 die("");
  
$user_url = $_GET['user_url_form'];
$actdata = date("Y.m.d");

include 'crawler.php';
crawl_site($user_url);


?>
      <div class="row marketing">
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
		
			
		var ga = <?php echo $jsarraya; ?>;
		var gb = <?php echo $jsarrayb; ?>;
				
      // Load the Visualization API and the corechart package.
      google.charts.load('current', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.charts.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Link');
        data.addColumn('number', 'Rq Time');
        for(i = 0; i < ga.length; i++)
			data.addRow(
		[ga[i].toString(), parseFloat(gb[i])],
		);


        // Set chart options
        var options = {'title':'Request time of URLs',
                       'width':800,
                       'height':400};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.BarChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
	
	
	<div class="great-table" style="margin-top:50px;">
	<?php echo $html_table; ?>
	</div>
	
	<div style="margin-top: 50px;">
	<h3>Search history:</h3>
	<?php 
	// Search history from $ah
	foreach ($ah as $key => $val){
		echo '<a href="form.php?user_url_form='. $key .'">'. $key .'</a> - '. $val ."<br />";
	
	}
	?>
	
	</div>
      </div>

      <footer class="footer">
        <p>Jakub Hlubiński 2017</p>
      </footer>

    </div> <!-- /container -->


    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
