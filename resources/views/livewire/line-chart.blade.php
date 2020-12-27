<div>
    <script src="{{asset('vendor/chart.js/Chart.min.js')}}">
    </script>
    <script src="{{asset('js/utils.js')}}">
    </script>
    
    <div style="width:100%;">
        <canvas id="canvas" style="display: block; width: 1125px; height: 562px;" width="1125" height="562" class="chartjs-render-monitor"></canvas>
    </div>
    <script>

        var config = {
            type: 'line',
            data: {
                labels: @json($period['labels']),
                datasets: [{
                    label: 'Absent',
                    backgroundColor: window.chartColors.red,
                    borderColor: window.chartColors.red,
                    data: @json(array_values($period['data']['Absent'])) ,
                    fill: false,
                }, {
                    label: 'Present',
                    fill: false,
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.blue,
                    data: @json(array_values($period['data']['Present'])),
                }]
            },
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Attendance by Period'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'date'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Count'
                        },
                        ticks: {
                            min: {{$period['min']}},
                            max: {{$period['max']}},
        
                            // forces step size to be 5 units
                            stepSize: 2
                        }
                    }]
                }
            }
        };
        
        window.onload = function() {
            var ctx = document.getElementById('canvas').getContext('2d');
            window.myLine = new Chart(ctx, config);
        };
        
        </script>
        
</div>
