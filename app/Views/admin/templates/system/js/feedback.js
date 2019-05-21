/*
 feedback.js <http://experiments.hertzen.com/jsfeedback/>
 Copyright (c) 2012 Niklas von Hertzen. All rights reserved.
 http://www.twitter.com/niklasvh

 Released under MIT License
 */
(function( window, document, undefined ) {
	if ( window.Feedback !== undefined ) {
		return;
	}

// log proxy function
	var log = function( msg ) {
			window.console.log( msg );
		},
// function to remove elements, input as arrays
		removeElements = function( remove ) {
			for (var i = 0, len = remove.length; i < len; i++ ) {
				var item = Array.prototype.pop.call( remove );
				if ( item !== undefined ) {
					if (item.parentNode !== null ) { // check that the item was actually added to DOM
						item.parentNode.removeChild( item );
					}
				}
			}
		},
		loader = function() {
			var div = document.createElement("div"), i = 3;
			div.className = "feedback-loader";

			while (i--) { div.appendChild( document.createElement( "span" )); }
			return div;
		},
		getBounds = function( el ) {
			return el.getBoundingClientRect();
		},
		emptyElements = function( el ) {
			var item;
			while( (( item = el.firstChild ) !== null ? el.removeChild( item ) : false) ) {}
		},
		element = function( name, text ) {
			var el = document.createElement( name );
			el.appendChild( document.createTextNode( text ) );
			return el;
		},
// script onload function to provide support for IE as well
		scriptLoader = function( script, func ){

			if (script.onload === undefined) {
				// IE lack of support for script onload

				if( script.onreadystatechange !== undefined ) {

					var intervalFunc = function() {
						if (script.readyState !== "loaded" && script.readyState !== "complete") {
							window.setTimeout( intervalFunc, 250 );
						} else {
							// it is loaded
							func();
						}
					};

					window.setTimeout( intervalFunc, 250 );

				} else {
					log("ERROR: We can't track when script is loaded");
				}

			} else {
				return func;
			}

		},
		nextButton,
		H2C_IGNORE = "data-html2canvas-ignore",
		currentPage,
		modalBody = document.createElement("div");

	window.Feedback = function( options ) {

		options = options || {};

		// default properties
		options.label = options.label || "Send Feedback";
		options.header = options.header || "Send Feedback";
		options.url = options.url || "/";
		options.adapter = options.adapter || new window.Feedback.XHR( options.url );

		options.nextLabel = options.nextLabel || "Continue";
		options.reviewLabel = options.reviewLabel || "Review";
		options.sendLabel = options.sendLabel || "Send";
		options.closeLabel = options.closeLabel || "Close";

		options.messageSuccess = options.messageSuccess || "Your feedback was sent succesfully.";
		options.messageError = options.messageError || "There was an error sending your feedback to the server.";


		if (options.pages === undefined ) {
			options.pages = [
				new window.Feedback.Form(options),
				//new window.Feedback.Screenshot( options ),
				new window.Feedback.Review()
			];
		}

		var button,
			modal,
			currentPage,
			glass = document.createElement("div"),
			returnMethods = {

				// open send feedback modal window
				open: function() {
					var len = options.pages.length;
					currentPage = 0;
					for (; currentPage < len; currentPage++) {
						// create DOM for each page in the wizard
						if ( !(options.pages[ currentPage ] instanceof window.Feedback.Review) ) {
							options.pages[ currentPage ].render();
						}
					}

					var a = element("a", "×"),
						modalHeader = document.createElement("div"),
						// modal container
						modalFooter = document.createElement("div");

					modal = document.createElement("div");
					document.body.appendChild( glass );

					// modal close button
					a.className =  "feedback-close";
					a.onclick = returnMethods.close;
					a.href = "#";

					button.disabled = true;

					// build header element
					modalHeader.appendChild( a );
					modalHeader.appendChild( element("h3", options.header ) );
					modalHeader.className =  "feedback-header";

					modalBody.className = "feedback-body";

					emptyElements( modalBody );
					currentPage = 0;
					modalBody.appendChild( options.pages[ currentPage++ ].dom );


					// Next button
					nextButton = element( "button", options.nextLabel );

					nextButton.className =  "feedback-btn";
					nextButton.onclick = function() {

						if (currentPage > 0 ) {
							if ( options.pages[ currentPage - 1 ].end( modal ) === false ) {
								// page failed validation, cancel onclick
								return;
							}
						}

						emptyElements( modalBody );

						if ( currentPage === len ) {
							returnMethods.send( options.adapter );
						} else {

							options.pages[ currentPage ].start( modal, modalHeader, modalFooter, nextButton );

							if ( options.pages[ currentPage ] instanceof window.Feedback.Review ) {
								// create DOM for review page, based on collected data
								options.pages[ currentPage ].render( options.pages );
							}

							// add page DOM to modal
							modalBody.appendChild( options.pages[ currentPage++ ].dom );

							// if last page, change button label to send
							if ( currentPage === len ) {
								nextButton.firstChild.nodeValue = options.sendLabel;
							}

							// if next page is review page, change button label
							if ( options.pages[ currentPage ] instanceof window.Feedback.Review ) {
								nextButton.firstChild.nodeValue = options.reviewLabel;
							}
						}

					};

					modalFooter.className = "feedback-footer";
					modalFooter.appendChild( nextButton );


					modal.className =  "feedback-modal";
					modal.setAttribute(H2C_IGNORE, true); // don't render in html2canvas


					modal.appendChild( modalHeader );
					modal.appendChild( modalBody );
					modal.appendChild( modalFooter );

					document.body.appendChild( modal );
				},


				// close modal window
				close: function() {

					button.disabled = false;

					// remove feedback elements
					removeElements( [ modal, glass ] );

					// call end event for current page
					if (currentPage > 0 ) {
						options.pages[ currentPage - 1 ].end( modal );
					}

					// call close events for all pages
					for (var i = 0, len = options.pages.length; i < len; i++) {
						options.pages[ i ].close();
					}

					return false;

				},

				// send data
				send: function( adapter ) {

					// make sure send adapter is of right prototype
					if ( !(adapter instanceof window.Feedback.Send) ) {
						throw new Error( "Adapter is not an instance of Feedback.Send" );
					}

					// fetch data from all pages
					for (var i = 0, len = options.pages.length, data = [], p = 0, tmp; i < len; i++) {
						if ( (tmp = options.pages[ i ].data()) !== false ) {
							data[ p++ ] = tmp;
						}
					}

					nextButton.disabled = true;

					emptyElements( modalBody );
					modalBody.appendChild( loader() );

					// send data to adapter for processing
					adapter.send( data, function( success ) {

						emptyElements( modalBody );
						nextButton.disabled = false;

						nextButton.firstChild.nodeValue = options.closeLabel;

						nextButton.onclick = function() {
							returnMethods.close();
							return false;
						};

						if ( success === true ) {
							modalBody.appendChild( document.createTextNode( options.messageSuccess ) );
						} else {
							modalBody.appendChild( document.createTextNode( options.messageError ) );
						}

					} );

				}
			};

		glass.className = "feedback-glass";
		glass.style.pointerEvents = "none";
		glass.setAttribute(H2C_IGNORE, true);

		options = options || {};

		button = element( "button", options.label );
		button.className = "feedback-btn feedback-bottom-right";

		button.setAttribute(H2C_IGNORE, true);

		button.onclick = returnMethods.open;

		if ( options.appendTo !== null ) {
			((options.appendTo !== undefined) ? options.appendTo : document.body).appendChild( button );
		}

		return returnMethods;
	};
	window.Feedback.Page = function() {};
	window.Feedback.Page.prototype = {

		render: function( dom ) {
			this.dom = dom;
		},
		start: function() {},
		close: function() {},
		data: function() {
			// don't collect data from page by default
			return false;
		},
		review: function() {
			return null;
		},
		end: function() { return true; }

	};
	window.Feedback.Send = function() {};
	window.Feedback.Send.prototype = {

		send: function() {}

	};

	window.Feedback.Form = function( options ) {
		this.options = options || {};
		this.elements = [{
			type: "textarea",
			name: "Issue",
			label: "Please describe the issue you are experiencing",
			required: false
		}];

		this.dom = document.createElement("div");
		this.h2cDone = false;
	};

	window.Feedback.Form.prototype = new window.Feedback.Page();

	window.Feedback.Form.prototype.review = function( dom ) {

		var i = 0, item, len = this.elements.length;

		for (; i < len; i++) {
			item = this.elements[ i ];

			if (item.element.value.length > 0) {
				dom.appendChild( element("label", item.name + ":") );
				dom.appendChild( document.createTextNode( item.element.value.length ) );
				dom.appendChild( document.createElement( "hr" ) );
			}

		}

		return dom;

	};
	window.Feedback.Review = function() {

		this.dom = document.createElement("div");
		this.dom.className = "feedback-review";

	};

	window.Feedback.Review.prototype = new window.Feedback.Page();

	window.Feedback.Review.prototype.render = function( pages ) {

		var i = 0, len = pages.length, item;
		emptyElements( this.dom );

		for (; i < len; i++) {

			// get preview DOM items
			pages[ i ].review( this.dom );

		}

		return this;

	};

	window.Feedback.Form.prototype.render = function() {

		var i = 0, len = this.elements.length, item;
		emptyElements( this.dom );
		for (; i < len; i++) {
			item = this.elements[ i ];

			switch( item.type ) {
				case "textarea":
					this.dom.appendChild( element("label", item.label + ":" + (( item.required === true ) ? " *" : "")) );
					this.dom.appendChild( ( item.element = document.createElement("textarea")) );
					break;
			}
		}
		this.dom.append(document.createElement("div"));

		// execute the html2canvas script
		var script,
			$this = this,
			options = this.options,
			runH2c = function(){
				try {

					options.onrendered = options.onrendered || function( canvas ) {
							$this.h2cCanvas = canvas;
							$this.h2cDone = true;
						};

					window.html2canvas([ document.body ], options);

				} catch( e ) {

					$this.h2cDone = true;
					log("Error in html2canvas: " + e.message);
				}
			};

		if ( window.html2canvas === undefined && script === undefined ) {

			// let's load html2canvas library while user is writing message

			script = document.createElement("script");
			script.src = options.h2cPath || "libs/html2canvas.js";
			script.onerror = function() {
				log("Failed to load html2canvas library, check that the path is correctly defined");
			};

			script.onload = (scriptLoader)(script, function() {

				if (window.html2canvas === undefined) {
					log("Loaded html2canvas, but library not found");
					return;
				}

				window.html2canvas.logging = window.Feedback.debug;
				runH2c();


			});

			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(script, s);

		} else {
			// html2canvas already loaded, just run it then
			runH2c();
		}

		return this;
	};

	window.Feedback.Review.prototype.data = function() {
		var feedbackDesc = this.dom.firstChild.outerHTML;
		return ( this._data = feedbackDesc ) ;
	};

	window.Feedback.Form.prototype.data = function() {

		if ( this.h2cCanvas !== undefined ) {

			var ctx = this.h2cCanvas.getContext("2d"),
				canvasCopy,
				copyCtx,
				radius = 5;
			ctx.fillStyle = "#000";

			// draw blackouts
			Array.prototype.slice.call( document.getElementsByClassName('feedback-blackedout'), 0).forEach( function( item ) {
				var bounds = getBounds( item );
				ctx.fillRect( bounds.left, bounds.top, bounds.width, bounds.height );
			});

			// draw highlights
			var items = Array.prototype.slice.call( document.getElementsByClassName('feedback-highlighted'), 0);

			if (items.length > 0 ) {

				// copy canvas
				canvasCopy = document.createElement( "canvas" );
				copyCtx = canvasCopy.getContext('2d');
				canvasCopy.width = this.h2cCanvas.width;
				canvasCopy.height = this.h2cCanvas.height;

				copyCtx.drawImage( this.h2cCanvas, 0, 0 );

				ctx.fillStyle = "#777";
				ctx.globalAlpha = 0.5;
				ctx.fillRect( 0, 0, this.h2cCanvas.width, this.h2cCanvas.height );

				ctx.beginPath();

				items.forEach( function( item ) {

					var x = parseInt(item.style.left, 10),
						y = parseInt(item.style.top, 10),
						width = parseInt(item.style.width, 10),
						height = parseInt(item.style.height, 10);

					ctx.moveTo(x + radius, y);
					ctx.lineTo(x + width - radius, y);
					ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
					ctx.lineTo(x + width, y + height - radius);
					ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
					ctx.lineTo(x + radius, y + height);
					ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
					ctx.lineTo(x, y + radius);
					ctx.quadraticCurveTo(x, y, x + radius, y);

				});
				ctx.closePath();
				ctx.clip();

				ctx.globalAlpha = 1;

				ctx.drawImage(canvasCopy, 0,0);

			}

			// to avoid security error break for tainted canvas
			try {
				// cache and return data
				return ( this._data = this.h2cCanvas.toDataURL() );
			} catch( e ) {}

		}
	};


	window.Feedback.Form.prototype.review = function( dom ) {

		var data = this.data();
		if ( data !== undefined ) {
			var img = new Image();
			img.src = data;
			img.style.width = "100%";
			data = this.elements[ 0 ].element.value;
			data = data.replace(/\r?\n/g, '<br />');
			var descNode = document.createElement('p');
			descNode.innerHTML = data;
			dom.appendChild( descNode );
			dom.appendChild( img );
		}

	};
	window.Feedback.XHR = function( url ) {

		this.xhr = new XMLHttpRequest(), token = document.querySelector('meta[name="csrf-token"]').content;
		this.url = url;

	};

	window.Feedback.XHR.prototype = new window.Feedback.Send();

	window.Feedback.XHR.prototype.send = function( data, callback ) {

		var xhr = this.xhr;

		xhr.onreadystatechange = function() {
			if( xhr.readyState == 4 ){
				callback( (xhr.status === 200) );
			}
		};

		xhr.open( "POST", this.url, true);
		xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
		xhr.setRequestHeader('X-CSRF-TOKEN', token);
		xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xhr.send( "data=" + encodeURIComponent( window.JSON.stringify( data ) ) );

	};
})( window, document );

