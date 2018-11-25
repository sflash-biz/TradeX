
<script src="js/jquery-3.min.js"></script>
<script src="js/highstock.js"></script>
<script src="js/exporting.js"></script>

<style>
    .highcharts-navigator-mask-inside {
        cursor:pointer !important
    }
</style>


<?php if (empty($_REQUEST['domain'])) { ?>
<div class="block-center d-none margin-top-10px" id="summary-in-clicks-out" style="border:width: 850px; height: 400px;"></div>

    <!-- <script src="index.php?r=_xGraphDataHistoryStats&date_range=0 day,-120 days&from_month=01&from_day=01&from_year=2017&to_month=01&to_day=01&to_year=2017&breakdown=%241-%242-%243&?rndstr="></script> -->
<script src="index.php?r=_xGraphDataHistoryStats&date_range=-1%20day%2C-120%20days&breakdown=%241-%242-%243&rndstr=<?=time()?>"></script>
<script>
    Highcharts.stockChart('summary-in-clicks-out', {

        title: {
            text: 'Summary daily history for last 3 month'
        },

        chart: {
            alignTicks: false
        },

        rangeSelector: {
            selected: 0
        },

        legend: {
            enabled: true,
            layout: 'horizontal'

        },

        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Hits\\Clicks',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            opposite: false
        }, { // Secondary yAxis
            title: {
                text: 'Prod\\Return',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} %',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],

        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },

        series: [{
            type: 'column',
            pointPlacement: 0.15,
            name: 'In',
            data: data[0],
            stack: 'in'

        }, {
            type: 'column',
            name: 'Uniq In',
            data: data[1],
            stack: 'suniq_in'

        }, {
            type: 'column',
            name: 'Clicks',
            data: data[2],
            stack: 'clicks'

        }, {
            type: 'column',
            name: 'Out',
            data: data[3],
            stack: 'out'

        }, {
            name: 'Prod',
            yAxis: 1,
            data: data[4],
            stack: 'prod'

        }, {
            name: 'Return',
            yAxis: 1,
            data: data[5],
            stack: 'return'

        }]
    });
</script>



<?php } else { ?>


    <?php if (empty($_REQUEST['hourly'])) { ?>

<div class="block-center d-none margin-top-10px" id="<?=$_REQUEST['domain']?>-in-clicks-out" style="border:width: 850px; height: 400px;"></div>

<script src="index.php?r=_xTradesGraphDataHistoryStats&domain=<?=$_REQUEST['domain']?>&date_range=-1%20day%2C-120%20days&breakdown=%241-%242-%243&rndstr=<?=time()?>"></script>
<script>
    Highcharts.stockChart('<?=$_REQUEST['domain']?>-in-clicks-out', {

        title: {
            text: '<strong><?=$_REQUEST['domain']?></strong> daily history for last 3 month'
        },

        chart: {
            alignTicks: false
        },

        rangeSelector: {
            selected: 0
        },

        legend: {
            enabled: true,
            layout: 'horizontal'

        },

        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'Hits\\Clicks',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            opposite: false
        }, { // Secondary yAxis
            title: {
                text: 'Prod\\Return',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value} %',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],

        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },

        series: [{
            type: 'column',
            pointPlacement: 0.15,
            name: 'In',
            data: data[0],
            stack: 'in'

        }, {
            type: 'column',
            name: 'Uniq In',
            data: data[1],
            stack: 'suniq_in'

        }, {
            type: 'column',
            name: 'Clicks',
            data: data[2],
            stack: 'clicks'

        }, {
            type: 'column',
            name: 'Out',
            data: data[3],
            stack: 'out'

        }, {
            name: 'Prod',
            yAxis: 1,
            data: data[4],
            stack: 'prod'

        }, {
            name: 'Return',
            yAxis: 1,
            data: data[5],
            stack: 'return'

        }]
    });
</script>

    <?php } else { ?>

        <div class="block-center d-none margin-top-10px" id="hourly-<?=$_REQUEST['domain']?>-in-clicks-out" style="border:width: 850px; height: 400px;"></div>

        <script src="index.php?r=_xTradesGraphDataHourly&domain=<?=$_REQUEST['domain']?>&rndstr=<?=time()?>"></script>
        <script>
            Highcharts.stockChart('hourly-<?=$_REQUEST['domain']?>-in-clicks-out', {

                title: {
                    text: '<strong><?=$_REQUEST['domain']?></strong> hourly stats'
                },

                chart: {
                    alignTicks: false
                },

                rangeSelector: {
                    selected: 4,
                    inputEnabled: false,
                    buttonTheme: {
                        visibility: 'hidden'
                    },
                    labelStyle: {
                        visibility: 'hidden'
                    }
                },

                navigator: {
                    enabled: false
                },

                legend: {
                    enabled: true,
                    layout: 'horizontal'

                },

                yAxis: [{ // Primary yAxis
                    labels: {
                        format: '{value}',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    title: {
                        text: 'Hits\\Clicks',
                        style: {
                            color: Highcharts.getOptions().colors[1]
                        }
                    },
                    opposite: false
                }, { // Secondary yAxis
                    title: {
                        text: 'Prod\\Return',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    labels: {
                        format: '{value} %',
                        style: {
                            color: Highcharts.getOptions().colors[0]
                        }
                    },
                    opposite: true
                }],

                plotOptions: {
                    column: {
                        stacking: 'normal'
                    }
                },

                series: [{
                    type: 'column',
                    pointPlacement: 0.15,
                    name: 'In',
                    data: data[0],
                    stack: 'in'

                }, {
                    type: 'column',
                    name: 'Clicks',
                    data: data[2],
                    stack: 'clicks'

                }, {
                    type: 'column',
                    name: 'Out',
                    data: data[3],
                    stack: 'out'

                }, {
                    name: 'Prod',
                    yAxis: 1,
                    data: data[4],
                    stack: 'prod'

                }, {
                    name: 'Return',
                    yAxis: 1,
                    data: data[5],
                    stack: 'return'

                }]
            });
        </script>

    <?php } ?>
<?php } ?>