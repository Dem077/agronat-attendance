<div>
    <script src="{{asset('vendor/chart.js/Chart.min.js')}}">
    </script>
    <script src="{{asset('js/utils.js')}}">
    </script>
    
    <div style="width:100%;">
        <canvas id="bar-canvas" style="display: block; width: 1125px; height: 562px;" width="1125" height="562" class="chartjs-render-monitor"></canvas>
    </div>
    <script>
        var period=@json($period);

		var MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
		var color = Chart.helpers.color;
		var barChartData = {
			labels: period.labels,
			datasets: [{
				label: 'Absent',
				backgroundColor: color(window.chartColors.red).alpha(0.5).rgbString(),
				borderColor: window.chartColors.red,
				borderWidth: 1,
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}, {
				label: 'Present',
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				borderWidth: 1,
				data: [
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor(),
					randomScalingFactor()
				]
			}]

		};

		barChartData.datasets=period.map(function(label,datapoints){
			return {
				label: label,
				backgroundColor: color(window.chartColors.blue).alpha(0.5).rgbString(),
				borderColor: window.chartColors.blue,
				borderWidth: 1,
				data: [
					datapoints
				]
			}
		});

		window.onload = function() {
			var ctx = document.getElementById('bar-canvas').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'top',
					},
					title: {
						display: true,
						text: 'Chart.js Bar Chart'
					}
				}
			});

		};
	
        </script>
        
</div>
