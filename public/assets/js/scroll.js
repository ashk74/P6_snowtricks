//Get the button
let mybutton = document.getElementById("btn-back-to-top");
let buttonIcon = document.getElementById("scroll-icon");


// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollFunction();
};

function backToTop() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}

function backToBottom() {
    let navbarHeight = document.getElementById("navbar-top").scrollHeight;
    let headerHeight = document.getElementById("index-header").scrollHeight;
    let height = navbarHeight + headerHeight;
    document.body.scrollTop = height;
    document.documentElement.scrollTop = height;
}

function scrollFunction() {
    let navbarHeight = document.getElementById("navbar-top").scrollHeight;
    let headerHeight = document.getElementById("index-header").scrollHeight;
    let height = navbarHeight + headerHeight - 10;

    if (
        document.body.scrollTop > height ||
        document.documentElement.scrollTop > height
    ) {
        mybutton.style.display = "block";
        buttonIcon.classList.replace("fa-arrow-down", "fa-arrow-up");
        mybutton.addEventListener("click", backToTop);
    } else {
        mybutton.style.display = "block";
        buttonIcon.classList.replace("fa-arrow-up", "fa-arrow-down");
        mybutton.addEventListener("click", backToBottom);
    }
}
