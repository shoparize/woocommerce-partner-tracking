console.log("Shoparize Plugin INIT");
const SHOPARIZE_API = () => {

	const shoparize_setToCookies = (cname, cvalue) => {
    const expire = new Date();
    const today = new Date();
    expire.setTime(today.getTime() + 3600000*24*90);
    document.cookie = cname + "=" + cvalue + ";expires= " + expire.toGMTString() + ";" + ";path=/";
	}
	const shoparize_getFromCookies = (cname) => {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
	}

  const sendAsyncReq = (obj, type) => {
    let xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {

      //const response = JSON.parse(xmlhttp.responseText);
      console.log(xmlhttp.responseText)

      if (xmlhttp.readyState == XMLHttpRequest.DONE) { // XMLHttpRequest.DONE == 4

        if (xmlhttp.status == 200) {
          //Success
        }else {
          //Error
        }
      }
    };

    if(type === "click") {
      xmlhttp.open("POST", "https://partner.shoparize.com/api/incoming/click?x-partner=allow");
      xmlhttp.setRequestHeader('Content-type', 'application/json');
      xmlhttp.send(JSON.stringify(obj));
    }else if(type === "conv") {

      xmlhttp.open("POST", "https://partner.shoparize.com/api/incoming/conv?x-partner=allow");
      xmlhttp.setRequestHeader('Content-type', 'application/json');
      xmlhttp.send(JSON.stringify(obj));
    }
  }
  const init = (shopID) => {
    if(shopID) {

      const keywords = ["utm_source", "utm_medium", "utm_campaign", "utm_term", "msclkid", "gclid", "wbraid", "gbraid"];

     	const url = new URLSearchParams(window.location.search);
      //const url = new URLSearchParams("?utm_source=shoparize");

      let isShoparize = false;
      let obj = {
      	"shopId": shopID
      };

      if(url.has('utm_source') && url.get('utm_source').toLowerCase() === "shoparize") {
        isShoparize = true;
      }

      if(isShoparize) {

        const epok = new Date().getTime();
        shoparize_setToCookies("_partner_click_time", epok);

        obj["_partner_click_time"] = epok;

        keywords.forEach(item => {
          const _found = url.has(item);
          if(_found) {
            const val = url.get(item);
            obj["_partner_" + item] = val;
            shoparize_setToCookies("_partner_" + item, val);
          }
        });

        sendAsyncReq(obj, 'click');
      }
    }else {
    	throw new Error("Please provide shopID");
    }
  }

  const conv = (shopID) => {
  	if(shopID) {

      const keywords = ["_partner_utm_source", "_partner_utm_medium", "_partner_utm_campaign", "_partner_utm_term", "_partner_msclkid", "_partner_gclid", "_partner_wbraid", "_partner_gbraid", "_partner_click_time"];

      let obj = {
      	"shopId": shopID,
        "dataLayerShoparize": window.dataLayerShoparize[0] || [],
        "transaction_time": new Date().getTime()
      };

      keywords.forEach(item => {

        obj[item] = shoparize_getFromCookies(item);
      });

      sendAsyncReq(obj, 'conv');
    }else {
    	throw new Error("Please provide shopID");
    }
  }
  return {init: init, conv: conv};
}

/*

window.dataLayer = window.dataLayer || [];
window.dataLayerShoparize = window.dataLayerShoparize || [];
dataLayerShoparize.push({
  event: "purchase",
  ecommerce: {
      transaction_id: "{{ order.order_number }}",
      value: {{ total_price | times: 0.01 }},
      tax: {{ tax_price | times: 0.01 }},
      shipping: {{ shipping_price | times: 0.01 }},
      currency: "{{ order.currency }}",
      items: [
       {% for line_item in line_items %}{
        item_id: "{{ line_item.product_id }}",
        item_name: "{{ line_item.title | remove: "'" | remove: '"' }}",
        currency: "{{ order.currency }}",
        price: {{ line_item.final_price | times: 0.01 }},
        quantity: {{ line_item.quantity }}
      },{% endfor %}
 ]
  }
});
 */
