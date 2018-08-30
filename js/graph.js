
	$(function () {

		var chart_line_regions = c3.generate({
		    bindto: '#c3-line-regions-chart',
		    size: { height: 350 },
		    point: {
		        r: 3
		    },
		    data: {
		        columns: graph_line,
		    },
		    axis: {
	                x: {
	                    type: 'category',
	                    categories: ['1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月']
	                },
		    },
		    grid: {
		        y: {
		            show: false
		        }
		    },
		    legend: {
		    	show: false
		    },
		    tooltip: {
		      grouped: false
		    },
		    transition: {
		      duration: 500
		    }

		});

		$(".sidebar-control").on('click', function() {
		    chart_line_regions.resize();
		});

	    require.config({
	        paths: {
	            echarts: 'assets/js/plugins/visualization/echarts'
	        }
	    });

	    require(
	        [
	            'echarts',
	            'echarts/theme/limitless',
	            'echarts/chart/bar',
           		'echarts/chart/pie',
	            'echarts/chart/line'
	        ],

	        function (ec, limitless) {

	            var stacked_bars = ec.init(document.getElementById('stacked_bars'), limitless);
            	var stacked_columns = ec.init(document.getElementById('stacked_columns'), limitless);
            	// var stacked_lines = ec.init(document.getElementById('stacked_lines'), limitless);
	            var basic_pie = ec.init(document.getElementById('basic_pie'), limitless);

	            stacked_bars_options = {

	                // Setup grid
	                grid: {
	                    x: 75,
	                    x2: 25,
	                    y: 35,
	                    y2: 25
	                },

	                

	                // Add tooltip
	                tooltip: {
	                    trigger: 'axis',
	                    axisPointer: {
	                        type: 'shadow'
	                    }
	                },

	                // Add legend
	                legend: {
	                    data: ['タイヤ', 'バッテリー', 'ホイール']
	                },

	                // Enable drag recalculate
	                calculable: false,

	                // Horizontal axis
	                xAxis: [{
	                    type: 'value',
	                	splitLine: {
	                		show: false,
	                	},
	                	splitArea: {
	                		show: false
	                	} 
	                }],

	                // Vertical axis
	                yAxis: [{
	                    type: 'category',
	                    data: stores,
	                }],

	                // Add series
	                series: [
	                    {
	                        name: 'タイヤ',
	                        type: 'bar',
	                        stack: 'Total',
	                            
	                        data: type['ban']
	                    },
	                    {
	                        name: 'バッテリー',
	                        type: 'bar',
	                        stack: 'Total',
	                        data: type['battery']
	                    },
	                    {
	                        name: 'ホイール',
	                        type: 'bar',
	                        stack: 'Total',
	                        data: type['velg']
	                    },
	                    {
	                        name: 'オイル',
	                        type: 'bar',
	                        stack: 'Total',
	                        data: type['oli']
	                    }
	                ]
	            };

	            stacked_columns_options = {

	                // Setup grid
	                grid: {
	                    x: 40,
	                    x2: 47,
	                    y: 35,
	                    y2: 25
	                },
	                // Add tooltip
	                tooltip: {
	                    trigger: 'item',
	                    axisPointer: {
	                        type: 'shadow' // 'line' | 'shadow'
	                    }
	                },

	                // // Add legend
	                // legend: {
	                //     data: stores
	                // },

	               
	                // Enable drag recalculate
	                calculable: false,

	                // Horizontal axis
	                xAxis: [{
	                    type: 'category',
	                    data: [
	                        '1月', '2月', '3月', '4月', '5月', '6月', '7月', '8月', '9月', '10月', '11月', '12月'
	                    ],
	                    splitLine: {
	                    	show: false,
	                    },

	                }],

	                // Vertical axis
	                yAxis: [{
	                    type: 'value',
	                    splitArea: {
	                    	show: false
	                    },
	                    axisLabel: {
                        	width: '100%',
                            formatter: function(value){
                                // return this.value/10000 + "K";
                         		return value/10000;
                        	},
                        	rich: {
                        	        a: {
                        	            color: 'red',
                        	            lineHeight: 10
                        	        },
                        	        b: {
                        	            backgroundColor: {
                        	                image: 'xxx/xxx.jpg'
                        	            },
                        	            height: 40
                        	        },
                        	        x: {
                        	            fontSize: 18,
                        	            fontFamily: 'Microsoft YaHei',
                        	            borderColor: '#449933',
                        	            borderRadius: 4
                        	        },
                        	    }
                    	},
	                }],

	                // Add series
	                series: graph_column
	            };

	            var dataStyle = {
                    normal: {
                        label: {show: false},
                        labelLine: {show: false}
                    }
                };

	            basic_pie_options = {

                // Add tooltip
                tooltip: {
                    trigger: 'item',
                    formatter: "{b} <br> {d}%"
                },

                // Add legend
                legend: {
                    display: true,
                    orient: 'vertical',
                    x: 'right',
                    data: graph_pie,
                },

                // Enable drag recalculate
                calculable: false,

                // Add series
                series: [{
                    name: 'Brand',
                    type: 'pie',
                    radius: '90%',
                    center: ['20%', '50%'],
                    itemStyle: dataStyle,
                    data: graph_pie
                }]
            };


	            stacked_bars.setOption(stacked_bars_options);
            	stacked_columns.setOption(stacked_columns_options);
	            // stacked_lines.setOption(stacked_lines_options);
            	basic_pie.setOption(basic_pie_options);



	            window.onresize = function () {
	                setTimeout(function (){
	                    stacked_bars.resize();
                    	stacked_columns.resize();
	                    // stacked_lines.resize();
	                    basic_pie.resize();

	                }, 200);
	            }
	        }
	    );
	});
