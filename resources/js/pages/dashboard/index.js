// Class definition
var Dashboard = function() {
    // Private variables
    var chart;
    var chart_element = document.getElementById("widget_dashboard_chart");
    var chart_options;
    var chartMonths = $('.chartMonths');
    var months = 6;

    // Private functions
    var widgetDashboard = function () {
        // var element = document.getElementById("widget_dashboard_chart");
        var height = parseInt(KTUtil.css(chart_element, 'height'));

        if (!chart_element) {
            return;
        }

        var strokeColor = '#007bff';
        var depaColor = '#1BC5BD';
        var restantColor = '#007bff';

        chart_options = {
            series: [],
            chart: {
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                },
                sparkline: {
                    enabled: true
                },
                dropShadow: {
                    enabled: true,
                    enabledOnSeries: undefined,
                    top: 5,
                    left: 0,
                    blur: 3,
                    color: strokeColor,
                    opacity: 0.5
                }
            },
            plotOptions: {},
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: 'solid',
                opacity: 0
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [depaColor, restantColor]
            },
            xaxis: {
                categories: null,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    show: false,
                    style: {
                        colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                        fontSize: '12px',
                        fontFamily: KTApp.getSettings()['font-family']
                    }
                },
                crosshairs: {
                    show: false,
                    position: 'front',
                    stroke: {
                        color: KTApp.getSettings()['colors']['gray']['gray-300'],
                        width: 1,
                        dashArray: 3
                    }
                }
            },
            yaxis: {
                min: 0,
                max: 0,
                labels: {
                    show: false,
                    style: {
                        colors: KTApp.getSettings()['colors']['gray']['gray-500'],
                        fontSize: '12px',
                        fontFamily: KTApp.getSettings()['font-family']
                    }
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px',
                    fontFamily: KTApp.getSettings()['font-family']
                },
                y: {
                    formatter: function (val) {
                        // return "$" + val + " thousands"
                        return val
                    }
                },
                marker: {
                    show: false
                }
            },
            colors: ['transparent'],
            markers: {
                colors: [KTApp.getSettings()['colors']['theme']['light']['danger']],
                strokeColor: [strokeColor],
                strokeWidth: 3
            }
        };

        chart = new ApexCharts(chart_element, chart_options);
        chart.render();

        widgetDashboardChartUpdate();
    }

    var widgetDashboardChartUpdate = function () {
      $.ajax({
          url: "/dashboard/widget_dashboard",
          type: "POST",
          cache: false,
          datatype: 'JSON',
          data: {
            "months" : months
          },
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response, status, xhr, $form) {
            chart.updateOptions({
              xaxis: {
                categories: response.categories
              },
              yaxis: {
                min: response.min,
                max: response.max + 1,
                labels: {
                    show: false
                }
              }
            });
            chart.updateSeries(response.series);
          },
          error: function (response)
          {
              var e = '<b>' + String(response.responseJSON.message) + '</b><br>';
              for (var err in response.responseJSON.errors) {
                if (response.responseJSON.errors.hasOwnProperty(err)) {
                  e += response.responseJSON.errors[err] + '<br>';
                }
              }
              console.log(e);
          }
      });
    }

    var widgetDashboardMonths = function() {
      chartMonths.on('click', function(e){
        e.preventDefault();
        chartMonths.removeClass('bg-gray-200 active');
        $(this).addClass('bg-gray-200 active');
        months = $(this).data('months');
        widgetDashboardChartUpdate();
      })
    };

    return {
        // public functions
        init: function() {
            widgetDashboard();
            widgetDashboardMonths();
            // widgetDashboardChart();
        }
    };
}();

jQuery(document).ready(function() {
    Dashboard.init();
});
