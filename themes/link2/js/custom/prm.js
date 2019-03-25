// grab an element
var stickyNav = document.querySelector("#location-sticky-nav");
// construct an instance of Headroom, passing the element
if (stickyNav !== null) {
  var headroom  = new Headroom(stickyNav);
  headroom.offset = 100;
  headroom.init();
}
