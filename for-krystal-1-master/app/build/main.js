require('source-map-support/register')
module.exports =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./server/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./nuxt.config.js":
/*!************************!*\
  !*** ./nuxt.config.js ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

const nodeExternals = __webpack_require__(/*! webpack-node-externals */ "webpack-node-externals");

module.exports = {
  /*
  ** Headers of the page
  */
  head: {
    title: 'starter',
    meta: [{
      charset: 'utf-8'
    }, {
      name: 'viewport',
      content: 'width=device-width, initial-scale=1'
    }, {
      hid: 'description',
      name: 'description',
      content: 'Nuxt.js project'
    }],
    link: [{
      rel: 'icon',
      type: 'image/x-icon',
      href: '/favicon.ico'
    }, {
      rel: 'stylesheet',
      href: 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700|Material+Icons'
    }]
  },
  loading: {
    color: '#73DC50',
    failedColor: '#F90000'
  },
  css: [{
    src: '~assets/css/style.styl',
    lang: 'styl'
  }],
  plugins: [{
    src: '~plugins/i18n.js',
    injectAs: 'i18n'
  }, {
    src: '~plugins/vuetify.js'
  }, {
    src: '~plugins/toast.js',
    ssr: false
  }, // { src: '~plugins/currency.js', ssr: false },
  {
    src: '~filters/currency.js',
    ssr: false
  }],
  build: {
    vendor: ['axios', 'vue-i18n'],

    /*
    ** Run ESLINT on save
    */
    transpile: [/^vue-echarts/, /\/node_modules\/resize-detector\//],

    extend(config, ctx) {
      if (ctx.isDev && process.isClient) {
        config.module.rules.push({
          enforce: 'pre',
          test: /\.(js|vue)$/,
          loader: 'eslint-loader',
          exclude: /(node_modules)/
        });
      }

      if (ctx.isServer) {
        config.externals = [nodeExternals({
          // default value for `whitelist` is
          // [/es6-promise|\.(?!(?:js|json)$).{1,5}$/i]
          whitelist: [/es6-promise|\.(?!(?:js|json)$).{1,5}$/i, /^vue-echarts/]
        })];
      }
    }

  }
};

/***/ }),

/***/ "./server/api/account.js":
/*!*******************************!*\
  !*** ./server/api/account.js ***!
  \*******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! express */ "express");
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(express__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var express_validation__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! express-validation */ "express-validation");
/* harmony import */ var express_validation__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(express_validation__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _fixtures_linkAccounts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../fixtures/linkAccounts */ "./server/fixtures/linkAccounts.js");
/* harmony import */ var _lib_validation__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ../lib/validation */ "./server/lib/validation.js");




/* import request from 'request-promise'
import { server } from '../server.config'
import { parseToQuery } from '../lib/utils' */

const router = Object(express__WEBPACK_IMPORTED_MODULE_0__["Router"])();
router.get('/', (req, res, next) => {
  res.json(_fixtures_linkAccounts__WEBPACK_IMPORTED_MODULE_2__["accounts"]);
});
router.post('/', express_validation__WEBPACK_IMPORTED_MODULE_1___default()(_lib_validation__WEBPACK_IMPORTED_MODULE_3__["linkAccount"]), (req, res, next) => {
  const {
    password,
    username,
    companyId,
    linkedAccount
  } = req.body;
  const dataAccount = {
    'company_id': companyId,
    'password': password,
    'username': username,
    'linkedaccount': {
      'linkedaccount_username': linkedAccount.username,
      'linkedaccount_platform_display_name': linkedAccount.account,
      'linkedaccount_alias': linkedAccount.alias
    }
  };
  console.log(dataAccount);
  res.json(_fixtures_linkAccounts__WEBPACK_IMPORTED_MODULE_2__["created"]);
});
router.post('/pre-check', express_validation__WEBPACK_IMPORTED_MODULE_1___default()(_lib_validation__WEBPACK_IMPORTED_MODULE_3__["linkAccountPreCheck"]), (req, res, next) => {
  const {
    password,
    username,
    companyId
  } = req.body;
  const dataAccount = {
    'linkedaccount_password': password,
    'linkedaccount_username': username,
    'linkedaccount_company_id': companyId
  };
  console.log(dataAccount);
  res.json(_fixtures_linkAccounts__WEBPACK_IMPORTED_MODULE_2__["preChecks"]);
});
/* harmony default export */ __webpack_exports__["default"] = (router);

/***/ }),

/***/ "./server/api/company.js":
/*!*******************************!*\
  !*** ./server/api/company.js ***!
  \*******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! express */ "express");
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(express__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _fixtures_companies__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../fixtures/companies */ "./server/fixtures/companies.js");


/* import request from 'request-promise'
import { server } from '../server.config'
import { parseToQuery } from '../lib/utils' */

const router = Object(express__WEBPACK_IMPORTED_MODULE_0__["Router"])();
router.get('/lending', (req, res, next) => {
  res.json(_fixtures_companies__WEBPACK_IMPORTED_MODULE_1__["companies"]);
});
router.get('/marketplace', (req, res, next) => {
  res.json(_fixtures_companies__WEBPACK_IMPORTED_MODULE_1__["marketplace"]);
});
router.get('/marketplace/:id', async (req, res, next) => {
  res.json(Object(_fixtures_companies__WEBPACK_IMPORTED_MODULE_1__["getCompany"])(req.params.id));
});
/* harmony default export */ __webpack_exports__["default"] = (router);

/***/ }),

/***/ "./server/api/dashboard.js":
/*!*********************************!*\
  !*** ./server/api/dashboard.js ***!
  \*********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! express */ "express");
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(express__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _fixtures_globalDashboard__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../fixtures/globalDashboard */ "./server/fixtures/globalDashboard.js");
/* harmony import */ var _fixtures_charts__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../fixtures/charts */ "./server/fixtures/charts.js");



/* import request from 'request-promise'
import { server } from '../server.config'
import { parseToQuery } from '../lib/utils' */

const router = Object(express__WEBPACK_IMPORTED_MODULE_0__["Router"])();
router.get('/', (req, res, next) => {
  res.json(_fixtures_globalDashboard__WEBPACK_IMPORTED_MODULE_1__["general"]);
});
router.get('/chart/payment', (req, res, next) => {
  res.json(_fixtures_charts__WEBPACK_IMPORTED_MODULE_2__["paymentDelay"]);
});
router.get('/chart/current', (req, res, next) => {
  res.json(_fixtures_charts__WEBPACK_IMPORTED_MODULE_2__["current"]);
});
router.get('/chart/exposure', (req, res, next) => {
  res.json(_fixtures_charts__WEBPACK_IMPORTED_MODULE_2__["exposure"]);
});
/* harmony default export */ __webpack_exports__["default"] = (router);

/***/ }),

/***/ "./server/api/index.js":
/*!*****************************!*\
  !*** ./server/api/index.js ***!
  \*****************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! express */ "express");
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(express__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _company__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./company */ "./server/api/company.js");
/* harmony import */ var _dashboard__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./dashboard */ "./server/api/dashboard.js");
/* harmony import */ var _account__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./account */ "./server/api/account.js");
/* harmony import */ var _polling__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./polling */ "./server/api/polling.js");
 // import session from '../lib/session' Middleware to check if session exists





const router = Object(express__WEBPACK_IMPORTED_MODULE_0__["Router"])();
router.use('/companies', _company__WEBPACK_IMPORTED_MODULE_1__["default"]);
router.use('/dashboard', _dashboard__WEBPACK_IMPORTED_MODULE_2__["default"]);
router.use('/link', _account__WEBPACK_IMPORTED_MODULE_3__["default"]);
router.use('/polling', _polling__WEBPACK_IMPORTED_MODULE_4__["default"]);
/* harmony default export */ __webpack_exports__["default"] = (router);

/***/ }),

/***/ "./server/api/polling.js":
/*!*******************************!*\
  !*** ./server/api/polling.js ***!
  \*******************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! express */ "express");
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(express__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _fixtures_pollings__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ../fixtures/pollings */ "./server/fixtures/pollings.js");


/* import request from 'request-promise'
import { server } from '../server.config'
import { parseToQuery } from '../lib/utils' */

const router = Object(express__WEBPACK_IMPORTED_MODULE_0__["Router"])();
router.get('/', (req, res, next) => {
  res.json(_fixtures_pollings__WEBPACK_IMPORTED_MODULE_1__["pollings"]);
});
/* harmony default export */ __webpack_exports__["default"] = (router);

/***/ }),

/***/ "./server/fixtures/charts.js":
/*!***********************************!*\
  !*** ./server/fixtures/charts.js ***!
  \***********************************/
/*! exports provided: paymentDelay, current, exposure */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "paymentDelay", function() { return paymentDelay; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "current", function() { return current; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "exposure", function() { return exposure; });
const paymentDelay = {
  'dataset1': {
    'display_name': 'PaymentDelay',
    'data': [{
      'range_display_name': '1-7 days',
      'percent': 0.55
    }, {
      'range_display_name': '8-30 days',
      'percent': 0.12
    }, {
      'range_display_name': '31-60 days',
      'percent': 20.93
    }, {
      'range_display_name': '61-90 days',
      'percent': 0.00
    }, {
      'range_display_name': '> 91 days',
      'percent': 0.01
    }]
  }
};
const current = {
  'dataset_1': {
    'display_name': 'Daily Current',
    'data': [{
      'max_value': 100,
      'percent': 73.5
    }]
  }
};
const exposure = {
  'dataset_1': {
    'display_name': 'Platform Exposure',
    'data': [{
      'max_value': 100,
      'percent': 22.55
    }]
  }
};


/***/ }),

/***/ "./server/fixtures/companies.js":
/*!**************************************!*\
  !*** ./server/fixtures/companies.js ***!
  \**************************************/
/*! exports provided: companies, marketplace, getCompany, marketplaceDetail */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "companies", function() { return companies; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "marketplace", function() { return marketplace; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "getCompany", function() { return getCompany; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "marketplaceDetail", function() { return marketplaceDetail; });
const companies = [{
  'id': '6',
  'company_name': 'myTripleAAA',
  'company_url': 'www.mytripleaaa.com',
  'company_country': 'ES',
  'company_country_name': 'SPAIN',
  'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
  'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
  'company_logo_url': 'https://testing4.azarus.es/img/logo/MyTripleA.png'
}, {
  'id': '2',
  'company_name': 'Comunitae',
  'company_url': 'www.comunitae.es',
  'company_country': 'ES',
  'company_country_name': 'SPAIN',
  'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
  'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Comunitae.png'
}, {
  'id': '4',
  'company_name': 'Arboribus',
  'company_url': 'www.arboribus.com',
  'company_country': 'ES',
  'company_country_name': 'SPAIN',
  'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
  'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Arboribus.png'
}, {
  'id': '7',
  'company_name': 'Lendix',
  'company_url': 'www.lendix.com',
  'company_country': 'ES',
  'ccompany_country_name': 'SPAIN',
  'company_privacy_url': 'https://testing4.azarus.es/pages/privacyPolicy',
  'company_termsofservice_url': 'https://testing4.azarus.es/pages/termsOfService',
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Lendix.png'
}];
const marketplace = [{
  'company_id': 25,
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Mintos.png',
  'company_key_info': {
    'company_key_info_text': 'We provide retail investors with an easy ...',
    'company_name': 'Mintos',
    'company_number_of_investors': 43342,
    'company_start': '2013',
    'company_country_of_origin': 'Latvia',
    'company_total_invested_assets': {
      'amount': {
        'value': 2342443223,
        'currency_code': 'EUR'
      }
    }
  },
  'company_product_details': {
    'loan_types': 'All Loans',
    'minimum_investment': {
      'value': 10,
      'currency_code': 'EUR'
    },
    'autoinvest_option': true,
    'fees': true,
    'secondary_market': false,
    'supported_currencies': [{
      'currency_code': 'EUR'
    }, {
      'currency_code': 'USD'
    }],
    'lending_return': 2,
    'buyback_guarantee': true,
    'collateral': false,
    'crossborder': true
  },
  'company_winvestify_evaluation': {
    'global_evaluation_actual_value': 800,
    'global_evaluation_max': 1000,
    'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP'
  }
}, {
  'company_id': 26,
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Finanzarel.png',
  'company_key_info': {
    'company_key_info_text': 'We provide retail investors with an easy ...',
    'company_name': 'Finanzarel',
    'company_number_of_investors': 3342,
    'company_start': '2013',
    'company_country_of_origin': 'Spain',
    'company_total_invested_assets': {
      'amount': {
        'value': 24244228,
        'currency_code': 'EUR'
      }
    }
  },
  'company_product_details': {
    'loan_types': 'All Loans',
    'minimum_investment': {
      'value': 10,
      'currency_code': 'EUR'
    },
    'autoinvest_option': true,
    'fees': false,
    'secondary_market': false,
    'supported_currencies': [{
      'currency_code': 'EUR'
    }, {
      'currency_code': 'USD'
    }],
    'lending_return': 2,
    'buyback_guarantee': true,
    'collateral': false,
    'crossborder': true
  },
  'company_winvestify_evaluation': {
    'global_evaluation_actual_value': 740,
    'global_evaluation_max': 1000,
    'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP'
  }
}, {
  'company_id': 7,
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Loanbook.png',
  'company_key_info': {
    'company_key_info_text': 'We provide retail investors with an easy ...',
    'company_name': 'Loanbook',
    'company_number_of_investors': 442,
    'company_start': '2013',
    'company_country_of_origin': 'Spain',
    'company_total_invested_assets': {
      'amount': {
        'value': 23443223,
        'currency_code': 'EUR'
      }
    }
  },
  'company_product_details': {
    'loan_types': 'All Loans',
    'minimum_investment': {
      'value': 10,
      'currency_code': 'EUR'
    },
    'autoinvest_option': true,
    'fees': false,
    'secondary_market': false,
    'supported_currencies': [{
      'currency_code': 'EUR'
    }, {
      'currency_code': 'USD'
    }],
    'lending_return': 2,
    'buyback_guarantee': true,
    'collateral': false,
    'crossborder': true
  },
  'company_winvestify_evaluation': {
    'global_evaluation_actual_value': 540,
    'global_evaluation_max': 1000,
    'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP'
  }
}];
const getCompany = id => {
  return marketplaceDetail.find(item => {
    return item.company_id == id;
  });
};
const marketplaceDetail = [{
  'company_id': 26,
  'company_name': 'Twino',
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Twino.png',
  'company_key_info': {
    'company_key_info_text': 'We provide retail investors with an easy ...',
    'company_name': 'Twino',
    'company_number_of_investors': 12045,
    'company_start': '2014',
    'company_country_of_origin': 'Latvia',
    'company_total_invested_assets': {
      'amount': {
        'value': 72443223,
        'currency_code': 'EUR'
      }
    }
  },
  'company_product_details': {
    'loan_types': 'All Loans',
    'minimum_investment': {
      'value': 10,
      'currency_code': 'EUR'
    },
    'autoinvest_option': true,
    'fees': false,
    'secondary_market': false,
    'supported_currencies': [{
      'currency_code': 'EUR'
    }, {
      'currency_code': 'GBP'
    }],
    'lending_return': 2,
    'buyback_guarantee': true,
    'collateral': false,
    'is_regulated': true,
    'crossborder': true
  },
  'company_extendedInfo': {
    'custodial_funds': 'Payasera',
    'transfer_funds': 'SEPA Bank Transfer',
    'withdrawals_fee': false,
    'secondary_market_fee': false,
    'sell_outs': false,
    'accepted_investors': 'Worldwide',
    'taxation': 'Country of Origin'
  },
  'company_commercial_offer_info': {
    'commercial_info_text': 'You will receive 5€ ....',
    'commercial_offer_url': 'https://www.twino.eu'
  },
  'company_winvestify_evaluation': {
    'global_evaluation_actual_value': 810,
    'global_evaluation_max': 1000,
    'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP',
    'details': [{
      'display_name': 'Transparency',
      'value': 74
    }, {
      'display_name': 'Investor Protection',
      'value': 64,
      'tooltip_display_name': 'Nothing defined in protection'
    }, {
      'display_name': 'Technology',
      'value': 62,
      'tooltip_display_name': 'Nothing defined yet in technology'
    }, {
      'display_name': 'Customer Care',
      'value': 86
    }, {
      'display_name': 'Risk Scoring',
      'value': 93,
      'tooltip_display_name': 'Nothing defined'
    }, {
      'display_name': 'Regulated',
      'value': 33,
      'tooltip_display_name': 'Nothing defined in regulated'
    }, {
      'display_name': 'Time In Market',
      'value': 66,
      'tooltip_display_name': 'Nothing defined in market'
    }, {
      'display_name': 'Team',
      'value': 78
    }, {
      'display_name': 'Usability',
      'value': 83,
      'tooltip_display_name': 'No tooltip contents defined'
    }, {
      'display_name': 'Yield',
      'value': 43,
      'tooltip_display_name': 'No tooltip contents defined for yield'
    }]
  }
}, {
  'company_id': 7,
  'company_name': 'Loanbook',
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Loanbook.png',
  'company_key_info': {
    'company_key_info_text': 'We provide retail investors with an easy ...',
    'company_name': 'Loanbook',
    'company_number_of_investors': 2342,
    'company_start': '2014',
    'company_country_of_origin': 'Spain',
    'company_total_invested_assets': {
      'amount': {
        'value': 7443223,
        'currency_code': 'EUR'
      }
    }
  },
  'company_product_details': {
    'loan_types': 'P2B loans',
    'minimum_investment': {
      'value': 60,
      'currency_code': 'EUR'
    },
    'autoinvest_option': false,
    'fees': true,
    'secondary_market': false,
    'supported_currencies': [{
      'currency_code': 'EUR'
    }],
    'lending_return': 2,
    'buyback_guarantee': false,
    'collateral': false,
    'is_regulated': true,
    'crossborder': true
  },
  'company_extendedInfo': {
    'transfer_funds': 'SEPA Bank Transfer',
    'withdrawals_fee': false,
    'secondary_market_fee': false,
    'sell_outs': false,
    'accepted_investors': 'Spain Only',
    'taxation': 'Country of Origin'
  },
  'company_commercial_offer_info': {
    'commercial_info_text': 'You will receive 5€ ....',
    'commercial_offer_url': 'https://www.loanbook.es'
  },
  'company_winvestify_evaluation': {
    'global_evaluation_actual_value': 620,
    'global_evaluation_max': 1000,
    'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP',
    'details': [{
      'display_name': 'Transparency',
      'value': 5
    }, {
      'display_name': 'Investor Protection',
      'value': 44,
      'tooltip_display_name': 'Nothing defined in protection'
    }, {
      'display_name': 'Technology',
      'value': 44,
      'tooltip_display_name': 'Nothing defined yet in technology'
    }, {
      'display_name': 'Customer Care',
      'value': 86
    }, {
      'display_name': 'Risk Scoring',
      'value': 93,
      'tooltip_display_name': 'Nothing defined'
    }, {
      'display_name': 'Regulated',
      'value': 88,
      'tooltip_display_name': 'Nothing defined in regulated'
    }, {
      'display_name': 'Time In Market',
      'value': 26,
      'tooltip_display_name': 'Nothing defined in market'
    }, {
      'display_name': 'Team',
      'value': 78
    }, {
      'display_name': 'Usability',
      'value': 83,
      'tooltip_display_name': 'No tooltip contents defined'
    }, {
      'display_name': 'Yield',
      'value': 43,
      'tooltip_display_name': 'No tooltip contents defined for yield'
    }]
  }
}, {
  'company_id': 25,
  'company_name': 'Mintos',
  'company_logo_url': 'https://testing4.azarus.es/img/logo/Mintos.png',
  'company_key_info': {
    'company_key_info_text': 'We provide retail investors with an easy ...',
    'company_name': 'Mintos',
    'company_number_of_investors': 43342,
    'company_start': '2013',
    'company_country_of_origin': 'Latvia',
    'company_total_invested_assets': {
      'amount': {
        'value': 2342443223,
        'currency_code': 'EUR'
      }
    }
  },
  'company_product_details': {
    'loan_types': 'All Loans',
    'minimum_investment': {
      'value': 10,
      'currency_code': 'EUR'
    },
    'autoinvest_option': true,
    'fees': false,
    'secondary_market': false,
    'supported_currencies': [{
      'currency_code': 'EUR'
    }, {
      'currency_code': 'USD'
    }, {
      'currency_code': 'GBP'
    }, {
      'currency_code': 'RUB'
    }],
    'lending_return': 1,
    'buyback_guarantee': true,
    'collateral': false,
    'is_regulated': false,
    'crossborder': true
  },
  'company_extendedInfo': {
    'custodial_funds': 'Payasera',
    'transfer_funds': 'SEPA Bank Transfer',
    'withdrawals_fee': false,
    'secondary_market_fee': false,
    'sell_outs': false,
    'accepted_investors': 'Worldwide',
    'taxation': 'Country of Origin'
  },
  'company_commercial_offer_info': {
    'commercial_info_text': 'You will receive 5€ ....',
    'commercial_offer_url': 'www.mintos.com'
  },
  'company_winvestify_evaluation': {
    'global_evaluation_actual_value': 840,
    'global_evaluation_max': 1000,
    'tooltip_display_name': 'This is the combination of all individual values and provides a very good evaluation of the PFP',
    'details': [{
      'display_name': 'Transparency',
      'value': 24
    }, {
      'display_name': 'Investor Protection',
      'value': 44,
      'tooltip_display_name': 'Nothing defined in protection'
    }, {
      'display_name': 'Technology',
      'value': 62,
      'tooltip_display_name': 'Nothing defined yet in technology'
    }, {
      'display_name': 'Customer Care',
      'value': 86
    }, {
      'display_name': 'Risk Scoring',
      'value': 93,
      'tooltip_display_name': 'Nothing defined'
    }, {
      'display_name': 'Regulated',
      'value': 33,
      'tooltip_display_name': 'Nothing defined in regulated'
    }, {
      'display_name': 'Time In Market',
      'value': 26,
      'tooltip_display_name': 'Nothing defined in market'
    }, {
      'display_name': 'Team',
      'value': 78
    }, {
      'display_name': 'Usability',
      'value': 83,
      'tooltip_display_name': 'No tooltip contents defined'
    }, {
      'display_name': 'Yield',
      'value': 43,
      'tooltip_display_name': 'No tooltip contents defined for yield'
    }]
  }
}];

/***/ }),

/***/ "./server/fixtures/globalDashboard.js":
/*!********************************************!*\
  !*** ./server/fixtures/globalDashboard.js ***!
  \********************************************/
/*! exports provided: general */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "general", function() { return general; });
const general = {
  'invested_assets': {
    'display_name': 'Invested Assets',
    'tooltip_display_name': 'Total nominal value of all assets held in your linked accounts.',
    'value': {
      'amount': 335.43,
      'currency_code': 'EUR'
    }
  },
  'reservedFunds': {
    'display_name': 'Reserved Funds',
    'tooltip_display_name': 'The part of Invested Assets, which are dedicated to specific loans that are not yet issued.',
    'value': {
      'amount': 24.01,
      'currency_code': 'EUR'
    }
  },
  'cash': {
    'display_name': 'Cash',
    'tooltip_display_name': 'The total cash balance on all your linked accounts. You should use this balance to invest in assets to reduce Cash Drag.',
    'value': {
      'amount': 81.01,
      'currency_code': 'EUR'
    }
  },
  'nar_total_funds': {},
  'nar': {
    'display_name': 'Net Annual Yield',
    'tooltip_display_name': '',
    'icon': 'icon-win-airplay',
    'graph_data': [{
      'url': '/v1/dashboards/graphics/nar/?period=year',
      'option_display_name': 'Last Year',
      'default': true
    }, {
      'url': '/v1/dashboards/graphics/nar/?period=all',
      'option_display_name': 'All'
    }]
  },
  'nar_past_year': {
    'display_name': 'Net Annual Yield',
    'tooltip_display_name': 'Net Annual Return past year',
    'icon': 'icon-win-airplay',
    'graph_data': [{
      'url': '/v1/dashboards/graphics/narpastyear/?period=year',
      'option_display_name': 'Last Year',
      'default': true
    }, {
      'url': '/v1/dashboards/graphics/narpastyear/?period=all',
      'option_display_name': 'All'
    }]
  },
  'active_investments': {
    'display_name': 'Active Investments',
    'tooltip_display_name': 'Number of individual loans or assets that you currently own. The higher the sum, the better diversified your portfolio is.',
    'icon': 'icon-win-airplay',
    'graph_data': [{
      'url': '/v1/dashboards/graphics/activeinvestments/?period=year',
      'option_display_name': 'Year',
      'default': true
    }, {
      'url': '/v1/dashboards/graphics/activeinvestments/?period=all',
      'option_display_name': 'All'
    }]
  },
  'net_deposits': {
    'display_name': 'Net Deposits',
    'tooltip_display_name': 'All transfers from your bank account to all linked platforms minus the withdrawals from these platforms.',
    'icon': 'icon-win-airplay',
    'value': {
      'amount': 81.01,
      'currency_code': 'EUR'
    }
  },
  'cashDrag': {
    'display_name': 'Cash Drag',
    'tooltip_display_name': 'The percentage of your Total Volume, which is not invested in assets and therefore does not yield any interest currently.',
    'percent': 33.1,
    'icon': 'icon-win-airplay',
    'graph_data': [{
      'url': '/v1/dashboards/graphics/cashdrag/?period=year',
      'option_display_name': 'Year',
      'default': true
    }, {
      'url': '/v1/dashboards/graphics/cashdrag/?period=all',
      'option_display_name': 'All'
    }]
  },
  'net_return': {
    'display_name': 'Net Return',
    'tooltip_display_name': 'Net Return',
    'icon': 'icon-win-airplay',
    'graph_data': [{
      'url': '/v1/dashboards/graphics/netreturn?period=year',
      'option_display_name': 'Last Year',
      'default': true
    }, {
      'url': '/v1/dashboards/graphics/netreturn?period=all',
      'option_display_name': 'All'
    }]
  },
  'net_return_past_year': {
    'display_name': 'Net Return',
    'tooltip_display_name': 'Your total interest and other income on all linked platforms minus fees, tax and write-offs.',
    'value': {
      'amount': '2453.00',
      'currency_code': 'EUR'
    },
    'icon': 'icon-win-airplay',
    'graph_data': [{
      'url': '/v1/dashboards/graphics/netreturnpastyear/?period=year',
      'option_display_name': 'Last Year',
      'default': true
    }, {
      'url': '/v1/dashboards/graphics/netreturnpastyear/?period=all',
      'option_display_name': 'All'
    }]
  },
  'net_return_total_funds': {},
  'kpis': {
    'display_name': 'Key Performance Indicators',
    'tooltip_display_name': "This displays the most importante KPI's",
    'issues': [{
      'issue_platform': 'Mintos',
      'issue_text': 'It was not possible to update the data of this platform. This will be retried again during the next night. No action is required from you.'
    }],
    'table_header': [{
      'display_name': 'Platform/UserName'
    }, {
      'display_name': 'Yield',
      'tooltip_display_name': 'The standardized yield of the platform'
    }, {
      'display_name': 'Total Volume',
      'tooltip_display_name': 'Total Volume tooltip'
    }, {
      'display_name': 'Cash'
    }, {
      'display_name': 'Exposure',
      'tooltip_display_name': 'Exposure tooltip'
    }, {
      'display_name': 'Current',
      'tooltip_display_name': 'Current tooltip'
    }],
    'table_data': [[{
      'datum': 'Mintos',
      'issue_text': 'It was not possible to update the data of this platform. This will be retried again during the next night. No action is required from you.'
    }, {
      'datum': 'Antoine@winvestify.com'
    }, {
      'datum': {
        'percent': 11.3
      }
    }, {
      'datum': {
        'amount': 225700.88,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'amount': 5563.42,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'percent': 28.3
      }
    }, {
      'datum': {
        'percent': 98.12
      }
    }], [{
      'datum': 'Twino'
    }, {
      'datum': 'Antoine@gmail.com'
    }, {
      'datum': {
        'percent': 9.8
      }
    }, {
      'datum': {
        'amount': 24200,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'amount': 551.42,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'percent': 17.3
      }
    }, {
      'datum': {
        'percent': 99.6
      }
    }], [{
      'datum': 'Bondora',
      'issue_text': 'Our system detected a new concept payment concept in the account of this PFP which was not understood. This problem will be rectified during the course of the day and incorporated during the regular update of the next night. No action is required from you.'
    }, {
      'datum': 'Charly@winvestify.com'
    }, {
      'datum': {
        'percent': 9.8
      }
    }, {
      'datum': {
        'amount': 24200,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'amount': 551.42,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'percent': 17.3
      }
    }, {
      'datum': {
        'percent': 99.6
      }
    }], [{
      'datum': 'Bondora'
    }, {
      'datum': 'My BusinessAccount on Bondora'
    }, {
      'datum': {
        'percent': 12.5
      }
    }, {
      'datum': {
        'amount': 924200,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'amount': 71.42,
        'currency_code': 'EUR'
      }
    }, {
      'datum': {
        'percent': 4.3
      }
    }, {
      'datum': {
        'percent': 99.0
      }
    }]]
  }
};

/***/ }),

/***/ "./server/fixtures/linkAccounts.js":
/*!*****************************************!*\
  !*** ./server/fixtures/linkAccounts.js ***!
  \*****************************************/
/*! exports provided: accounts, created, preChecks */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "accounts", function() { return accounts; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "created", function() { return created; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "preChecks", function() { return preChecks; });
const accounts = {
  'tooltip_display_name': 'An linked account can have three states: <br>1 - QUEUED which means that the accountlinking event is put in a queue for collecting the data, <br< 2- ANALYZING which means that the all the collected data is being analyzed. This can take quite some time and depends on the size of the account. You will receive an SMS when this process has finished. <br> 3 - MONITORED, All data has been analyzed and is available in your dashboard. The account will from now on be regularly updated (typically daily)',
  'service_status': 'ACTIVE',
  'service_status_display_message': 'You are using the maximum number of linkedaccounts. If you like to link more accounts, please upgrade your subscription',
  'data': [{
    'id': 432455,
    'company_id': 25,
    'linkedaccount_status': 'ACTIVE',
    'linkedaccount_visual_state': 'ANALYZING',
    'linkedaccount_username': 'klaus.austria@gmail.com',
    'linkedaccount_password': 'myPassword',
    'linkedaccount_platform_display_name': 'klaus@gmail.com@EUR',
    'linkedaccount_alias': 'KLAUS',
    'links': [{
      'href': '/v1/linkedaccounts/432455',
      'rel': 'edit',
      'method': 'PATCH'
    }, {
      'href': '/v1/linkedaccounts/432455',
      'rel': 'delete',
      'method': 'DELETE'
    }, {
      'href': '/v1/pollingresources/499825',
      'rel': 'monitor',
      'method': 'GET'
    }, {
      'href': '/v1/pollingresources/499825',
      'rel': 'delete',
      'method': 'DELETE'
    }]
  }, {
    'id': 432456,
    'company_id': 25,
    'linkedaccount_status': 'ACTIVE',
    'linkedaccount_visual_state': 'MONITORED',
    'linkedaccount_username': 'antoine@gmail.com',
    'linkedaccount_password': 'myPassword',
    'linkedaccount_platform_display_name': 'klaus@gmail.com@USD',
    'linkedaccount_alias': 'KLAUS',
    'links': [{
      'href': '/v1/linkedaccounts/432456',
      'rel': 'edit',
      'method': 'PATCH'
    }, {
      'href': '/v1/linkedaccounts/432456',
      'rel': 'delete',
      'method': 'DELETE'
    }]
  }, {
    'id': 432458,
    'company_id': 26,
    'linkedaccount_status': 'ACTIVE',
    'linkedaccount_visual_state': 'MONITORED',
    'linkedaccount_username': 'antoine@gmail.com',
    'linkedaccount_password': 'myPassword',
    'linkedaccount_platform_display_name': 'kkukovetz@gmail.com',
    'linkedaccount_alias': 'KLAUS',
    'links': [{
      'href': '/v1/linkedaccounts/432458',
      'rel': 'edit',
      'method': 'PATCH'
    }, {
      'href': '/v1/linkedaccounts/432458',
      'rel': 'delete',
      'method': 'DELETE'
    }]
  }]
};
const preChecks = {
  accounts: [{
    'account_identity': '154ded8e-52d2-48c8-8ba1-a39300b590a7',
    'account_platform_display_name': 'klaus@gmail.com'
  }, {
    'account_identity': '13y4ed9x-52d2-48c8-8ba1-a39300b590a7',
    'account_platform_display_name': 'MLI Limited'
  }]
};
const created = {
  'data': {
    'feedback_message_user': 'This is the confirmation message for the end-user',
    'linkedaccount': {
      'id': 432455,
      'company_id': 25,
      'linkedaccount_username': 'klaus@gmail.com',
      'linkedaccount_password': 'myPassword',
      'linkedaccount_platform_display_name': 'klaus@gmail.com@eur',
      'linkedaccount_alias': 'KLAUS',
      'links': [{
        'href': '/v1/linkedaccounts/432455',
        'rel': 'edit',
        'method': 'PATCH'
      }, {
        'href': '/v1/linkedaccounts/432455',
        'rel': 'delete',
        'method': 'DELETE'
      }, {
        'href': '/v1/pollingresources/499825',
        'rel': 'monitor   ',
        'method': 'GET'
      }, {
        'href': '/v1/pollingresources/499825',
        'rel': 'polling',
        'method': 'DELETE'
      }]
    }
  }
};


/***/ }),

/***/ "./server/fixtures/pollings.js":
/*!*************************************!*\
  !*** ./server/fixtures/pollings.js ***!
  \*************************************/
/*! exports provided: pollings */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "pollings", function() { return pollings; });
const pollings = [{
  'polling_id': '424534',
  'polling_frequency': 60,
  'polling_type': 'NOTIFICATION_CHECK',
  'polling_value': '232'
}, {
  'polling_id': '42436',
  'polling_frequency': 0,
  'polling_type': 'LINKEDACCOUNT_CHECK',
  'polling_value': '2'
}, {
  'polling_id': '1436',
  'polling_frequency': 60,
  'polling_type': 'LINKEDACCOUNT_CHECK',
  'polling_value': '3'
}];

/***/ }),

/***/ "./server/index.js":
/*!*************************!*\
  !*** ./server/index.js ***!
  \*************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! express */ "express");
/* harmony import */ var express__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(express__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var nuxt__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! nuxt */ "nuxt");
/* harmony import */ var nuxt__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(nuxt__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _server_config__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./server.config */ "./server/server.config.js");
/* harmony import */ var _server_config__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_server_config__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var cookie_parser__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! cookie-parser */ "cookie-parser");
/* harmony import */ var cookie_parser__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(cookie_parser__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var body_parser__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! body-parser */ "body-parser");
/* harmony import */ var body_parser__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(body_parser__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var express_validation__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! express-validation */ "express-validation");
/* harmony import */ var express_validation__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(express_validation__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var cookie_session__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! cookie-session */ "cookie-session");
/* harmony import */ var cookie_session__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(cookie_session__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var _api__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./api */ "./server/api/index.js");








const app = express__WEBPACK_IMPORTED_MODULE_0___default()();
const host = _server_config__WEBPACK_IMPORTED_MODULE_2__["server"].host;
const port = _server_config__WEBPACK_IMPORTED_MODULE_2__["server"].port;
app.set('port', port);
app.use(body_parser__WEBPACK_IMPORTED_MODULE_4___default.a.json({
  limit: '50mb'
}));
app.use(body_parser__WEBPACK_IMPORTED_MODULE_4___default.a.urlencoded({
  limit: '50mb',
  extended: true
}));
app.use(cookie_parser__WEBPACK_IMPORTED_MODULE_3___default()());
app.use(cookie_session__WEBPACK_IMPORTED_MODULE_6___default()({
  name: 'wvt',
  keys: _server_config__WEBPACK_IMPORTED_MODULE_2__["cookie"].session.keys
}));
app.use(express__WEBPACK_IMPORTED_MODULE_0___default.a.static('../assets')); // Import API Routes

app.use('/api', _api__WEBPACK_IMPORTED_MODULE_7__["default"]); // catch validation errors

app.use((err, req, res, next) => {
  if (err.statusCode === 401) {
    delete req.session.user;
  }

  if (err instanceof express_validation__WEBPACK_IMPORTED_MODULE_5___default.a.ValidationError) {
    // const error = new Error(err.errors, err.status, true)
    res.status(err.status).json(err.errors);
  }

  return next(err);
}); // Import and Set Nuxt.js options

let config = __webpack_require__(/*! ../nuxt.config.js */ "./nuxt.config.js");

config.dev = !("development" === 'production'); // Init Nuxt.js

const nuxt = new nuxt__WEBPACK_IMPORTED_MODULE_1__["Nuxt"](config); // Build only in dev mode

if (config.dev) {
  const builder = new nuxt__WEBPACK_IMPORTED_MODULE_1__["Builder"](nuxt);
  builder.build();
} // Give nuxt middleware to express


app.use(nuxt.render); // Listen the server

app.listen(port, host);
console.log('Server listening on: ' + host + ':' + port); // eslint-disable-line no-console

/***/ }),

/***/ "./server/lib/validation.js":
/*!**********************************!*\
  !*** ./server/lib/validation.js ***!
  \**********************************/
/*! exports provided: linkAccount, linkAccountPreCheck, login */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "linkAccount", function() { return linkAccount; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "linkAccountPreCheck", function() { return linkAccountPreCheck; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "login", function() { return login; });
/* harmony import */ var joi__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! joi */ "joi");
/* harmony import */ var joi__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(joi__WEBPACK_IMPORTED_MODULE_0__);

const login = {
  body: {
    username: joi__WEBPACK_IMPORTED_MODULE_0___default.a.string().email().required(),
    password: joi__WEBPACK_IMPORTED_MODULE_0___default.a.string().required()
  }
};
const linkAccountPreCheck = {
  body: {
    password: joi__WEBPACK_IMPORTED_MODULE_0___default.a.string().required(),
    username: joi__WEBPACK_IMPORTED_MODULE_0___default.a.string().required(),
    companyId: joi__WEBPACK_IMPORTED_MODULE_0___default.a.number().required()
  }
};
const linkAccount = {
  body: {
    password: joi__WEBPACK_IMPORTED_MODULE_0___default.a.string().required(),
    username: joi__WEBPACK_IMPORTED_MODULE_0___default.a.string().required(),
    companyId: joi__WEBPACK_IMPORTED_MODULE_0___default.a.number().required(),
    linkedAccount: joi__WEBPACK_IMPORTED_MODULE_0___default.a.object().not().empty()
  }
};


/***/ }),

/***/ "./server/server.config.js":
/*!*********************************!*\
  !*** ./server/server.config.js ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

const env = "development" || false;
const config = {
  env: {
    current: env,
    is: {
      development: env === 'development',
      testing: env === 'testing',
      production: env === 'production'
    }
  },
  server: {
    host: process.env.HOST || '0.0.0.0',
    port: process.env.NODE_PORT || process.env.PORT || 3000,
    urlApi: process.env.URL_API || 'TBD'
  },
  cookie: {
    session: {
      keys: [process.env.COOKIE_SESSION_KEY || '-8jd*#9o&iQAoCxd52hhvxCfuNMcQBXP']
    }
  }
};
module.exports = Object.freeze(config);

/***/ }),

/***/ "body-parser":
/*!******************************!*\
  !*** external "body-parser" ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("body-parser");

/***/ }),

/***/ "cookie-parser":
/*!********************************!*\
  !*** external "cookie-parser" ***!
  \********************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("cookie-parser");

/***/ }),

/***/ "cookie-session":
/*!*********************************!*\
  !*** external "cookie-session" ***!
  \*********************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("cookie-session");

/***/ }),

/***/ "express":
/*!**************************!*\
  !*** external "express" ***!
  \**************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("express");

/***/ }),

/***/ "express-validation":
/*!*************************************!*\
  !*** external "express-validation" ***!
  \*************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("express-validation");

/***/ }),

/***/ "joi":
/*!**********************!*\
  !*** external "joi" ***!
  \**********************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("joi");

/***/ }),

/***/ "nuxt":
/*!***********************!*\
  !*** external "nuxt" ***!
  \***********************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("nuxt");

/***/ }),

/***/ "webpack-node-externals":
/*!*****************************************!*\
  !*** external "webpack-node-externals" ***!
  \*****************************************/
/*! no static exports found */
/***/ (function(module, exports) {

module.exports = require("webpack-node-externals");

/***/ })

/******/ });
//# sourceMappingURL=main.map