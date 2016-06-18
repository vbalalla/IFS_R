<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
	<script src="calendar/js/jquery.js"></script>
	<script src="calendar/js/responsive-calendar.js"></script>

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="calendar/css/responsive-calendar.css" rel="stylesheet" media="screen">
	
  </head>
  <body>
    <div class="container">
      <!-- Responsive calendar - START -->
    	<div class="responsive-calendar">
        <div class="controls">
            <a class="pull-left" data-go="prev"><div class="btn btn-primary">Prev</div></a>
            <h4><span data-head-year></span> <span data-head-month></span></h4>
            <a class="pull-right" data-go="next"><div class="btn btn-primary">Next</div></a>
        </div><hr/>
        <div class="day-headers">
          <div class="day header">Mon</div>
          <div class="day header">Tue</div>
          <div class="day header">Wed</div>
          <div class="day header">Thu</div>
          <div class="day header">Fri</div>
          <div class="day header">Sat</div>
          <div class="day header">Sun</div>
        </div>
        <div class="days" data-group="days">
          
        </div>
      </div>
      <!-- Responsive calendar - END -->
    </div>
    <script src="calendar/js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="calendar/js/responsive-calendar.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $(".responsive-calendar").responsiveCalendar({
          time: '2015-08',
          events: {
            "2015-08-30": {"number": 5, "url": "http://w3widgets.com/responsive-slider"},
            "2015-08-26": {"number": 1, "url": "http://w3widgets.com"}, 
            "2015-08-03":{"number": 1}, 
            "2015-08-12": {}}
        });
      });
    </script>
  </body>
</html>