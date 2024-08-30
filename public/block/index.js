(()=>{"use strict";const e=window.wp.blocks,t=window.wc.blocksCheckout,o=JSON.parse('{"$schema":"https://schemas.wp.org/trunk/block.json","apiVersion":3,"name":"packeta/packeta-widget","version":"0.1.0","title":"Packeta Widget","category":"woocommerce","parent":["woocommerce/checkout-shipping-methods-block"],"attributes":{"lock":{"type":"object","default":{"remove":true,"move":true}}},"icon":"cart","description":"Packeta Widget Block","textdomain":"packeta-widget","viewScript":"file:./index.js"}'),n=window.React,c=window.wp.blockEditor,i=({children:e,buttonLabel:t,logoSrc:o,logoAlt:c,info:i,onClick:a,loading:s,placeholderText:l})=>(0,n.createElement)("div",{className:"packetery-widget-button-wrapper"},s&&(0,n.createElement)("div",{className:"packeta-widget-loading"},l),!s&&(0,n.createElement)("div",{className:"form-row packeta-widget blocks"},(0,n.createElement)("div",{className:"packetery-widget-button-row packeta-widget-button"},(0,n.createElement)("img",{className:"packetery-widget-button-logo",src:o,alt:c}),(0,n.createElement)("a",{onClick:a,className:"button alt components-button wc-block-components-button wp-element-button contained"},t)),e,i&&(0,n.createElement)("p",{className:"packeta-widget-info"},i))),a=window.wp.data,s=window.wc.wcSettings,l=window.wc.blocksComponents,r=window.wc.wcBlocksRegistry,{PAYMENT_STORE_KEY:p}=window.wc.wcBlocksData,d=window.wp.i18n,{extensionCartUpdate:u}=wc.blocksCheckout,w=function(e,t){let o=null;if(t)o=t.value;else{const e=document.querySelector('input[name="radio-control-wc-payment-method-options"]:checked');null!==e&&(o=e.value)}let n=null;if(e)n=e.shippingRateId;else{let e=document.querySelectorAll('.wc-block-components-shipping-rates-control input[type="radio"]');for(let t=0;t<e.length;t++)if(e[t].checked){n=e[t].value;break}}let c={};n&&(c.shipping_method=n),o&&(c.payment_method=o),"{}"!==JSON.stringify(c)&&u({namespace:"packetery-js-hooks",data:c})};(0,e.registerBlockType)(o,{title:(0,d.__)("title","packeta"),description:(0,d.__)("description","packeta"),edit:()=>{const e=(0,c.useBlockProps)();return(0,n.createElement)("div",{...e},(0,n.createElement)(i,{buttonLabel:"Choose pickup point",logoSrc:"data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDI1LjEuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IlZyc3R2YV8xIiB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB4PSIwcHgiIHk9IjBweCIKCSB2aWV3Qm94PSIwIDAgMzcgNDAiIHN0eWxlPSJmaWxsOiNhN2FhYWQiIHhtbDpzcGFjZT0icHJlc2VydmUiPgo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPgoJLnN0MHtmaWxsLXJ1bGU6ZXZlbm9kZDtjbGlwLXJ1bGU6ZXZlbm9kZDtmaWxsOiAjYTdhYWFkO30KPC9zdHlsZT4KPHBhdGggY2xhc3M9InN0MCIgZD0iTTE5LjQsMTYuMWwtMC45LDAuNGwtMC45LTAuNGwtMTMtNi41bDYuMi0yLjRsMTMuNCw2LjVMMTkuNCwxNi4xeiBNMzIuNSw5LjZsLTQuNywyLjNsLTEzLjUtNmw0LjItMS42CglMMzIuNSw5LjZ6Ii8+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xOSwwbDE3LjIsNi42bC0yLjQsMS45bC0xNS4yLTZMMy4yLDguNkwwLjgsNi42TDE4LDBMMTksMEwxOSwweiBNMzQuMSw5LjFsMi44LTEuMWwtMi4xLDE3LjZsLTAuNCwwLjgKCUwxOS40LDQwbC0wLjUtMy4xbDEzLjQtMTJMMzQuMSw5LjF6IE0yLjUsMjYuNWwtMC40LTAuOEwwLDguMWwyLjgsMS4xbDEuOSwxNS43bDEzLjQsMTJMMTcuNiw0MEwyLjUsMjYuNXoiLz4KPHBhdGggY2xhc3M9InN0MCIgZD0iTTI4LjIsMTIuNGw0LjMtMi43bC0xLjcsMTQuMkwxOC42LDM1bDAuNi0xN2w1LjQtMy4zTDI0LjMsMjNsMy4zLTIuM0wyOC4yLDEyLjR6Ii8+CjxwYXRoIGNsYXNzPSJzdDAiIGQ9Ik0xNy43LDE3LjlsMC42LDE3bC0xMi4yLTExTDQuNCw5LjhMMTcuNywxNy45eiIvPgo8L3N2Zz4K",logoAlt:"Packeta",info:"Pickup Point Name"}))}}),(0,t.registerCheckoutBlock)({metadata:o,component:({cart:e})=>{const[t,o]=(0,n.useState)(null),{shippingRates:c,shippingAddress:d,cartItemsWeight:u}=e,w=(0,a.useSelect)((e=>e(p)),[]),k=(0,s.getSetting)("packeta-widget_data"),{carrierConfig:g,translations:m,logo:h,widgetAutoOpen:y,adminAjaxUrl:M,codPaymentMethod:b}=k,L=((e,t)=>{if(!e||0===e.length)return null;const{shipping_rates:o}=e[0];return o&&0!==o.length?{packetaShippingRate:o.find((({rate_id:e,selected:o})=>{if(!o)return!1;const n=e.split(":").pop(),c=t[n];if(!c)return!1;const{is_pickup_points:i}=c;return c&&i}))||null,chosenShippingRate:o.find((({selected:e})=>e))||null}:null})(c,g),{packetaShippingRate:C=null,chosenShippingRate:N=null}=L||{},[j,I,f]=(e=>{let[t,o]=(0,n.useState)(null),[c,i]=(0,n.useState)(!1);return(0,n.useEffect)((()=>{c||null!==t||(i(!0),fetch(e,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded"},body:new URLSearchParams({action:"get_settings"})}).then((e=>e.json())).then((e=>{const{isAgeVerificationRequired:t}=e;o((e=>({...e,isAgeVerificationRequired:t})))})).catch((e=>{console.error("Error:",e),o(!1)})).finally((()=>{i(!1)})))}),[t,e,c]),[t,o,c]})(M),_=N?.rate_id||null,S=(0,n.useCallback)((e=>{if(console.log("paymentMethods",e),console.log("rateId",_),null===_)return;const t=_.split(":").pop(),o=g[t]||null;return e?Object.entries(e).filter((e=>(console.log("rateCarrierConfig",o),e[0]!==b||o.supports_cod?!o.hasOwnProperty("disallowed_checkout_payment_methods")||!o.disallowed_checkout_payment_methods.includes(e[0])||(console.log("disallowed_checkout_payment_methods",o.disallowed_checkout_payment_methods),!1):(console.log("! rateCarrierConfig.supports_cod"),!1)))):[]}),[_]);(0,r.registerPaymentMethodExtensionCallbacks)("packetery-js-hooks",{cod:e=>(console.log("arg",e),S().some((e=>"cod"===e[0]))),bacs:e=>S().some((e=>"bacs"===e[0]))}),(0,n.useEffect)((()=>{if(!j)return;const e=w.getActivePaymentMethod();let t=!1,o=!1;(!j.shippingSaved&&_||!j.paymentSaved&&""!==e)&&(_&&(t=!0),""!==e&&(o=!0),wp.hooks.doAction("packetery_save_shipping_and_payment_methods",_,e),I({...j,shippingSaved:t,paymentSaved:o}))}),[w,_,j,I,wp]),(0,n.useEffect)((()=>{if(!j)return;const e=d.country.toLowerCase();j.lastCountry?j.lastCountry!==e&&(t&&o(null),I({...j,lastCountry:e})):I({...j,lastCountry:e})}),[j,I,t,o,d]);const P=((e,t,o,c,i,a)=>{const{carrierConfig:s,language:l,packeteryApiKey:r,appIdentity:p,nonce:d,saveSelectedPickupPointUrl:u,pickupPointAttrs:w}=t;return(0,n.useCallback)((()=>{const t=e.rate_id.split(":").pop();let n=+(a/1e3).toFixed(2),k={language:l,appIdentity:p,weight:n};k.country=i.country.toLowerCase(),s[t].carriers&&(k.carriers=s[t].carriers),s[t].vendors&&(k.vendors=s[t].vendors),o&&o.isAgeVerificationRequired&&(k.livePickupPoint=!0);let g={};Packeta.Widget.pick(r,(e=>{if(!e)return;c({pickupPoint:e}),function(e,t,o){for(let n in t){if(!t.hasOwnProperty(n))continue;const{name:c,widgetResultField:i,isWidgetResultField:a}=t[n];if(!1===a)continue;let s=o[i||n];g[e]=g[e]||{},g[e][c]=s}}(t,w,e);let o=g[t];o.packetery_rate_id=t,fetch(u,{method:"POST",headers:{"Content-Type":"application/x-www-form-urlencoded","X-WP-Nonce":d},body:new URLSearchParams(o)}).then((e=>{if(!e.ok)throw new Error("HTTP error "+e.status)})).catch((e=>{console.error("Failed to save pickup point data:",e)}))}),k)}),[e,o])})(C,k,j,o,d,u);(0,n.useEffect)((()=>{C&&j&&!t&&y&&P()}),[C,y,P]);const{choosePickupPoint:T,packeta:x}=m;return C?(0,n.createElement)(i,{onClick:P,buttonLabel:T,logoSrc:h,logoAlt:x,info:t&&t.pickupPoint&&t.pickupPoint.name,loading:f,placeholderText:m.placeholderText},(0,n.createElement)(l.ValidatedTextInput,{value:t&&t.pickupPoint?t.pickupPoint.name:"",required:!0,errorMessage:function(e){return e&&e.pickupPoint?null:m.pickupPointNotChosen}(t)})):null}});const{extensionCartUpdate:k}=wc.blocksCheckout;wp.hooks.addAction("experimental__woocommerce_blocks-checkout-set-selected-shipping-rate","packetery-js-hooks",(function(e){w(e,null)})),wp.hooks.addAction("experimental__woocommerce_blocks-checkout-set-active-payment-method","packetery-js-hooks",(function(e){w(null,e)})),wp.hooks.addAction("experimental__woocommerce_blocks-checkout-render-checkout-form","packetery-js-hooks",(function(e){k({namespace:"packetery-js-hooks",data:{shipping_method:"n/a",payment_method:"n/a"}})})),wp.hooks.addAction("packetery_save_shipping_and_payment_methods","packetery-js-hooks",(function(e,t){w(e?{shippingRateId:e}:null,""!==t?{value:t}:null)}))})();