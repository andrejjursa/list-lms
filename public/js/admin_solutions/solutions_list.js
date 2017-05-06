jQuery(document).ready(function($) {
    
    make_switchable_form('#new_solution_form_id');
    make_filter_form('#filter_form_id');
    
    var url_anchor = api_read_url_anchor();
    if (url_anchor.substring(0, 6) === 'group_') {
        var group_id = url_anchor.substring(6);
        var regex = /^[1-9][0-9]*$/;
        if (regex.test(group_id)) {
            $('#filter_group_id option[value="' + group_id + '"]').prop('selected', true);
        }
    }
    
    var refresh_all_solutions = function() {
        var data = $('#filter_form_id').serializeArray();
        var url = global_base_url + 'index.php/admin_solutions/get_solutions_list_for_task_set/' + task_set_id;
        var target = '#table_content_id';
        api_ajax_load(url, target, 'post', data);
    };

    var refresh_points_overview = function() {
        var data = $('#filter_form_id').serializeArray();
        var url = global_base_url + 'index.php/admin_solutions/get_points_overview/' + task_set_id;
        $.ajax(url, {
            method: 'post',
            cache: false,
            dataType: 'json',
            data: data,
            success: function(data) {
                $('#valuationCharts').html('');
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
    
    $('#filter_form_id').submit(function(event) {
        event.preventDefault();
        refresh_all_solutions();
        refresh_points_overview();
    });

    var create_histogram = function(data) {
        var histogram = {
            'x': [],
            'labels': [],
            'y': []
        };
        var dataXMin = 0;
        var dataXMax = -Infinity;
        var haveData = false;
        for (var x in data) {
            haveData = true;
            var nx = parseFloat(x);
            dataXMax = Math.max(dataXMax, nx);
            dataXMin = Math.min(dataXMin, nx);
        }
        if (!haveData) {
            return histogram;
        }

        var diff = dataXMax - dataXMin;
        var bars = Math.ceil(diff) * 2;
        var barsWidth = diff / bars;

        var barsObject = {};

        for (var b = 0; b < bars; b++) {
            var bmin = b * barsWidth + dataXMin;
            var bmax = (b + 1) * barsWidth + dataXMin;
            var bavg = (bmin + bmax) / 2;
            barsObject[bavg] = {
                'bmin': bmin,
                'bmax': bmax,
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
            labels.push(barsObject[bavg].bmin + ' - ' + barsObject[bavg].bmax);
            ys.push(barsObject[bavg].count);
        }

        histogram.x = xs;
        histogram.labels = labels;
        histogram.y = ys;

        return histogram;
    };
    
    refresh_all_solutions();
    refresh_points_overview();
    
    $('#new_solution_form_id').submit(function(event) {
        event.preventDefault();
        var url = $(this).attr('action');
        var data = $(this).serializeArray();
        var success = function() {
            if ($('#new_solution_form_id .flash_message.message_success').length > 0) {
                refresh_all_solutions();
                refresh_points_overview();
            }
            $('#new_solution_form_id').formErrorWarning();
            var last_created_id = $('#new_solution_form_id input[type=hidden][name=last_created_solution_id]');
            if (last_created_id.length > 0 && last_created_id.val() > 0) {
                open_upload_dialog(global_base_url + 'index.php/admin_solutions/student_solution_upload/' + last_created_id.val());
            }
        };
        api_ajax_load(url, '#new_solution_form_id', 'post', data, success);
    });
    
    $(document).on('click', '#table_content_id a.open_valuation_dialog, #table_content_id a.open_upload_dialog', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');
        open_upload_dialog(url);
    });
    
    var open_upload_dialog = function(url) {
        $.fancybox(url, {
            type: 'iframe',
            width: '100%',
            height: '100%',
            autoSize: false,
            autoHeight: false,
            autoWidth: false,
            helpers: {
                overlay: {
                    css: {
                        background: 'rgba(255,255,255,0)'
                    }
                }
            },
            beforeClose: function() {
                refresh_all_solutions();
                refresh_points_overview();
                return true;
            }
        });
    };
    
});