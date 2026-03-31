function drawRegionsMap(selector) {
    var element = selector[0];

    var valueName = selector.attr("back-name");
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Country');
    data.addColumn('number', valueName);
    data.addColumn('string', 'CountryName');
    data.addRows(selector.data("country"));

    var formatter = new google.visualization.PatternFormat('{0}');
    formatter.format(data, [0, 1], 0);
    var formatter = new google.visualization.PatternFormat('{2}');
    formatter.format(data, [0, 1, 2], 0);

    var view = new google.visualization.DataView(data);
    view.setColumns([0, 1]);
    var geoChart = new google.visualization.GeoChart(element);
    geoChart.draw(view, {
        colorAxis: {
            colors: ['#d3decc', '#bed5b0', '#a8cb95', '#93c279', '#7eb85d']
        },
        datalessRegionColor: '#e8e8e8',
        legend: 'none',
        tooltip: {
            textStyle: {
                color: '#4a4a4a'
            }
        }
    });
}


$(window).load(function () {

    google.charts.load('current', {
        'packages': ['corechart', 'geochart']
    });
    google.charts.setOnLoadCallback(draw);

    function draw() {
        $("div.regions-map").each(function () {
            drawRegionsMap($(this));
        });
        if (typeof afterDraw === 'function')
            afterDraw();
    }

});
;
