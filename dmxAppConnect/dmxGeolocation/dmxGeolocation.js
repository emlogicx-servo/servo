/*!
 App Connect Geolocation
 Version: 2.0.1
 (c) 2024 Wappler.io
 @build 2024-04-25 11:18:00
 */
dmx.Component("geolocation",{initialData:{support:!!navigator.geolocation,status:navigator.geolocation?"Geolocation is not supported by this browser.":"Locating...",timestamp:null,coords:null},attributes:{tracking:{type:Boolean,default:!1},enableHighAccuracy:{type:Boolean,default:!1},maxAge:{type:Number,default:3e4},timeout:{type:Number,default:27e3}},methods:{getCurrentPosition(){this._getCurrentPosition()},watchPosition(){this._watchPosition()},clearWatch(){this._clearWatch()}},events:{success:Event,error:Event,permissiondenied:Event,unavailable:Event,timeout:Event},errorCodes:{1:"permissiondenied",2:"unavailable",3:"timeout"},render:!1,init(t){this._successHandler=this._successHandler.bind(this),this._errorHandler=this._errorHandler.bind(this),this.props.tracking?this._watchPosition():this._getCurrentPosition()},performUpdate(t){this.data.supprt&&t.has("tracking")&&(this.props.tracking?this._watchPosition():this._clearWatch())},_getCurrentPosition(){this.data.support&&navigator.geolocation.getCurrentPosition(this._successHandler,this._errorHandler,{enableHighAccuracy:this.props.enableHighAccuracy,timeout:this.props.timeout,maximumAge:this.props.maxAge})},_watchPosition(){this.data.support&&!this._watching&&(this._watching=navigator.geolocation.watchPosition(this._successHandler,this._errorHandler,{enableHighAccuracy:this.props.enableHighAccuracy,timeout:this.props.timeout,maximumAge:this.props.maxAge}))},_clearWatch(){this.data.support&&this._watching&&(navigator.geolocation.clearWatch(this._watching),delete this._watching)},_successHandler(t){this.set("status","OK"),this.set("timestamp",t.timestamp),this.set("coords",{latitude:t.coords.latitude,longitude:t.coords.longitude,altitude:t.coords.altitude,accuracy:t.coords.accuracy,altitudeAccuracy:t.coords.altitudeAccuracy,heading:t.coords.heading,speed:t.coords.speed}),dmx.nextTick((()=>this.dispatchEvent("success")))},_errorHandler(t){this.set("status",t.message||this.errorCodes[t.code]||"Unknown Error."),dmx.nextTick((()=>this.dispatchEvent(this.errorCodes[t.code]||"error")))}});
//# sourceMappingURL=dmxGeolocation.js.map
