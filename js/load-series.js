/**
 *
 * @filesource
 * @author Stefan Herndler
 * @since 1.0.1 11.10.14 21:32
 */

function Competition_load_line_series(p_str_Name, p_str_Type, p_obj_Chart) {
    jQuery.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data: {
            action: 'Competition_loadLineSeries',
            name: p_str_Name,
            type: p_str_Type
        },
        dataType: 'json',
        success: function (json, textStatus, XMLHttpRequest) {
            if (json == null) {
                return;
            }
            var series = { id: 'series', name: p_str_Name, data: [] };
            jQuery.each(json, function(date,value) {
                var xval = date.split("-");
                var x = Date.UTC(xval[0], xval[1] - 1, xval[2]);
                series.data.push([x, parseInt(value)]);
            });
            p_obj_Chart.addSeries(series);
        },
        error: function (MLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        }
    });
}

function Competition_load_pie_series(p_str_Name, p_str_Type, p_str_PreSelect, p_obj_Chart) {
    jQuery.ajax({
        type: 'POST',
        url: '/wp-admin/admin-ajax.php',
        data: {
            action: 'Competition_loadPieSeries',
            name: p_str_Name,
            type: p_str_Type
        },
        dataType: 'json',
        success: function (json, textStatus, XMLHttpRequest) {
            if (json == null) {
                return;
            }
            var l_obj_NewPoint = null;
            if (p_str_PreSelect == "select") {
                l_obj_NewPoint = {name: p_str_Name, y: parseInt(json.downloads), sliced: true, selected: true};
            } else {
                l_obj_NewPoint = {name: p_str_Name, y: parseInt(json.downloads)};
            }
            p_obj_Chart.series[0].addPoint(l_obj_NewPoint);
        },
        error: function (MLHttpRequest, textStatus, errorThrown) {
            console.log(textStatus);
        }
    });
}