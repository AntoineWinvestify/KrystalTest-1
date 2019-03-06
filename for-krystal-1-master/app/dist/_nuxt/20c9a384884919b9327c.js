(window.webpackJsonp=window.webpackJsonp||[]).push([[12],{184:function(t,e,l){"use strict";var n=l(209);e.a=n.create({})},850:function(t,e,l){"use strict";l.r(e);var n=l(184),r={head:function(){return{title:"Marketplace"}},data:function(){return{ex0:!1,ex1:!1,ex2:!1,ex3:!1,ex4:!1,ex5:!1,ex6:!1,ex7:!1,ex8:!1,ex9:!1,showFilter:!1,show:!1,lendings:[]}},mounted:function(){this.$store.commit("SET_BUTTON_BACK",!1),this.getData()},methods:{resizing:function(){window.innerWidth<=800?this.showFilter=!0:(this.showFilter=!1,this.show=!1)},getData:function(){var t=this;n.a.get("/api/companies/marketplace").then(function(e){var data=e.data;t.lendings=data})},goTo:function(t){return"/".concat(this.$store.state.locale,"/marketplace/").concat(t)}},layout:"dashboard"},o=l(4),component=Object(o.a)(r,function(){var t=this,e=t.$createElement,l=t._self._c||e;return l("v-container",{directives:[{name:"resize",rawName:"v-resize",value:t.resizing,expression:"resizing"}],attrs:{"grid-list-md":""}},[l("v-layout",{attrs:{row:"",wrap:""}},[l("v-flex",{attrs:{xs12:""}},[l("v-card",{staticClass:"data-card marketplace",attrs:{"elevation-1":"",dark:""}},[l("v-layout",{attrs:{row:"",wrap:""}},[l("v-flex",{attrs:{xs12:""}},[l("div",{staticClass:"card-title"},[t._v("\n\t\t\t\t\t\t\tLending Platforms\n\t\t\t\t\t\t")])]),t._v(" "),l("v-flex",{attrs:{xs12:""}},[l("v-layout",{staticClass:"filter-mobile",class:{show:t.show},attrs:{row:"",wrap:""}},[l("v-flex",{attrs:{xs12:""}},[l("v-layout",{attrs:{row:"",wrap:""}},[l("v-flex",{staticClass:"title-check",attrs:{md2:"",lg2:""}},[t._v("\n\t\t\t\t\t\t\t\t\t\tType of Loans:\n\t\t\t\t\t\t\t\t\t")]),t._v(" "),l("v-flex",{staticClass:"custom-checks main",attrs:{md10:"",lg10:""}},[l("v-checkbox",{attrs:{label:"Business Loans",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex1,callback:function(e){t.ex1=e},expression:"ex1"}}),t._v(" "),l("v-checkbox",{attrs:{label:"Consumer Loans",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex2,callback:function(e){t.ex2=e},expression:"ex2"}}),t._v(" "),l("v-checkbox",{attrs:{label:"Invoice Financing",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex3,callback:function(e){t.ex3=e},expression:"ex3"}}),t._v(" "),l("v-checkbox",{attrs:{label:"Mortage Loans",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex4,callback:function(e){t.ex4=e},expression:"ex4"}})],1)],1)],1),t._v(" "),l("v-flex",{staticClass:"custom-checks main",attrs:{xs12:""}},[l("v-layout",{attrs:{row:"",wrap:""}},[l("v-flex",{staticClass:"title-check",attrs:{md2:"",lg2:""}},[t._v("\n\t\t\t\t\t\t\t\t\t\tPlus:\n\t\t\t\t\t\t\t\t\t")]),t._v(" "),l("v-flex",{staticClass:"custom-checks main",attrs:{md10:"",lg10:""}},[l("v-checkbox",{attrs:{label:"Secondary Market",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex6,callback:function(e){t.ex6=e},expression:"ex6"}}),t._v(" "),l("v-checkbox",{attrs:{label:"Cross-Border",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex7,callback:function(e){t.ex7=e},expression:"ex7"}}),t._v(" "),l("v-checkbox",{attrs:{label:"Collateral",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex8,callback:function(e){t.ex8=e},expression:"ex8"}}),t._v(" "),l("v-checkbox",{attrs:{label:"Buyback Guarantee",color:"light-green accent-3",value:"","hide-details":""},model:{value:t.ex9,callback:function(e){t.ex9=e},expression:"ex9"}})],1)],1)],1)],1),t._v(" "),l("v-fab-transition",[l("v-btn",{directives:[{name:"show",rawName:"v-show",value:t.showFilter,expression:"showFilter"}],staticClass:"filter-button",attrs:{color:"blue accent-4",dark:"",fixed:"",small:"",bottom:"",right:"",fab:""},on:{click:function(e){t.show=!t.show}}},[l("v-icon",[t._v("settings")])],1)],1)],1)],1)],1)],1),t._v(" "),t._l(t.lendings,function(e,n){return l("v-flex",{key:n,attrs:{xs12:""}},[l("v-card",{staticClass:"data-card marketplace",attrs:{"elevation-1":"",dark:""}},[l("v-layout",{staticClass:"lending-row",attrs:{row:"",wrap:""}},[l("v-flex",{staticClass:"image-container",attrs:{xs6:"",sm8:"",md2:"",lg2:""}},[l("figure",[l("img",{attrs:{src:e.company_logo_url,alt:""}})])]),t._v(" "),l("v-flex",{staticClass:"lendings-list",attrs:{xs12:"","order-xs3":"","order-sm3":"","order-md2":"","order-lg2":"",md8:"",lg8:""}},[l("div",{staticClass:"lendings-item"},[l("span",[t._v("All loans")]),t._v(" "),l("span",[t._v(t._s(e.company_product_details.loan_types))])]),t._v(" "),l("div",{staticClass:"lendings-item"},[l("span",[t._v(t._s(t._f("currency")(e.company_product_details.minimum_investment.value,e.company_product_details.minimum_investment.currency_code)))]),t._v(" "),l("span",[t._v("Min. Investment")])]),t._v(" "),l("div",{staticClass:"lendings-item"},[l("span",[t._v(t._s(e.company_product_details.crossborder?"Yes":"No"))]),t._v(" "),l("span",[t._v("CrossBorder")])]),t._v(" "),l("div",{staticClass:"lendings-item"},[l("span",[t._v(t._s(e.company_product_details.secondary_market?"Yes":"No"))]),t._v(" "),l("span",[t._v("Secondary Market")])]),t._v(" "),l("div",{staticClass:"lendings-item"},[l("span",[t._v(t._s(e.company_product_details.buyback_guarantee?"Yes":"No"))]),t._v(" "),l("span",[t._v("Guarantee")])]),t._v(" "),l("div",{staticClass:"lendings-item"},[l("span",[t._v(t._s(e.company_winvestify_evaluation.global_evaluation_actual_value))]),t._v(" "),l("span",[t._v("FinScore")])])]),t._v(" "),l("v-flex",{staticClass:"btn-container",attrs:{xs6:"","order-xs2":"","order-sm2":"","order-md3":"",sm4:"",md2:"",lg2:""}},[l("v-btn",{attrs:{nuxt:"",to:t.goTo(e.company_id),color:"success"}},[t._v("Read More")])],1)],1)],1)],1)})],2)],1)},[],!1,null,null,null);e.default=component.exports}}]);