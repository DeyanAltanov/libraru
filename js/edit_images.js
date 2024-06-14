const images = document.querySelector('.model-images');
const elements = Array.from(images.children);
const leftmostarrow = document.querySelector('#left-btn0');
const rightmostarrow = document.querySelector("#right-btn" + (elements.length - 1));

window.onload = function() {
    leftmostarrow.style.display = "none";
    rightmostarrow.style.display = "none";
};