// assets/js/app.js
require('../css/app.css');
require('../css/bootstrap/css/bootstrap.min.css');
require('../css/bootstrap/js/bootstrap.bundle.js');
require('../css/fontawesome/css/font-awesome.css');
// loads the jquery package from node_modules
const $ = require('jquery');

// import the function from greet.js (the .js extension is optional)
// ./ (or ../) means to look for a local file
var greet = require('./greet');

$(document).ready(function() {
    $('body').prepend('<h1>'+greet('john')+'</h1>');
});

const imagesContext = require.context('../img', true, /\.(png|jpg|jpeg|gif|ico|svg|webp)$/);
imagesContext.keys().forEach(imagesContext);