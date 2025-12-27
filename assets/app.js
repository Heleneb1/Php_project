
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
// assets/app.js
import './styles/global.scss';
import './bootstrap.js';

// app.js

const $ = require('jquery');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
//require('bootstrap');

// or you can include specific pieces
// require('bootstrap/js/dist/tooltip');
require('bootstrap/js/dist/popover');

$(document).ready(function () {
    $('[data-toggle="popover"]').popover();
});

document.getElementById("watchlist").addEventListener("click", addWatchlist);

function addWatchlist(e) {
    e.preventDefault();

    const watchlistLink = e.currentTarget;
    const link = watchlistLink.href;

    fetch(link)
        .then(res => res.json())
        .then(data => {
            const watchlistIcon = watchlistLink.firstElementChild;
            if (data.isInWatchlist) {
                watchlistIcon.classList.remove("bi-heart");
                watchlistIcon.classList.add("bi-heart-fill");
            } else {
                watchlistIcon.classList.remove("bi-heart-fill");
                watchlistIcon.classList.add("bi-heart");
            }
        })
        .catch(err => {
            console.error("An error occurred:", err);
        });

} console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
