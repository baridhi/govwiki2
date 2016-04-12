var data = require('./glob.js').data;
var chart = require('./config.js').chart;

/**
 * Initialization
 */
function init() {
    financialHealth_one();
    financialHealth_two();
    financialHealth_three();
}

/**
 * public-safety-pie
 */
function financialHealth_one() {
    /**
     * Ahtung! Hardcode detected!
     * todo replace such bad code
     */
    if (! data['public_safety_exp_over_tot_gov_fund_revenue']) {
        data['public_safety_exp_over_tot_gov_fund_revenue'] = data['public_safety_expense_total_governmental_fund_revenue'];
    }

    if (! data['public_safety_exp_over_tot_gov_fund_revenue']) {
        return;
    }

    var chart, options, vis_data, container;
    vis_data = new google.visualization.DataTable();
    vis_data.addColumn('string', 'Public Safety Expense');
    vis_data.addColumn('number', 'Total');
    vis_data.addRows([['Public Safety Exp', 1 - data['public_safety_exp_over_tot_gov_fund_revenue']], ['Other', +data['public_safety_exp_over_tot_gov_fund_revenue']]]);


    container = 'public-safety-pie';
    options = {
        'title': 'Public safety expense',
        'titleTextStyle': {
            'fontSize': 12
        },
        'tooltip': {
            'textStyle': {
                'fontSize': 12
            }
        },
        'width': '100%',
        'height': '100%',
        'is3D': 'true',
        'colors': ['#005ce6', '#009933'],
        'slices': {
            1: {
                offset: 0.2
            }
        },
        'pieStartAngle': 45
    };

    var element = document.getElementById(container);

    if (element) {
        chart = new google.visualization.PieChart(element);
        chart.draw(vis_data, options);
    }
}

/**
 * fin-health-revenue-graph
 */
function financialHealth_two() {

    if (! data['total_revenue_per_capita']) {
        return;
    }

    var chart, options, vis_data, container;
    vis_data = new google.visualization.DataTable();
    vis_data.addColumn('string', 'Per Capita');
    vis_data.addColumn('number', 'Rev.');
    vis_data.addRows([['Total Revenue \n Per Capita', +data['total_revenue_per_capita']], ['Median Total \n Revenue Per \n Capita For All Cities', 420]]);


    container = 'fin-health-revenue-graph';
    options = {
        'title': 'Total Revenue',
        'titleTextStyle': {
            'fontSize': 12
        },
        'tooltip': {
            'textStyle': {
                'fontSize': 12
            }
        },
        'width': '100%',
        'height': '100%',
        'isStacked': 'true',
        'colors': ['#005ce6', '#009933'],
        'chartArea.width': '100%'
    };

    chart = new google.visualization.ColumnChart(document.getElementById(container));
    chart.draw(vis_data, options);
}

/**
 * fin-health-expenditures-graph
 */
function financialHealth_three() {

    if (! data['total_expenditures_per_capita']) {
        return;
    }

    var chart, options, vis_data, container;
    vis_data = new google.visualization.DataTable();
    vis_data.addColumn('string', 'Per Capita');
    vis_data.addColumn('number', 'Exp.');
    vis_data.addRows([['Total Expenditures \n Per Capita', +data['total_expenditures_per_capita']], ['Median Total \n Expenditures \n Per Capita \n For All Cities', 420]]);

    container = 'fin-health-expenditures-graph';
    options = {
        'title': 'Total Expenditures',
        'titleTextStyle': {
            'fontSize': 12
        },
        'tooltip': {
            'textStyle': {
                'fontSize': 12
            }
        },
        'width': '100%',
        'height': '100%',
        'isStacked': 'true',
        'colors': ['#005ce6', '#009933'],
        'chartArea.width': '100%'
    };

    chart = new google.visualization.ColumnChart(document.getElementById(container));
    chart.draw(vis_data, options);
}


module.exports = {
    init: init,
    initAll: init
};