<!DOCTYPE html>
<html>

<head>
	<title>BusStop SPA</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<link rel="stylesheet" type="text/css" href="./assets/css/style.css">
	<style type="text/css">
		#ui-id-1 {
			overflow-y: scroll;
			max-height: 35%;
			height: auto;
			overflow-x: hidden;
		}
		#ui-id-2 {
			overflow-y: scroll;
			max-height: calc(35% - 50px);
			height: auto;
			overflow-x: hidden;
		}
		.dropdown-container-1 {
			display: flex;
			flex-direction: row;
			justify-content: space-evenly;
		}
		.dropdown-container-1 button {
			margin-left: 50px;
		}
		.dropdown-container-2 {
			display: flex;
			flex-direction: row;
			justify-content: space-evenly;
		}
		.dropdown-container-2 button {
			margin-left: 50px;
		}
		input:focus-visible {
			outline: none;
		}
		.buses-container {

		}
		.left-container .selections {
			overflow-y: auto;
			overflow-x: hidden;
		}
		.bus-label {
			margin-top: 10px;
			font-size: 14pt;
			font-family: serif;
			font-weight: bold;
		}

	</style>
</head>

<body>
	<div class="container">
		<div class="row main-container">
			<div class="col-sm-12 text-center header-container">
				<p class="h1 text-success">
					Bus Stop Single Page Website
				</p>
			</div>
			<div class="left-container col-sm-6 text-center ">
				<div class="col-12 h-50 border border-primary ">
					
				</div>
				<div class="selections col-12 h-50 border border-primary">
					<div>
						<div class="dropdown-container-1 pt-2">
							<div class="ui-widget">
								<select id="area_drowdown" >
								</select>
							</div>
							<button type="button" class="btn btn-primary" onclick = "handleArea()">Send</button>
						</div>

						<div class="dropdown-container-2 pt-2">
							<div class="ui-widget">
								<select id="station_drowdown">
								</select>
							</div>
							<button type="button" class="btn btn-primary" onclick = "handleStation()">Send</button>
						</div>
					</div>
					<div class="bus-label">
						Bus list
					</div>
					<div class="buses-container px-2 py-1" id="buses_list">
					</div>
				</div>

			</div>
			<div class="right-container col-sm-6 text-center border border-success pt-3">
				<button onclick="clearAll()" class="btn btn-success">Clear All</button>
			</div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script type="text/javascript" src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script type="text/javascript">
		$(function() {
			$.widget("custom.combobox", {
				_create: function() {
					this.wrapper = $("<span>")
						.addClass("custom-combobox")
						.insertAfter(this.element);
					this.element.hide();
					this._createAutocomplete();
					this._createShowAllButton();
				},

				_createAutocomplete: function() {
					console.log()
					var selected = this.element.children(":selected"),
						value = selected.val() ? selected.text() : "";

					this.input = $("<input>")
						.appendTo(this.wrapper)
						.val(value)
						.attr("title", "")
						.attr("placeholder", "Select one...")
						.attr('id', this.element.context.id+'_combo')
						.addClass("custom-combobox-input w-60 ui-widget ui-widget-content ui-state-default ui-corner-left")
						.autocomplete({
							delay: 0,
							minLength: 0,
							source: $.proxy(this, "_source")
						})
						.tooltip({
							classes: {
								"ui-tooltip": "ui-state-highlight"
							}
						});

					this._on(this.input, {
						autocompleteselect: function(event, ui) {
							if(this.element.context.id==='area_drowdown'){
								$("#station_drowdown_combo").val('');
								$("#station_drowdown").html('');
							}
							else if(this.element.context.id==='station_drowdown') {
								// handleStation(ui.item.value);
							}
							
							
							ui.item.option.selected = true;
							this._trigger("select", event, {
								item: ui.item.option
							});
						},

						autocompletechange: "_removeIfInvalid"
					});
				},

				_createShowAllButton: function() {
					var input = this.input,
						wasOpen = false;

					$("<a>")
						.attr("tabIndex", -1)
						.attr("title", "Show All Items")
						.tooltip()
						.appendTo(this.wrapper)
						.button({
							icons: {
								primary: "ui-icon-triangle-1-s"
							},
							text: false
						})
						.removeClass("ui-corner-all")
						.addClass("custom-combobox-toggle ui-corner-right")
						.on("mousedown", function() {
							wasOpen = input.autocomplete("widget").is(":visible");
						})
						.on("click", function(e) {
							input.trigger("focus");

							// Close if already visible
							if (wasOpen) {
								return;
							}

							// Pass empty string as value to search for, displaying all results
							input.autocomplete("search", "");
						});
				},

				_source: function(request, response) {
					var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
					response(this.element.children("option").map(function() {
						var text = $(this).text();
						if (this.value && (!request.term || matcher.test(text)))
							return {
								label: text,
								value: text,
								option: this
							};
					}));
				},

				_removeIfInvalid: function(event, ui) {

					// Selected an item, nothing to do
					if (ui.item) {
						return;
					}

					// Search for a match (case-insensitive)
					var value = this.input.val(),
						valueLowerCase = value.toLowerCase(),
						valid = false;
					this.element.children("option").each(function() {
						if ($(this).text().toLowerCase() === valueLowerCase) {
							this.selected = valid = true;
							return false;
						}
					});

					// Found a match, nothing to do
					if (valid) {
						return;
					}

					// Remove invalid value
					this.input
						.val("")
						.attr("title", value + " didn't match any item")
						.tooltip("open");
					this.element.val("");
					this._delay(function() {
						this.input.tooltip("close").attr("title", "");
					}, 2500);
					this.input.autocomplete("instance").term = "";
				},

				_destroy: function() {
					this.wrapper.remove();
					this.element.show();
				}
			});

			$("#area_drowdown").combobox();
			$("#station_drowdown").combobox();

		});
		getAreaList();
		getLocation();
		function getLocation() {
		  if (navigator.geolocation) {
		    navigator.geolocation.getCurrentPosition(showNearBy);
		  } else {
		    console.log("Geolocation is not supported by this browser.");
		  }
		}

		function getAreaList() {
			$.ajax({
			  	url: "/function.php?id=getarea",
			  	type: 'post',
			  	dataType: 'json',
			  	success: function (res) {
			  		optionData = "";
			  		for(i=0; i<res.length; i++) {
			  			if(i===0){
			  				optionData += '<option value="'+res[i].stop_area+'" selected>'+res[i].stop_area+'</option>';
			  			}
			  			else optionData += '<option value="'+res[i].stop_area+'">'+res[i].stop_area+'</option>';
			  		}
			  		$("#area_drowdown").html(optionData);
			  	}
			});
		}

		function showNearBy(position) {
		    $.ajax({
			  	url: "/function.php?id=getnearby",
			  	type: 'post',
			  	dataType: 'json',
			  	data: {
			  		// lat: position.coords.latitude,
			  		// lon:position.coords.longitude,
			  		lat: 59.42,
			  		lon: 24.62,
			  		dis: 10
			  	},
			  	success: function (res) {
			  		console.log(res)
			  	}
		    });
		}
		function handleArea () {
			var area = $("#area_drowdown_combo").val();
			console.log(area)
			$("#station_drowdown_combo").val('');
			$.ajax({
			  	url: "/function.php?id=getstation",
			  	type: 'post',
			  	dataType: 'json',
			  	data: {
			  		area
			  	},
			  	success: function (res) {
			  		optionData = "";
			  		for(i=0; i<res.length; i++) {
			  			if(i===0){
			  				optionData += '<option value="'+res[i].stop_name+'" selected>'+res[i].stop_name+'</option>';
			  			}
			  			else optionData += '<option value="'+res[i].stop_name+'">'+res[i].stop_name+'</option>';
			  		}
			  		$("#station_drowdown").html(optionData);
			  	}
		    });
		}

		function handleStation () {
			var station = $("#station_drowdown_combo").val();
			$.ajax({
			  	url: "/function.php?id=getbuslist",
			  	type: 'post',
			  	dataType: 'json',
			  	data: {
			  		station
			  	},
			  	success: function (res) {
			  		optionData = "";
			  		for(i=0; i<res.length; i++) {
			  			optionData += '<button class="btn btn-primary badge px-2 me-1 my-1" onclick="handleBus(\''+ res[i].route_id+'\', \''+res[i].route_short_name+'\')">'+res[i].route_short_name+'</button>';
			  		}
			  		$("#buses_list").html(optionData);
			  	},
			  	error: function(err) {
			  		console.log(err)
			  	}
		    });
		}

		function clearAll () {
			$("#station_drowdown_combo").val('');
			$("#area_drowdown_combo").val('');
			$("#station_drowdown").html('');
			$("#buses_list").html('');
		}
		function handleBus(route_id, bus_name) {
			console.log(route_id, bus_name)
		}
	</script>
</body>

</html>