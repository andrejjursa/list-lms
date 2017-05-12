var refresh_points_overview = function() {
    var targetID = '#valuationCharts';
    var formID = '#filter_form_id';
    if (arguments.length >= 1) {
        targetID = arguments[0];
    }
    if (arguments.length >= 2) {
        formID = arguments[1];
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
            var chartsdata = create_histogram(data);

            Highcharts.chart('valuationCharts', {
                chart: {
                    type: 'column'
                },
                title: {
                    text: chartmessages.chartTitle
                },
                xAxis: {
                    categories: chartsdata.labels,
                    title: {
                        text: chartmessages.xAxis
                    }
                },
                yAxis: {
                    title: {
                        text: chartmessages.yAxis
                    }
                },
                series: [
                    {
                        name: chartmessages.yAxis,
                        data: chartsdata.y
                    }
                ]
            });
        }
    });
};

var create_histogram = function(data) {
    var histogram = {
        'x': [],
        'labels': [],
        'y': []
    };
    var dataXMin = Infinity;
    var dataXMax = -Infinity;
    var haveData = false;
    for (var x in data) {
        haveData = true;
        var nx = parseFloat(x);
        dataXMax = Math.ceil(Math.max(dataXMax, nx));
        dataXMin = Math.floor(Math.min(dataXMin, nx));
    }
    if (!haveData) {
        return histogram;
    }

    var diff = dataXMax - dataXMin;
    var bars = Math.max(Math.ceil(diff) * 2, 1);
    var barsWidth = (diff / bars).toFixed(3);

    var barsObject = {};

    for (var b = 0; b < bars; b++) {
        var bmin = b * barsWidth + dataXMin;
        var bmax = (b + 1) * barsWidth + dataXMin;
        if (b + 1 == bars) { bmax = dataXMax; }
        var bavg = (bmin + bmax) / 2;
        barsObject[bavg] = {
            'bmin': bmin.toFixed(3),
            'bmax': bmax.toFixed(3),
            'count': 0,
        };
        for (var x in data) {
            var nx = parseFloat(x);
            if (nx >= bmin && ((b == bars - 1 && nx <= bmax) || (b < bars - 1 && nx < bmax))) {
                barsObject[bavg].count += parseInt(data[x]);
            }
        }
    }

    var xs = [];
    var labels = [];
    var ys = [];

    for (var bavg in barsObject) {
        xs.push(bavg);
        labels.push(barsObject[bavg].bmin + ' ' + chartmessages.to + ' ' + barsObject[bavg].bmax);
        ys.push(barsObject[bavg].count);
    }

    histogram.x = xs;
    histogram.labels = labels;
    histogram.y = ys;

    return histogram;
};