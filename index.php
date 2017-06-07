<!doctype html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta charset="utf-8">
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <script type="text/javascript" src="js/jquery-3.2.0.min.js"></script>
        <script type="text/javascript" src="js/bootstrap.min.js"></script>
	    <link rel="stylesheet" href="css/style.css">
	    <title>Location :: Track</title>
	</head>
	<script type="text/javascript">
	  $(document).ready(function(){
		if (localStorage.getItem("info") === null) {
			$('#userinfo').show();		
		}else{		
			$('#userinfo').hide();
		  	geoFindMe();
		  	track();
		}
	  	$('#proceed').on('click',function(){
	  		alert("entred");
	 		$.ajax({
	          type: "POST",
	          url: "userinfo_cont.php",
	          data: $('form').serialize(),
	          success: function(results){ 
	          	var name=$('.name').val();
	          	var device=$('.agent').val();
	          	var info=[name,results,device];
	          	var stringify=JSON.stringify(info);
	          	localStorage.setItem("info",stringify);
	            if (results!=0){	              
	              $("#result").html("Registered Sucessfully");
	              $('#result')[0].scrollIntoView(true);
	              $("#result").addClass("alert alert-success");	              
	              $('#userinfo').hide(); 
          	  		geoFindMe();
          	  		track();
	            }else{
	              $('#error').html("Registration failed");
	              $('#error')[0].scrollIntoView(true);
	              $('#error').addClass("alert alert-danger");           
	            }
	          },      
	          error: function(XMLHttpRequest, textStatus, errorThrown) {            
	              $('.result').text(textStatus,errorThrown);
	              $('#error').html(textStatus,errorThrown);
	              $('#error').addClass("alert alert-danger");
	            }         
			}); 
	  	});        
	  	var userAgent=navigator.userAgent;	  	
	  	$('.agent').val(userAgent);

	  });

  		function geoFindMe() {
		  var output = document.getElementById("out");
		  if (!navigator.geolocation){
		    output.innerHTML = "<p>Geolocation is not supported by your browser</p>";
		    return;
		  }
		  
		  function success(position) {
		    var latitude  = position.coords.latitude;
		    var longitude = position.coords.longitude;

		    output.innerHTML ='<p class='+"lat"+'>Latitude is :' + latitude + '°</p><p class='+"long"+'>Longitude is:' + longitude + '°</p>';

		    var img = new Image();
		    img.src = "https://maps.googleapis.com/maps/api/staticmap?center=" + latitude + "," + longitude + "&zoom=13&size=300x300&sensor=false";

		    output.appendChild(img);
		  }

		  function error() {
		    output.innerHTML = "Unable to retrieve your location";
		  }

		  output.innerHTML = "<p>Locating…</p>";

		  navigator.geolocation.getCurrentPosition(success, error);
		}
		function track(){
			navigator.geolocation.watchPosition(function(position) {
			document.getElementById('value').innerHTML = '<p data-id='+position.coords.latitude+'  class='+"cLat"+'>Current Latitude:'+position.coords.latitude+'°</p><p  data-id='+position.coords.longitude+' class='+"cLon"+'>Current Longitude:'+position.coords.longitude+ '°</p>';
			save();
			});
		}
		function save(){
			var retrive= localStorage.getItem("info");
			var data=JSON.parse(retrive);
			var userid= data[1];
			var clatitude=$('.cLat').data("id");
			console.log(clatitude);
			var clongitute=$('.cLon').data("id");
	 		$.ajax({
	          type: "POST",
	          url: "track.php",
	          data: {
	          	"clat":clatitude,
	          	"clon":clongitute,
	          	"userid":userid
	          },
	          success: function(results){ 
	          	if (results==1) {
	            }else{
	              $('#error').html("Registration failed");
	              $('#error')[0].scrollIntoView(true);
	              $('#error').addClass("alert alert-danger");           
	            }
	          },      
	          error: function(XMLHttpRequest, textStatus, errorThrown) {            
	              $('.result').text(textStatus,errorThrown);
	              $('#error').html(textStatus,errorThrown);
	              $('#error').addClass("alert alert-danger");
	            }         
			}); 
		}
	</script>
	<body>
		<div class="warpper">
			<form id="userinfo">
         	    <div>
                  <span id="result" class="col-md-12 col-sm-12"></span>
                  <span id="error" class="col-md-12 col-sm-12"></span>
                </div> 
				<div class="info col-md-12">
					<div class=" grp col-md-12">
						<label class="col-md-12 col-sm-12">Name:</label>
						<div class="col-md-12 col-sm-12"><input class="col-md-12 col-sm-12 name" type="text" name="name"></div>
					</div>
					<div class=" grp col-md-12">
						<label class="col-md-12 col-sm-12">Device Type:</label>
						<div class="col-md-12 col-sm-12"><input class="col-md-12 col-sm-12 agent" type="text" name="device" readonly="readonly"></div>
					</div>
					<div class="grp submit col-md-12 col-sm-12">
						<div class="col-md-12 col-sm-12"><button id="proceed" class="btn btn-success" type="button">Submit</button></div>
					</div>
				</div>
			</form>
			<div class="col-md-12 col-sm-12">
				<div class="col-md-6 col-sm-6" id="out"></div>
				<div  class="col-md-6 col-sm-6" id="value"></div>
			</div>
			
		</div>
	</body>
</html>
