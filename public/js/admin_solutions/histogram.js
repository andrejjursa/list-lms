var refresh_points_overview = function() {
    var step = 0.5;
    var targetID = '#valuationCharts';
    var formID = '#filter_form_id';
    if (arguments.length >= 1) {
        step = arguments[0];
    }
    if (arguments.length >= 2) {
        targetID = arguments[1];
    }
    if (arguments.length >= 3) {
        formID = arguments[2];
    }
    var data = jQuery(formID).serializeArray();
    var url = global_base_url + 'index.php/admin_solutions/get_points_overview/' + task_set_id;
    jQuery.ajax(url, {
        method: 'post',
        cache: false,
        dataType: 'json',
        data: data,
        success: function(data) {
            $(targetID).html('');
            var statistics = compute_statistics(data, step);
            var additionalData = prepare_graph_statistical_data(statistics);

            Highcharts.chart('valuationCharts', {
                title: {
                    text: chartmessages.chartTitle
                },
                subtitle: {
                    text: chartmessages.subtitle
                },
                tooltip: {
                    useHTML: true
                },
                plotOptions: {
                    marker: { enabled: false }
                },
                xAxis: {
                    title: {
                        text: chartmessages.xAxis
                    },
                    plotLines: additionalData.plotLines,
                    plotBands: additionalData.plotBands
                },
                yAxis: {
                    title: {
                        text: chartmessages.yAxis
                    }
                },
                series: [
                    {
                        name: chartmessages.yAxis,
                        type: 'column',
                        data: histogram(data, step),
                        pointPlacement: 'between',
                        pointPadding: 0,
                        groupPadding: 0,
                        color: 'rgba(0,192,255,0.8)',
                        tooltip: {
                            headerFormat: '',
                            pointFormatter: function () {
                                return '<table><tr><td style="padding: 0.1em;"><span style="color: ' + this.color + '">\u25CF</span> ' + chartmessages.range + ':</td><td style="padding: 0.1em;"><strong>' + this.x + ' - ' + (this.x + step) + '</strong></td></tr><tr><td style="padding: 0.1em;"><span style="color: ' + this.color + '">\u25CF</span> ' + chartmessages.sum + ':</td><td style="padding: 0.1em;"><strong>' + this.y + '</strong></td></tr></table>';
                            }
                        }
                    },
                    {
                        name: chartmessages.pointseries.name,
                        color: 'red',
                        type: 'scatter',
                        data: pointdata(data),
                        tooltip: {
                            headerFormat: '',
                            pointFormatter: function() {
                                return '<table><tr><td style="padding: 0.1em;"><span style="color: ' + this.color + '">\u25CF</span> ' + chartmessages.pointseries.x + ':</td><td style="padding: 0.1em;"><strong>' + this.x + '</strong></td></tr><tr><td style="padding: 0.1em;"><span style="color: ' + this.color + '">\u25CF</span> ' + chartmessages.pointseries.y + ':</td><td style="padding: 0.1em;"><strong>' + this.y + '</strong></td></tr></table>';
                            }
                        }
                    }
                ]
            });
        }
    });
};

function pointdata(data) {
    var pointdata = [];

    for (var x in data) {
        pointdata.push([parseFloat(x), parseFloat(data[x])]);
    }

    return pointdata;
}

function histogram(data, step) {
    var histo = {},
        arr = [];

    // Group down
    for (var i in data) {
        var x = Math.floor(parseFloat(i) / step) * step;
        if (!histo[x]) {
            histo[x] = 0;
        }
        histo[x]+=data[i];
    }

    // Make the histo group into an array
    for (var x in histo) {
        if (histo.hasOwnProperty((x))) {
            arr.push([parseFloat(x), histo[x]]);
        }
    }

    // Finally, sort the array
    arr.sort(function (a, b) {
        return a[0] - b[0];
    });

    return arr;
}

var compute_statistics = function (data, step) {
    var output = {
        mean: 0,
        sd: 0
    };

    if (data.length == 0) {
        return output;
    }

    var sum = 0, count = 0;
    for (var x in data) {
        var nx = parseFloat(x);
        count += parseInt(data[x]);
        sum += nx * parseInt(data[x]);
    }
    output.mean = sum / count;

    var variances = 0;
    for (var x in data) {
        var nx = parseFloat(x);
        for (var i = 0; i < parseInt(data[x]); i++) {
            variances += (nx - output.mean) * (nx - output.mean);
        }
    }
    output.sd = Math.sqrt(variances / count);

    return output;
};

var prepare_graph_statistical_data = function(statistics) {
    var output = {
        plotLines: [],
        plotBands: []
    };

    if (statistics.min > statistics.max) {
        return output;
    }

    output.plotLines = [
        {value: statistics.mean.toFixed(3), width: 2, color: '#666', zIndex: 1, dashStyle: 'Dash', label: {
            text: 'm', rotation: 0, align: 'center', x: 0, y: -5, style: {fontSize: '10px'}
        }}
    ];

    if (statistics.sd > 0) {
        for (var i = -3; i <= 3; i++) {
            if (i != 0) {
                var s = parseFloat((statistics.sd * i).toFixed(3)) + parseFloat(statistics.mean.toFixed(3));
                var plotLine = {
                    value: s.toFixed(3), width: 1, color: '#999', zIndex: 1, dashStyle: 'Dash', label: {
                        text: i + 's', rotation: 0, align: 'center', x: 0, y: -5, style: {fontSize: '10px'}
                    }
                };
                output.plotLines.push(plotLine);
            }
        }

        for (var i = 1; i <= 3; i++) {
            var sp = parseFloat((statistics.sd * i).toFixed(3)) + parseFloat(statistics.mean.toFixed(3));
            var sm = parseFloat((statistics.sd * (-i)).toFixed(3)) + parseFloat(statistics.mean.toFixed(3));
            var plotBand = {
                from: sm.toFixed(3), to: sp.toFixed(3), color: 'rgba(184,210,236,.2)', zIndex: 0
            };
            output.plotBands.push(plotBand);
        }
    }


    return output;
};