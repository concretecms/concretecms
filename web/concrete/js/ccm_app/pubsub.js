var ccm_event = function(h, b) {
  "use strict"
  /**
   * concrete5 Event Managment
   * -------------------------
   *
   * The event management paradigm that is used here is a subscription to the
   * main object to attach to the correct dialog.
   *
   * Events that go along with created elements should define the element type
   * and pass the element dom object.
   *
   * @author Korvin Szanto <Korvin@concrete5.org>
   */
  ccm_event = function() {
    var e = b.createElement("span"), t = this;
    this.sub = function(c, b, a) {
      a = a || e;
      a.addEventListener ? a.addEventListener(c.toLowerCase(), b, 0) : a.attachEvent("on" + c.toLowerCase(), b)
    };
    this.pub = function(c, g, a) {
      var d = a || e, f = c.toLowerCase();
      b.createEvent ? (a = b.createEvent("HTMLEvents"), a.initEvent(f, 1, 1)) : (a = b.createEventObject(), a.eventType = f);
      a.eventName = "CCMEvent";
      a.eventData = g || {};
      b.createEvent ? d.dispatchEvent(a) : d.fireEvent("on" + a.eventType, a);
      if("function" === typeof d["on" + c.toLowerCase()]) {
        d["on" + c.toLowerCase()](a)
      }
    };
    t.subscribe = t.bind = t.watch = t.on = t.sub;
    t.publish = t.fire = t.trigger = t.do = t.pub;
    return t
  };
  return new ccm_event
}(window, document);
window.ccm_subscribe = ccm_event.sub;
window.ccm_publish = ccm_event.pub;

/**
// Minimal example:
//Binding:
ccm_event.bind('someEvent',function(e){alert('Caught event with data: '+e.eventData.info)});

//Firing:
ccm_event.fire('someEvent',{info:'Some Data'});


// Sample subscription.
ccm_subscribe('dialogOpened',function(event){
  data = event.eventData;

  if (data.type == "add_block_dialog") {
    ccm_subscribe('close',function(event){
      console.log('Closed Dialog.');
    }, data.element);
  }
});

// Sample publish
function launchAddDialog(){
  var element = $('<div><span>Add Dialog</span></div>');
  $.fn.dialog.open({
    element:element,
    onClose:function(){
      ccm_publish('close',{},data.element);
    }
  });
  var data = {};
  data.element = element.get(0);
  data.type = 'add_block_dialog';
  ccm_publish('dialogOpened',data);
}
*/
