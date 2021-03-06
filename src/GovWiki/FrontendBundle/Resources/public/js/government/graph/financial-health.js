var data = require('./glob.js').data;

/**
 * Initialization
 */
function init() {
  financialHealthFirstChart();
  financialHealthSecondChart();
  financialHealthThirdChart();
}

/**
 * public-safety-pie
 */
function financialHealthFirstChart() {
  var chart;
  var options;
  var visData;
  var container;
  var element;
  /**
   * Ahtung! Hardcode detected!
   * todo replace such bad code
   */
  if (!data.public_safety_exp_over_tot_gov_fund_revenue) {
    data.public_safety_exp_over_tot_gov_fund_revenue = data.public_safety_expense_total_governmental_fund_revenue;
  }

  if (!data.public_safety_exp_over_tot_gov_fund_revenue) {
    return;
  }

  visData = new google.visualization.DataTable();
  visData.addColumn('string', 'Public Safety Expense');
  visData.addColumn('number', 'Total');
  visData.addRows([
    ['Public Safety Exp', 1 - data.public_safety_exp_over_tot_gov_fund_revenue],
    ['Other', +data.public_safety_exp_over_tot_gov_fund_revenue]
  ]);

  container = 'public-safety-pie';
  options = {
    title: 'Public safety expense',
    titleTextStyle: {
      fontSize: 12
    },
    tooltip: {
      textStyle: {
        fontSize: 12
      }
    },
    width: 340,
    height: 300,
    is3D: 'true',
    colors: ['#005ce6', '#009933'],
    slices: {
      1: {
        offset: 0.2
      }
    },
    pieStartAngle: 45
  };

  element = document.getElementById(container);

  if (element) {
    chart = new google.visualization.PieChart(element);
    chart.draw(visData, options);
  }
}

/**
 * fin-health-revenue-graph
 */
function financialHealthSecondChart() {
  var chart;
  var options;
  var visData;
  var container;

  if (!data.total_revenue_per_capita) {
    return;
  }

  visData = new google.visualization.DataTable();
  visData.addColumn('string', 'Per Capita');
  visData.addColumn('number', 'Rev.');
  visData.addRows([
    ['Total Revenue \n Per Capita', +data.total_revenue_per_capita],
    ['Median Total \n Revenue Per \n Capita For All Cities', 420]
  ]);

  container = 'fin-health-revenue-graph';
  options = {
    title: 'Total Revenue',
    titleTextStyle: {
      fontSize: 12
    },
    tooltip: {
      textStyle: {
        fontSize: 12
      }
    },
    width: 340,
    height: 300,
    isStacked: true,
    colors: ['#005ce6', '#009933'],
    'chartArea.width': '100%'
  };

  chart = new google.visualization.ColumnChart(document.getElementById(container));
  chart.draw(visData, options);
}

/**
 * fin-health-expenditures-graph
 */
function financialHealthThirdChart() {
  var chart;
  var options;
  var visData;
  var container;

  if (!data.total_expenditures_per_capita) {
    return;
  }

  visData = new google.visualization.DataTable();
  visData.addColumn('string', 'Per Capita');
  visData.addColumn('number', 'Exp.');
  visData.addRows([
    ['Total Expenditures \n Per Capita', +data.total_expenditures_per_capita],
    ['Median Total \n Expenditures \n Per Capita \n For All Cities', 420]
  ]);

  container = 'fin-health-expenditures-graph';
  options = {
    title: 'Total Expenditures',
    titleTextStyle: {
      fontSize: 12
    },
    tooltip: {
      textStyle: {
        fontSize: 12
      }
    },
    width: 340,
    height: 300,
    isStacked: 'true',
    colors: ['#005ce6', '#009933'],
    'chartArea.width': '100%'
  };

  chart = new google.visualization.ColumnChart(document.getElementById(container));
  chart.draw(visData, options);
}


module.exports = {
  init: init,
  initAll: init
};
