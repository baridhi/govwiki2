var employeeCompensationGraphs = require('./graph/employee-compensation');
var financialHealthGraphs = require('./graph/financial-health');
var financialStatementGraphs = require('./graph/financial-statement');

/**
 * Initialization
 */
function init(callback) {
  return function cb() {
    handlerOnTabSwitch();
    callback();
  };
}

/**
 * (Handler)
 * On tab switch
 */
function handlerOnTabSwitch() {
  $('a[data-toggle="tab"]').on('shown.bs.tab', function shownTab(e) {
    var tabname = $(e.target).attr('data-tabname');

        /**
         * Init graphs
         */
    switch (tabname) {
      case 'Quality of Services':
        break;
      case 'Employee Compensation':
        employeeCompensationGraphs.initAll();
        break;
      case 'Financial Health':
        financialHealthGraphs.initAll();
        break;
      case 'Financial Financial_Statements':
        financialStatementGraphs.initAll();
        break;
      default:
    }
  });
}

function forceInit() {
  employeeCompensationGraphs.initAll();
  financialHealthGraphs.initAll();
  financialStatementGraphs.initAll();
}

function initGoogleViz(callback) {
  google.load('visualization', '1.0', {
    packages: ['treemap', 'corechart'],
    callback: init(callback)
  });
}

module.exports = {
  init: initGoogleViz,
  forceInit: forceInit
};
