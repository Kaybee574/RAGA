// THis code is responsible for the changing pictures in the backdrop of the main page
const images = [
    "WelcomePictures/p1.jpg",
    "WelcomePictures/p2.jpg",
    "WelcomePictures/p3.jpg",
    "WelcomePictures/p4.jpg",
    "WelcomePictures/p5.jpg",
    "WelcomePictures/p6.jpg",
    "WelcomePictures/p7.jpg",
    "WelcomePictures/p8.jpg",
    "WelcomePictures/hello2.jpg",
];

// THis proovides some "dynamicity" for the front page, it displays welcome pictures for different users by simply using random
//let index = 0;
let index = Math.floor(Math.random() * 9) + 1;

function changeBackground() 
{
    document.body.style.backgroundImage = `url(${images[index]})`;
    index++;

    if (index >= images.length) 
        {
        index = 0;
    }
}

setInterval(changeBackground, 5000); // changes every 5 seconds

changeBackground(); // load first image immediately