<!-- Footer  style='background-color:#e8fafc!important;position:absolute;bottom:0!important;width:100%'-->
  <footer class="w3-container w3-padding-16 w3-light-grey">
    <h4 style='display:inline-block;'> © 2019 한동대학교 <a href="http://csee.handong.edu" style='text-decoration: none;' target="_blank">전산전자공학부</a></h4>
  </footer>
  <!-- End page content -->
</div>

<script>
// Get the Sidebar
var mySidebar = document.getElementById("mySidebar");
// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");
// Toggle between showing and hiding the sidebar, and add overlay effect
function w3_open() {
  if (mySidebar.style.display === 'block') {
    mySidebar.style.display = 'none';
    overlayBg.style.display = "none";
  } else {
    mySidebar.style.display = 'block';
    overlayBg.style.display = "block";
  }
}
// Close the sidebar with the close button
function w3_close() {
  mySidebar.style.display = "none";
  overlayBg.style.display = "none";
}
</script>
<!--로딩이 끝났을 때 init이라는 함수 호출(platform.js안에 구글api 정의되어있음) -->
<script src="https://apis.google.com/js/platform.js?onload=init" async defer></script>
</body>
</html>
