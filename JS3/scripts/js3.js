hee(200);



function hee(n){
    var list = document.createElement('ul');
    arr = [];
for(let i=1;i<=n;i++){
    if(i % 3  === 0 && i % 5 === 0){
        arr.push("Hee Haw!")}
    else if(i % 3 === 0){
        arr.push("Hee!");
    }
    else if(i % 5 === 0){
        arr.push("Haw!");
    }
    else
        arr.push(i);
}
    arr.forEach(function (arr) {
        var li = document.createElement('li');
        li.textContent = arr;
        list.appendChild(li);
    });
    var app = document.querySelector('#app');
    app.appendChild(list);
}


