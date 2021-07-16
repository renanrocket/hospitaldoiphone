<?php

class chart{

	private $name;
	private $fontes;
	private $dados;
    private $tipo;
	

	function __construct($name, $fontes, $dados, $tipo = 'area'){
		$this->name 					= $name;
		$this->fontes 					= $fontes;
		$this->dados 					= $dados;
		$this->tipo 					= $tipo;
	}

    public function get_chart(){

        $cod = null;
        $name = strtolower(strtr($this->name, unserialize(CHAR_MAP)));

        //AREA CHART
        if($this->tipo=='area'){

            
            $cod .= '<div class="box box-primary">';
            $cod .= '<div class="box-header with-border">';
            $cod .= '<h3 class="box-title">'.$this->name.'</h3>';

            $cod .= '<div class="box-tools pull-right">';
            $cod .= '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>';
            $cod .= '</button>';
            $cod .= '<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>';
            $cod .= '</div>';
            $cod .= '</div>';
            $cod .= '<div class="box-body">';
            $cod .= '<div class="chart">';
            $cod .= '<canvas id="areaChart_'.$name.'" style="height:250px"></canvas>';
            $cod .= '</div>';
            $cod .= '</div>';
            $cod .= '</div>';
            

            $cod.= '<script>';
            $cod.= '$(function () {';


            // Get context with jQuery - using jQuery's .get() method.
            $cod.= 'var areaChartCanvas = $("#areaChart_'.$name.'").get(0).getContext("2d");';
            // This will get the first returned node in the jQuery collection.
            $cod.= 'var areaChart = new Chart(areaChartCanvas);';

            $cod.= 'var areaChartData = {';
            $cod.= 'labels: [';
            
            if(is_array($this->fontes)){
                for($i=0; $i<count($this->fontes); $i++){
                    if($i>0)
                        $cod.= ', ';
                    $cod.= '"'.$this->fontes[$i].'"';
                }
            }else{
                $cod.= '"'.$this->fontes.'"';
            }

            $cod.= '],';            
            $cod.= 'datasets: [';
            $cod.= '{';
            $cod.= 'label: "Gráfico",';
            $cod.= 'fillColor: "rgba(210, 214, 222, 1)",';
            $cod.= 'strokeColor: "rgba(210, 214, 222, 1)",';
            $cod.= 'pointColor: "rgba(210, 214, 222, 1)",';
            $cod.= 'pointStrokeColor: "#c1c7d1",';
            $cod.= 'pointHighlightFill: "#fff",';
            $cod.= 'pointHighlightStroke: "rgba(220,220,220,1)",';
            $cod.= 'data: [';
            
            if(is_array($this->dados)){
                for($i=0; $i<count($this->dados); $i++){
                    if($i>0)
                        $cod.= ', ';
                    $cod.= $this->dados[$i];
                }
            }else{
                $cod.= $this->dados;
            }
            
            $cod.= ']';
            $cod.= '}]}; ';

            $cod.= 'var areaChartOptions = {';
            //Boolean - If we should show the scale at all
            $cod.= 'showScale: true,';
            //Boolean - Whether grid lines are shown across the chart
            $cod.= 'scaleShowGridLines: false,';
            //String - Colour of the grid lines
            $cod.= 'scaleGridLineColor: "rgba(0,0,0,.05)",';
            //Number - Width of the grid lines
            $cod.= 'scaleGridLineWidth: 1,';
            //Boolean - Whether to show horizontal lines (except X axis)
            $cod.= 'scaleShowHorizontalLines: true,';
            //Boolean - Whether to show vertical lines (except Y axis)
            $cod.= 'scaleShowVerticalLines: true,';
            //Boolean - Whether the line is curved between points
            $cod.= 'bezierCurve: true,';
            //Number - Tension of the bezier curve between points
            $cod.= 'bezierCurveTension: 0.3,';
            //Boolean - Whether to show a dot for each point
            $cod.= 'pointDot: false,';
            //Number - Radius of each point dot in pixels
            $cod.= 'pointDotRadius: 4,';
            //Number - Pixel width of point dot stroke
            $cod.= 'pointDotStrokeWidth: 1,';
            //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
            $cod.= 'pointHitDetectionRadius: 20,';
            //Boolean - Whether to show a stroke for datasets
            $cod.= 'datasetStroke: true,';
            //Number - Pixel width of dataset stroke
            $cod.= 'datasetStrokeWidth: 2,';
            //Boolean - Whether to fill the dataset with a color
            $cod.= 'datasetFill: true,';
            //String - A legend template
            $cod.= 'legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].lineColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",';
            //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
            $cod.= 'maintainAspectRatio: true,';
            //Boolean - whether to make the chart responsive to window resizing
            $cod.= 'responsive: true';
            $cod.= '};';
            $cod.= '';
            
            //Create the line chart
            $cod.= 'areaChart.Line(areaChartData, areaChartOptions);})';
            $cod.= '</script>';



        }

        //BAR CHART
        else if($this->tipo=='bar'){

            $cod .= '<div class="box box-primary">';
            $cod .= '<div class="box-header with-border">';
            $cod .= '<h3 class="box-title">'.$this->name.'</h3>';

            $cod .= '<div class="box-tools pull-right">';
            $cod .= '<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>';
            $cod .= '</button>';
            $cod .= '<button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>';
            $cod .= '</div>';
            $cod .= '</div>';
            $cod .= '<div class="box-body">';
            $cod .= '<div class="chart">';
            $cod .= '<canvas id="barChart_'.$name.'" style="height:250px"></canvas>';
            $cod .= '</div>';
            $cod .= '</div>';
            $cod .= '</div>';

            //-------------
            //- BAR CHART -
            //-------------
            $cod.= '<script>';
            $cod.= '$(function () {';
            $cod.= 'var barChartCanvas = $("#barChart_'.$name.'").get(0).getContext("2d");';
            $cod.= 'var barChart = new Chart(barChartCanvas);';

            $cod.= 'var areaChartData = {';
                $cod.= 'labels: [';
                
                if(is_array($this->fontes)){
                    for($i=0; $i<count($this->fontes); $i++){
                        if($i>0)
                            $cod.= ', ';
                        $cod.= '"'.$this->fontes[$i].'"';
                    }
                }else{
                    $cod.= '"'.$this->fontes.'"';
                }
    
                $cod.= '],';            
                $cod.= 'datasets: [';
                $cod.= '{';
                $cod.= 'label: "Gráfico",';
                $cod.= 'fillColor: "rgba(210, 214, 222, 1)",';
                $cod.= 'strokeColor: "rgba(210, 214, 222, 1)",';
                $cod.= 'pointColor: "rgba(210, 214, 222, 1)",';
                $cod.= 'pointStrokeColor: "#c1c7d1",';
                $cod.= 'pointHighlightFill: "#fff",';
                $cod.= 'pointHighlightStroke: "rgba(220,220,220,1)",';
                $cod.= 'data: [';
                
                if(is_array($this->dados)){
                    for($i=0; $i<count($this->dados); $i++){
                        if($i>0)
                            $cod.= ', ';
                        $cod.= $this->dados[$i];
                    }
                }else{
                    $cod.= $this->dados;
                }
                
                $cod.= ']';
                $cod.= '}]}; ';

            $cod.= 'var barChartData = areaChartData;';
            $cod.= 'barChartData.datasets[0].fillColor = "#00a65a";';
            $cod.= 'barChartData.datasets[0].strokeColor = "#00a65a";';
            $cod.= 'barChartData.datasets[0].pointColor = "#00a65a";';
            
            $cod.= 'var barChartOptions = {';            
            //Boolean - Whether the scale should start at zero, or an order of magnitude down from the lowest value
            $cod.= 'scaleBeginAtZero: true,';
            //Boolean - Whether grid lines are shown across the chart
            $cod.= 'scaleShowGridLines: true,';
            //String - Colour of the grid lines
            $cod.= 'scaleGridLineColor: "rgba(0,0,0,.05)",';
            //Number - Width of the grid lines
            $cod.= 'scaleGridLineWidth: 1,';
            //Boolean - Whether to show horizontal lines (except X axis)
            $cod.= 'scaleShowHorizontalLines: true,';
            //Boolean - Whether to show vertical lines (except Y axis)
            $cod.= 'scaleShowVerticalLines: true,';
            //Boolean - If there is a stroke on each bar
            $cod.= 'barShowStroke: true,';
            //Number - Pixel width of the bar stroke
            $cod.= 'barStrokeWidth: 2,';
            //Number - Spacing between each of the X value sets
            $cod.= 'barValueSpacing: 5,';
            //Number - Spacing between data sets within X values
            $cod.= 'barDatasetSpacing: 1,';
            //String - A legend template
            $cod.= 'legendTemplate: "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>",';
            //Boolean - whether to make the chart responsive
            $cod.= 'responsive: true,';
            $cod.= 'maintainAspectRatio: true';
            $cod.= '};';

            $cod.= 'barChartOptions.datasetFill = false;';
            $cod.= 'barChart.Bar(barChartData, barChartOptions);';

            $cod.= '});';
            $cod.= '</script>';
        }

        return $cod;
    }



}


?>





    