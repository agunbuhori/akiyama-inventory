function typeDetail(evt) 
{
    var activeElement = chart.getElementAtEvent(evt);

    chart = new Chart(canv, basic_bars_options);

    basic_bars_options.data.datasets[activeElement[0]._datasetIndex].data[activeElement[0]._index];
}

$(function () {

    // Set paths
    // ------------------------------

    require.config({
        paths: {
            echarts: 'assets/js/plugins/visualization/echarts'
        }
    });


    // Configuration
    // ------------------------------

    require(
        [
            'echarts',
            'echarts/theme/limitless',
            'echarts/chart/bar',
            'echarts/chart/line',
            'echarts/chart/pie'
        ],


        // Charts setup
        function (ec, limitless) {

            var basic_bars = ec.init(document.getElementById('basic_bars'), limitless);
            var basic_pie = ec.init(document.getElementById('basic_pie'), limitless);
            var stacked_columns = ec.init(document.getElementById('stacked_columns'), limitless);

            basic_bars_options = {

                // Setup grid
                grid: {
                    x: 75,
                    x2: 35,
                    y: 35,
                    y2: 25
                },

                // Add tooltip
                tooltip: {
                    trigger: 'item',
                    axisPointer: {
                        type: 'shadow'
                    }
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
                    data: ['タイヤ', 'バッテリー', 'ホイール', 'オイル']
                }],

                // Add series
                series: [
                    {
                        type: 'bar',
                        data: graph_bar_store["type"]
                    }
                ],

                
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
                    orient: 'vertical',
                    x: 'right',
                    data: graph_pie_store,
                    display: false
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
                    data: graph_pie_store
                }]

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
                    },
                    formatter: function (a, b, c) {
                        return currency(a[2]) + " (" + graph_column_store[0].data_amount[a[7]] + ")";
                    }
                    // formatter: "{b}: {c} ("++")"
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
                series: graph_column_store
            };

            // Apply options
            // ------------------------------

            basic_bars.setOption(basic_bars_options);
            basic_pie.setOption(basic_pie_options);
            stacked_columns.setOption(stacked_columns_options);

            // Resize charts
            // ------------------------------

            window.onresize = function () {
                setTimeout(function (){
                    stacked_columns.resize();
                    basic_bars.resize();
                    basic_pie.resize();
                }, 200);
            }
        }
    );
});