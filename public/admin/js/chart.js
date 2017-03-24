$(document).ready(function(){
    
    charts.initChartist();

    var number = parseInt($('.ct-data').attr('data-number'));
    var charts = $('.ct-data').attr('data-content');
    var obj = jQuery.parseJSON(charts);

    for (i = 0; i < obj.length; i++) {
        var dataInput = new Array();

        for (j = 0; j < obj[i]['chart'].length; j++) {
            dataInput.push([obj[i]['chart'][j]['answer'], obj[i]['chart'][j]['percent']])
        }

        if(dataInput.length != 1) {
            Highcharts.chart('container' + i, {
                chart: {
                    type: 'pie',
                    options3d: {
                        enabled: true,
                        alpha: 45,
                        beta: 0
                    }
                },
                title: {
                    text: obj[i]['question']['content']
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        depth: 35,
                        dataLabels: {
                            enabled: true,
                            format: '{point.name}'
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'View answers of survey',
                    data: dataInput
                }]
            });
        }
    }
});

//# sourceMappingURL=chart.js.map
